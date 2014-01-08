<?php
/**
 * The inspector base class
 * @todo Implement MODx logging
 * @package Campaigner
 * @author Patrick Stummer <info@patrickstummer.com>
 */
class Inspector
{

    public $modx;
    /**
     * Constructor.
     *
     * @param modX &$modx A reference to the modX object
     * @param array $config An array of configuration options
     */
    function __construct(modX &$modx,array $config = array())
    {
        $this->modx =& $modx;
        $this->modx->setOption('cultureKey', 'en');

        // PHPMailer
        require_once $this->modx->getOption('core_path') .'/model/modx/mail/modphpmailer.class.php';

        $basePath = $this->modx->getOption('campaigner.core_path',$config,$this->modx->getOption('core_path').'components/campaigner/');
        $assetsPath = $this->modx->getOption('campaigner.assets_path',$config,$this->modx->getOption('assets_path').'components/campaigner/');
        $assetsUrl = $this->modx->getOption('campaigner.assets_url',$config,$this->modx->getOption('assets_url').'components/campaigner/');
        $action = $this->modx->getObject('modAction', array('namespace' => 'campaigner'));
        $this->config = array_merge(array(
            'basePath'       => $basePath,
            'assetsPath'     => $assetsPath,
            'corePath'       => $basePath,
            'modelPath'      => $basePath.'model/',
            'processorsPath' => $basePath.'processors/',
            'chunksPath'     => $basePath.'elements/chunks/',
            'jsUrl'          => $assetsUrl.'js/',
            'baseUrl'        => $assetsUrl,
            'cssUrl'         => $assetsUrl.'css/',
            'assetsUrl'      => $assetsUrl,
            'connectorUrl'   => $assetsUrl.'connector.php',
            'actionId'       => $action->get('id'),
            ),$config);
        // package and lexicon
        $this->modx->addPackage('campaigner', $this->config['modelPath'], 'camp_');
        $this->modx->lexicon->load('campaigner:inspector');
        $this->smarty = $this->modx->getService('smarty','smarty.modSmarty');
    }

    /**
     * Initializes the class into the proper context
     *
     * @access public
     * @param string $ctx
     */
    public function initialize($ctx = 'web')
    {
        switch ($ctx) {
            case 'mgr':
            if (!$this->modx->loadClass('campaigner.request.campaignerControllerRequest',$this->config['modelPath'],true,true)) {
                return 'Could not load controller request handler.';
            }
            $this->request = new campaignerControllerRequest($this);
            return $this->request->handleRequest();
            break;

            default: break;
        }
        return true;
    }

    /**
     * Runs the inspector
     * @param  array  $options Optional configs
     * @return boolean          True on succesful email, FALSE when no report was found
     * 
     * $options params
     * * recipients: comma separated list of receivers
     * * debug: enabled this will only output the results, not mail them
     * * limit: how many newsletters should be searched for reportable items
     * * mode: which mode should be used. 'strict' is the only option
     * * message: if you want to send additional text with the mail
     * * reports: a simple array of reports, available are: newsletter, overdue, failed, unsubs
     */
    public function run($options = array()) {
        $this->options = $options;
        $this->smarty->assign('title', $this->modx->lexicon('campaigner.inspector.subject') . date('d.m.Y H:i'));
        $this->smarty->assign('message', $this->options['message']);
        $this->smarty->assign('introduction', $this->modx->lexicon('campaigner.inspector.introduction', array('site_name' => $this->modx->getOption('site_name'))));
        $this->smarty->assign('site_name', $this->modx->getOption('site_name'));
        $this->smarty->assign('site_url', $this->modx->getOption('site_url'));
        $o[] = $this->smarty->fetch($this->config['corePath'] . 'elements/smarty/inspector/header.tpl');

        $sum_reports = count($options['reports']);
        foreach($options['reports'] as $report) {
            $fn = 'report' . ucfirst($report);
            $$report = $this->$fn();
            if(!$$report)
                continue;
            $sum_reports--;
            $o[] = $this->generateOutput($$report);
        }
        // If there's no useful results at all there's no need to send anything
        if($sum_reports == count($options['reports']))
            return false;

        $o[] = $this->smarty->fetch($this->config['corePath'] . 'elements/smarty/inspector/footer.tpl');
        
        $options = array(
            'overrule'  => true,
            'recipients'=> $this->options['recipients'],
            'subject'   => $this->modx->lexicon('campaigner.inspector.subject') . date('d.m.Y H:i'),
            'body'      => array(
                'html'  => implode('', $o),
                'text'  => implode('', $o),
                ),
            );

        $debug = $this->options['debug'];
        if($debug) {
            $content = $options['body']['html'];
            unset($options['body']);
            var_dump($options);
            echo $content;
            return;
        }
        $this->modx->campaigner->sendSystemMail($options);
    }

    public function reportNewsletter()
    {
        // Get the latest newsletter sent
        $c = $this->modx->newQuery('Newsletter');
        $c->limit($this->options['limit']);
        $c->sortby('sent_date', 'DESC');
        $c->where(array('state' => 1));
        $c->where(array('sent_date:IS NOT' => NULL));
        if($this->options['mode'] == 'strict')
            $c->where('`Newsletter`.`sent` < `Newsletter`.`total`');
        // $c->prepare();
        // echo $c->toSQL();
        // die();
        $items = $this->modx->getCollection('Newsletter', $c);
        if(empty($items) || count($items) == 0)
            return false;

        $ids = array();
        // Iterate newsletters
        foreach($items as $item) {
            // Iterate queue items for timing data
            $c = $this->modx->newQuery('Queue');
            $c->where(array('newsletter' => $item->get('id')));
            $c->select($this->modx->getSelectColumns('Queue', 'Queue', '', array('id', 'properties')));
            $qt = $this->modx->getCollection('Queue', $c);
            $process_time = 0;
            $create_time = 0;
            foreach ($qt as $key => $value) {
                $props = unserialize($value->get('properties'));
                $process_time = $process_time + $props['processed'];
                $create_time = $create_time + $props['created'];
            }
            $item->set('process_time', $process_time);
            $item->set('create_time', $create_time);
            $ids[] = $item->get('id');
            $list[] = $item->toArray('', false, true);
        }
        $this->config['newsletter_ids'] = $ids;
        return array('type' => 'newsletter', 'tab' => 'newsletter', 'data' => $list, 'count' => count($list));
    }

    /**
     * Report fail mails
     * Fetch a list of failed mails
     * @return mixed False (no failed) or array of subscriber with queue-data
     */
    public function reportFailed()
    {
        $failed = array(6);
        $c = $this->modx->newQuery('Queue');
        $c->where(array(
            'state:IN' => $failed,
            'newsletter:IN' => $this->config['newsletter_ids']
            )
        );
        $c->limit($this->options['limit']);

        // Get the count of fails
        $fail_count = $this->modx->getCount('Queue', $c);
        // No fail mails at all - Positive
        if($fail_count <= 0)
            return false;
        // Grab the failed mails with subscriber information
        $c->leftJoin('Subscriber', 'Subscriber');
        $c->select($this->modx->getSelectColumns('Subscriber', 'Subscriber', '', array('id', 'email')));
        $c->select($this->modx->getSelectColumns('Queue', 'Queue', '', array('id', 'state', 'newsletter', 'error')));
        // $c->prepare();
        // echo $c->toSQL();
        $items = $this->modx->getCollection('Queue', $c);
        foreach($items as $item) {
            $list[] = $item->toArray('', false, true);
        }
        return array('type' => 'failed', 'tab' => 'queue', 'data' => $list, 'count' => count($list));
    }

    /**
     * Report overdue mails
     * Fetch a list of mails should have been delivered already but weren't
     * @return [type] [description]
     */
    public function reportOverdue()
    {
        $ov_states = array(0,3);
        $c = $this->modx->newQuery('Queue');
        $c->where(array(
            'state:IN' => $ov_states,
            'created:<=' => time() - 3600
        ));
        $c->limit($this->options['limit']);
        $c->leftJoin('Subscriber', 'Subscriber');
        $c->leftJoin('Newsletter', 'Newsletter');
        $c->select($this->modx->getSelectColumns('Newsletter', 'Newsletter', '', array('id', 'sent_date')));
        $c->select($this->modx->getSelectColumns('Subscriber', 'Subscriber', '', array('id', 'email')));
        $c->select($this->modx->getSelectColumns('Queue', 'Queue', '', array('id', 'state', 'newsletter', 'error', 'created')));
        // $c->prepare();
        // echo $c->toSQL();
        $items = $this->modx->getCollection('Queue', $c);
        if(empty($items) || count($items) == 0)
            return false;
        foreach($items as $item) {
            $item->set('overdue', $this->_time2str($item->get('sent_date')));
            $list[] = $item->toArray('', false, true);
        }
        // var_dump($list);
        // die();
        return array('type' => 'overdue', 'tab' => 'queue', 'data' => $list, 'count' => count($list));
    }

    /**
     * Report bounces
     * Fetches all new bounces fetched by the system
     * @return [type] [description]
     */
    public function reportBounces()
    {
        # code...
    }

    /**
     * Report unsubscribers
     * Fetches all new unsubscriptions
     * @return [type] [description]
     */
    public function reportUnsubs()
    {
        $unsubs = array(6);
        $c = $this->modx->newQuery('Unsubscriber');
        $c->where(array(
            'newsletter' => $this->config['newsletter_ids']
            )
        );
        $c->limit($this->options['limit']);
        
        // Get the count of fails
        $count = $this->modx->getCount('Unsubscriber', $c);
        // No fail mails at all - Positive
        if($count <= 0)
            return false;
        $items = $this->modx->getCollection('Unsubscriber', $c);
        foreach($items as $item) {
            $list[] = $item->toArray('', false, true);
        }
        return array('type' => 'unsubs', 'tab' => 'subscriber', 'data' => $list);
    }

    public function generateOutput($data)
    {
        $rows = null;
        $keys = array();
        foreach($data['data'] as $item) {
            if(empty($keys))
                $keys = array_keys(array_combine(
                            array_map(create_function('$k', 'return "campaigner.inspector.col.".$k;'), array_keys($item))
                            , $item
                        ));
            $this->smarty->assign('item', $item);
            $rows[] = $this->smarty->fetch($this->config['corePath'] . 'elements/smarty/inspector/' . $data['type'] . '_tr.tpl');
        }
        
        $lex_keys = array_map(create_function('$k', 'global $modx;return $modx->lexicon($k);' ), $keys);
        $this->smarty->assign('keys',$lex_keys);
        $this->smarty->assign('content', implode('', $rows));
        $this->smarty->assign('type', $data['type']);
        $this->smarty->assign('count', $data['count']);
        $this->smarty->assign('topic', $this->modx->lexicon('campaigner.inspector.' . $data['type']));
        $this->smarty->assign('message', $this->modx->lexicon('campaigner.inspector.' . $data['type'] . '.message'));
        $this->smarty->assign('link', $this->modx->getOption('manager_url') . '?a=' . $this->config['actionId'] . '#campaigner-tab-' . $data['tab']);
        $this->smarty->assign('link_description', $this->modx->lexicon('campaigner.inspector.' . $data['type'] . '.link_description'));
        return $this->smarty->fetch($this->config['corePath'] . 'elements/smarty/inspector/table.tpl');
        // $lexicon = $this->modx->lexicon('campaigner.inspector.failed.item', array());
        // echo $lexicon;
    }

    private function _time2str($ts)
    {
        if(!ctype_digit($ts))
            $ts = strtotime($ts);

        $diff = time() - $ts;
        if($diff == 0)
            return 'now';
        elseif($diff > 0)
        {
            $day_diff = floor($diff / 86400);
            if($day_diff == 0)
            {
                if($diff < 60) return 'just now';
                if($diff < 120) return '1 minute ago';
                if($diff < 3600) return floor($diff / 60) . ' minutes ago';
                if($diff < 7200) return 'seit 1 Stunde';
                if($diff < 86400) return 'seit ' . floor($diff / 3600) . ' Stunden';
            }
            if($day_diff == 1) return 'Gestern';
            if($day_diff < 7) return 'seit ' . $day_diff . ' Tage';
            if($day_diff < 31) return 'seit ' . ceil($day_diff / 7) . ' Woche(n)';
            if($day_diff < 60) return 'letzten Monat';
            return date('F Y', $ts);
        }
        else
        {
            $diff = abs($diff);
            $day_diff = floor($diff / 86400);
            if($day_diff == 0)
            {
                if($diff < 120) return 'in a minute';
                if($diff < 3600) return 'in ' . floor($diff / 60) . ' minutes';
                if($diff < 7200) return 'in an hour';
                if($diff < 86400) return 'in ' . floor($diff / 3600) . ' hours';
            }
            if($day_diff == 1) return 'Tomorrow';
            if($day_diff < 4) return date('l', $ts);
            if($day_diff < 7 + (7 - date('w'))) return 'next week';
            if(ceil($day_diff / 7) < 4) return 'in ' . ceil($day_diff / 7) . ' weeks';
            if(date('n', $ts) == date('n') + 1) return 'next month';
            return date('F Y', $ts);
        }
    }
}