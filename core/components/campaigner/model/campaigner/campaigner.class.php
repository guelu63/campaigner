<?php
/**
 * The campaigner base class
 * @todo Implement MODx logging
 * @package Campaigner
 * @author Patrick Stummer <info@patrickstummer.com>
 */
class Campaigner
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
        
        // Package, lexicon, vendors
        $this->modx->addPackage('campaigner', $this->config['modelPath'], 'camp_');
        $this->modx->lexicon->load('campaigner:default');
        // require $this->config['basePath'] . 'vendor/autoload.php';
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
    
    /*
    private function parseUrls($match)
	{
	    //echo 'Complete match: '.$match[0].PHP_EOL;
	    //echo 'Matched URL: '.$match[1].PHP_EOL;

        $siteurl = $this->modx->config['site_url'];

        //skip ubsubscribe and external links
	    if( stripos($match[1], '{{$unsubscribe}}') !== false || stripos($match[1], 'http://') !== false || stripos($match[1], 'https://') !==false || stripos($match[1], 'mailto:') !== false )
	    {
	        return $match[0];
	    }
	    else
	    {
	        $replace = $match[1]; //text to replace
	        $replaced = $siteurl.( substr($match[1], 0, 1) == '/' ? '' : '/' ).$match[1]; //replaced url
	        return str_replace($replace, $replaced, $match[0]);
	    }
	}
    */

    /**
     * Sign up a new user for the mailing system
     *
     * @access public
     * @param $data
     * @return bool False on signup error
     */
    public function subscribe($data)
    {
        // provide a unique email
        if( $this->emailTaken($data['email']) ) {
            $this->errormsg[] = $this->modx->lexicon('campaigner.subscribe.error.emailtaken');
            $this->reason = 'exists';
            // return false;
        }
        // provide a valid email
        if(!preg_match("/^(.+)@([^@]+)$/", $data['email'])) {
            $this->errormsg[] = $this->modx->lexicon('campaigner.subscribe.error.noemail');
            $this->reason = 'invalid';
            // return false;
        }
        // a group is required
        if(empty($data['groups'])) {
            $this->errormsg[] = $this->modx->lexicon('campaigner.subscribe.error.nogroup');
            $this->reason = 'nogroup';
            // return false;
        }
        
        if(!empty($this->reason))
            return false;

        // if(count($this->errormsg) > 0)
        //     return false;

        $groups = $data['groups'];
        unset($data['groups']);
        
        // new subscriber
        $subscriber     = $this->modx->newObject('Subscriber');
        $data['key']    = $this->generate_key(array('email' => $data['email']));
        // $data['key']    = md5(time() . substr($data['email'], rand(1,3), rand(-4, -1)));
        $data['active'] = 0;
        $data['since']  = time();
        $data['text']   = !empty($data['text']) ? 1 : 0;
        $subscriber->fromArray($data);
        $subscriber->save();

        // add subscriber to provided groups
        if(is_string($groups)) $groups = explode(',', $groups);
        if(is_array($groups)) {
            foreach($groups as $group) {
                // only allow signing to public groups
                if( $this->modx->getCount('Group', array('id' => $group, 'public' => 1)) == 1 )
                {
                    $grpSub = $this->modx->newObject('GroupSubscriber');
                    $_grpsub = array(
                        'group' => $group,
                        'subscriber' => $subscriber->get('id'),
                        );
                    // $grpSub->set('group', $group);
                    // $grpSub->set('subscriber', $subscriber->get('id'));
                    // var_dump($_grpsub);
                    $grpSub->fromArray($_grpsub);
                    $grpSub->save();
                }
            }
        }
        // die();
        //sent confirm message
        $mailer     = $this->getMailer();
        $params = array(
            'subscriber' => $subscriber->get('email'),
            'key' => $subscriber->get('key'),
            );

        // compose message
        $document = $this->modx->getObject('modDocument', $this->modx->getOption('campaigner.confirm_mail'));
        $message  = $this->composeNewsletter($document);
        $message  = $this->processNewsletter($message, $subscriber, array(
            'campaigner.confirm' => $this->modx->makeUrl($this->modx->getOption('campaigner.confirm_page'), '', $params, 'full')
            )
        );
        
        // set properties
        $mailer->set(modMail::MAIL_SUBJECT, $document->get('pagetitle'));
        $mailer->set(modMail::MAIL_BODY, $message);
        $mailer->setHTML(true);
        $mailer->address('to', $subscriber->get('email'));

        // Send the subscription confirmation mail
        if (!$mailer->send()) {
            $this->errormsg[] = $this->modx->lexicon('campaigner.subscribe.error.confirm_mail');
            return false;
        }
        $this->errormsg[] = $this->modx->lexicon('campaigner.subscribe.success.confirm_mail');
        
        // Send system mail
        $email = $subscriber->get('email');
        $link = $this->modx->getOption('manager_url') . '?a=' . $this->config['actionId'] . '#campaigner-tab-subscriber';
        $system_mail = array(
            'subject'   => $this->modx->lexicon('campaigner.system.mail.subscriber_new.subject'),
            'body'      => $this->modx->lexicon('campaigner.system.mail.subscriber_new.body', array('email' => $email, 'link' => $link)),
            );
        $this->sendSystemMail($system_mail);
        return true;
   }

   /**
    * Generate subscriber key
    * @param  array  $options Information to use for encryption
    * @return string          MD5 generated string
    */
   public function generate_key($options = array())
   {
       return md5(time() . substr($options['email'], rand(1,3), rand(-4, -1)));
   }

    /**
     * Checks if an email address is allready taken
     *
     * @access public
     * @param string $email The email address to check
     * @return boolean False if address already in use, true if address is not in use
     */
    public function emailTaken($email)
    {
    	$subscriber = $this->modx->getObject('Subscriber', array('email' => $email));
    	if($subscriber) return true;
       return false;
   }

    /**
     * Checks code and activates subscriber
     * 
     * @access public
     * @param string $subscriber Subscribers email address or identifier correspondig to $type
     * @param string $key The subscribers key
     * @param string $type The indentifier to identify a subscriber
     * @return boolean true on activated, false on not found
     */
    public function confirm($subscriber, $key, $type = 'email')
    {
    	$subscriber = $this->modx->getObject('Subscriber', array($type => $subscriber));
    	if( $subscriber && $subscriber->get('active') == 0 && $subscriber->get('key') == $key ) {
            $subscriber->set('active', 1);
            $subscriber->set('key', md5(time() . substr($_SERVER['REQUEST_URI'], rand(1, 20)) . $_SERVER['REMOTE_ADDR']));
			//Add the confirmation date to the table
			//added on 2011-05-10 by andreas
            $subscriber->set('since', time());
            $subscriber->save();
            // Send system mail
            $email = $subscriber->get('email');
            $link = $this->modx->getOption('manager_url') . '?a=' . $this->config['actionId'] . '#campaigner-tab-subscriber';
            $system_mail = array(
                'subject'   => $this->modx->lexicon('campaigner.system.mail.subscriber_confirmed.subject'),
                'body'      => $this->modx->lexicon('campaigner.system.mail.subscriber_confirmed.body', array('email' => $email, 'link' => $link)),
                );
            $this->sendSystemMail($system_mail);
            return true;
        }
        
        if(!$subscriber) {
            $this->errormsg[] = $this->modx->lexicon('campaigner.confirm.error.nosubscriber');
        } elseif($subscriber->get('active') == 1) {
            $this->errormsg[] = $this->modx->lexicon('campaigner.confirm.error.active');
        } elseif($subscriber->get('key') !== $key) {
            $this->errormsg[] = $this->modx->lexicon('campaigner.confirm.error.invalidkey');
        }
        return false;
    }

    /**
     * Checks code and removes subscriber
     * 
     * @access public
     * @param  array $params Given data (e.g. groups, subscriber, key)
     * @param string $type The indentifier to identify a subscriber
     * @return boolean true on removed, false on not found
     */
    public function unsubscribe($params = array(), $type = 'email')
    {
        // Get the subscriber object
        // $this->modx->setDebug(true);
        $c = $this->modx->newQuery('Subscriber');
        $c->where(array($type => $params['subscriber']));
        // $c->prepare();
        // echo $c->toSQL();
        $subscriber = $this->modx->getObject('Subscriber', $c);
        
        if(!$subscriber)
            $this->errormsg[] = $this->modx->lexicon('campaigner.unsubscribe.error.nosubscriber');
        
        if($subscriber->get('key') !== $params['key'])
            $this->errormsg[] = $this->modx->lexicon('campaigner.unsubscribe.error.invalidkey');

        // Get the subscriber's groups which should be disabled
        $c = $this->modx->newQuery('GroupSubscriber');
        $c->leftJoin('Group', 'Group', '`Group`.id = `GroupSubscriber`.`group`');
        $c->select($this->modx->getSelectColumns('GroupSubscriber', 'GroupSubscriber', ''));
        $c->select($this->modx->getSelectColumns('Group', 'Group', 'g_', array('id', 'name')));
        $c->where(array('group:IN' => $params['groups'], 'subscriber' => $subscriber->get('id')));
        // $c->prepare();
        // echo $c->toSQL();
        // die();
        $sub_groups_off = $this->modx->getCollection('GroupSubscriber', $c);

        // Get all groups a subscribers which are enabled
        $sub_groups_all = $this->getGroups($subscriber->get('email'));

        // If the subscriber will be removed from all groups => remove subscriber
        $remove = false;
        if(count($sub_groups_all) === count($sub_groups_off))
            $remove = true;

        $success = false;
        // Only remove the checked groups, if not all groups are checked
        if(!$remove) {
            foreach($sub_groups_off as $rmv_group) {
                $groups_off[] = $rmv_group->get('g_name');
                $rmv_group->remove();
            }
            // var_dump($groups_off);
            $this->errormsg[] = $this->modx->lexicon('campaigner.unsubscribe.fromgroups') . implode(', ', $groups_off);
            $success = true;
        }

        // Set inactive or remove the subscriber
        // If 'remove' = true composite relationship will delete group subscriptions, queue items and bounces
        // Add data for statistics
        $unsub = $this->modx->newObject('Unsubscriber');
        $data = array(
            'subscriber' => $params['subscriber'],
            'newsletter' => $params['letter'],
            'reason' => $params['reason'],
            'date' => time(),
            'via' => $params['via'],
            );
        $unsub->fromArray($data);
        if($unsub->save())
            $this->errormsg[] = $this->modx->lexicon('campaigner.unsubscribe.statistics');

    	if($subscriber && $subscriber->get('key') == $params['key'] && $remove) {
            // if($subscriber->remove()) {
            //     $this->errormsg[] = $this->modx->lexicon('campaigner.unsubscribe.success');
            //     $success = true;
            // }
        }
        return $success;
    }

    /**
     * Create a newsletter instance to be sent when timed
     *
     * Creates a new newsletter instance to be sent on time
     * by duplicating the actual autonewsletter resource and change
     * specific properties appropiately. A system setting allows
     * to decide if these new instances will be sent automatically or
     * must be approved.
     * 
     * @param  int $id Autonewsletter ID
     * @return bool     Either true or false
     */
    public function sheduleAutoNewsletter($id = null)
    {

        // Get system setting for auto-generated newsletters
        // #1: Automatically send ('campaigner.newsletter.autosend')
        $autosend = $this->modx->getOption('campaigner.newsletter.autosend', 0);

		//$fileHandlerShedule=fopen("/var/www/modx/buli/v1.1/core/components/campaigner/send_log.txt",a);
		//fwrite($fileHandlerShedule,"Start sheduleAutoNewsletter [".date('d.m.Y H:i:s',time())."]:\n");

        // the newsletters
        $c = $this->modx->newQuery('Autonewsletter');
        
        $c->where('`Autonewsletter`.`state` = 1 AND UNIX_TIMESTAMP() > (GREATEST((`Autonewsletter`.`start`), COALESCE(`Autonewsletter`.`last`, 0)) + `Autonewsletter`.`frequency` + TIME_TO_SEC(`Autonewsletter`.`time`))');

        if($id)
            $c->where(array('id' => $id));
		
        // $c->prepare();
        // var_dump($c->toSQL());
        // die();

        $newsletters = $this->modx->getCollection('Autonewsletter', $c);

        if(!$newsletters) {
		    //fwrite($fileHandlerShedule,"NO sheduleAutoNewsletter [".date('d.m.Y H:i:s',time())."]:\n");
            return false;
        }
        
        // process all newsletters
        foreach($newsletters as $newsletter) {	    
            $document = $this->modx->getObject('modDocument', $newsletter->get('docid'));
            $this->modx->user = $document->getOne('CreatedBy');	    
    			//fwrite($fileHandlerShedule,"Autonewsletter [".$document->get('pagetitle')."]:\n");

            // Duplicate resource into newsletter folder
    		// Plus 3600 at the end: for example "14:00:00" results in 13 hours
            $timestamp = max($newsletter->get('last'), $newsletter->get('start')) + $newsletter->get('frequency') + strtotime('1970-01-01 ' . $newsletter->get('time'))+3600;
            $newDoc   = $document->duplicate(array(
                'parent'  => $this->modx->getOption('campaigner.newsletter_folder'),
                'newName' => $this->parsePagetitle($document->get('pagetitle'), $timestamp)
                ));
            // New alias because we cant set it
            if(false === strpos($document->get('pagetitle'), '{')) {
                $newDoc->set('alias', $document->get('alias') .'-'. date('Ymd', $timestamp));
            }

            $newDoc->set('publishedon', $timestamp);
            $newDoc->save();

            // If setting is true ('campaigner.autofill'), run autofill on the created newsletter instance
            if($this->modx->getOption('campaigner.autofill'))
                $this->modx->runSnippet('CampaignerAutofill', array('resource' => $newDoc, 'runSnippet' => true));

            // Create the newsletter
            $newNl = $this->modx->newObject('Newsletter');
            $newNl->fromArray(array(
                'docid'        => $newDoc->get('id'),
                'state'        => $autosend,
                'total'        => 0,
                'sent'         => 0,
                'bounced'      => 0,
                'sender'       => $newsletter->get('sender'),
                'sender_email' => $newsletter->get('sender_email'),
                'auto'         => $newsletter->get('id'),
                //'auto'	       => 1,
                'priority'     => 5
                ));
            $newNl->save();

            $day = date("d", $timestamp);
            $month = date("m", $timestamp);
            $year = date("y", $timestamp);

            $timestamp =  strtotime($year."-".$month."-".$day." 00:00:00");

            $newsletter->set('last', $timestamp);
            $newsletter->save();

                // assign the groups
            $groups = $newsletter->getMany('AutonewsletterGroup');
            foreach($groups as $group) {
                $ng = $this->modx->newObject('NewsletterGroup');
                $ng->fromArray(array(
                    'newsletter' => $newNl->get('id'),
                    'group'      => $group->get('group')
                    ));
                $ng->save();
            }
    	    //fwrite($fileHandlerShedule,"End Autonewsletter [".$document->get('pagetitle')."]:\n");
        }
        // $link = $this->modx->makeUrl(0, 'mgr', array('a' => 30, 'id' => $newNl->get('docid'), 'letter' => 1), 'full');
        $link = $this->modx->getOption('site_url') . 'manager/?a=30&id=' . $newNl->get('docid');
        $system_mail = array(
            'subject'   => $this->modx->lexicon('campaigner.system.mail.newsletter_new.subject'),
            'body'      => $this->modx->lexicon('campaigner.system.mail.newsletter_new.body', array('link' => $link)),
            );

        if(!$autosend)
            $this->sendSystemMail($system_mail);

        //fwrite($fileHandlerShedule,"End sheduleAutoNewsletter [".date('d.m.Y H:i:s',time())."]:\n");
        //fclose($fileHandlerShedule);
        return true;
    }

    /**
     * Create the email queue
     *
     * @access public
     * @param array $options Array containing data for further handling, see below
     * @param int $nl Newsletter-ID
     * @param array $subscriber Array of subscriber-ids
     * @param int $scheduled Schedule time when this element should be send
     * @param string $type Type of queue-item
     * @return int Number of entrys added to the queue
     */
    public function createQueue($options = null)
    {

        // create the query for the newsletter
        $c = $this->modx->newQuery('Newsletter');
        $c->innerJoin('modDocument', 'modDocument', '`modDocument`.`id` = `Newsletter`.`docid`');
        if($options['nl']) {
            $c->where('`Newsletter`.`id` = ' . $options['nl']);
        } else {
            $c->where('`Newsletter`.`state` = 1');
            $c->where('((sent_date IS NULL AND `modDocument`.`publishedon` < UNIX_TIMESTAMP() AND `modDocument`.`publishedon` > 0) OR (`modDocument`.`pub_date` < UNIX_TIMESTAMP() AND `modDocument`.`publishedon` < `modDocument`.`pub_date` AND `modDocument`.`pub_date` > 0))');
            $c->sortby('`Newsletter`.`priority`', 'ASC');
    		$c->sortby('`modDocument`.`publishedon`', 'ASC'); // first document first
        }
        $c->limit(1, 0); // one per run is enough
        // $c->prepare();
		// echo $c->toSQL();
        //$this->modx->log($this->modx->LOG_LEVEL_ERROR, "SQL NEWSLETTER QUERY --> " . $c->toSql());
        $newsletter = $this->modx->getObject('Newsletter',$c);
        if(!$newsletter) {
			//fwrite($fileHandlerCreate,"NO createQueue [".date('d.m.Y H:i:s',time())."]:\n");
            return;
        }

        $document   = $this->modx->getObject('modDocument', $newsletter->get('docid'));
        $composedNewsletter = $this->composeNewsletter($document);

		//fwrite($fileHandlerCreate,"Start createQueue [".date('d.m.Y H:i:s',time())."] [".$document->get('pagetitle')."]:\n");
        /**
         * @since 2013-04-11 Added a cache file creation instead of storing composed newsletter in content of resource
         */
        $cacheOptions = array(
            xPDO::OPT_CACHE_KEY => '',
            xPDO::OPT_CACHE_HANDLER => 'xPDOFileCache',
            xPDO::OPT_CACHE_EXPIRES => 0,
            );
        $cacheElementKey = 'newsletter/' . $document->get('id');

        // Now the subscribers
        $c = $this->modx->newQuery('Subscriber');
        if($options['subscriber']) {
            $c->where(array('id:IN' => $options['subscriber']));
        } else {
            // they have to be in the group
            $c->innerJoin('GroupSubscriber', 'GroupSubscriber', '`GroupSubscriber`.`subscriber` = `Subscriber`.`id`');
            $c->innerJoin('NewsletterGroup', 'NewsletterGroup', '`NewsletterGroup`.`group` = `GroupSubscriber`.`group` AND `NewsletterGroup`.`newsletter` = '. $newsletter->get('id'));
            $c->leftJoin('Group', 'Group', '`Group`.id = `NewsletterGroup`.`group`');
            $c->groupby('`Subscriber`.`id`'); // only send once per subscriber
            $c->sortby('`Group`.`priority`', 'ASC');
        }
        $c->select('`Subscriber`.`id`');
        $c->where(array('active:=' => 1)); // only active ones
        $c->prepare();
		//$this->modx->log($this->modx->LOG_LEVEL_ERROR, "SQL NEWSLETTER QUERY --> " . $c->toSql());
        $subscribers = $this->modx->getCollection('Subscriber', $c);
        
		//fwrite($fileHandlerCreate,"build queue for [".$document->get('pagetitle')."]:\n");
        // build queue
        $count = 0;
        foreach($subscribers as $subscriber) {
            $tStart = $this->modx->getMicroTime();

            $queueItem = $this->modx->newObject('Queue');
            $queueItem->fromArray(array(
                'newsletter' => $newsletter->get('id'),
                'subscriber' => $subscriber->get('id'),
                'state'      => 0,
                'priority'   => $newsletter->get('priority'),
                'created'    => time(),
                'type'       => $options['type'],
                'scheduled'  => $options['scheduled'],
                ));
            $queueItem->save();
			//md5 verschluesselte QueueID als key-Attribut mitspeichern
			//fwrite($fileHandlerCreate,"CREATE KEY FOR QUEUE ELEMENT ".$queueItem->get('id')." [".md5($queueItem->get('id'))."]:\n");
            $queueItem->set('key', md5($queueItem->get('id')));
            
            $tEnd = $this->modx->getMicroTime();
            $properties = unserialize($queueItem->get('properties'));
            $properties['created'] = sprintf("%2.4f", $tEnd - $tStart);
            $queueItem->set('properties', serialize($properties));

            $queueItem->save();
            $queueItem = null;
            $count++;
        }

        if($count > 0) {
			/**
			* Feed the manager log
			*/
           $l = $this->modx->newObject('modManagerLog');
           $data = array(
              'user'      => 41,
              'occurred'  => date('Y-m-d H:i:s'),
              'action'    => 'queued_newsletter_items (' . $count . ')',
              'classKey'  => 'Newsletter',
              'item'      => $newsletter->get('id')
              );

           $l->fromArray($data);
           $l->save();
        }

		//fwrite($fileHandlerCreate,"end build queue for [".$document->get('pagetitle')."]:\n");
        $this->modx->log(modX::LOG_LEVEL_ERROR, 'Everthing is fine. We saved the newsletter.');
        
        // Save the date
        $newsletter->set('sent_date', time());
        $newsletter->set('total', $count);
        if(!$newsletter->save())
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'An error occurred while saving the newsletter.');
		//fwrite($fileHandlerCreate,"End createQueue [".date('d.m.Y H:i:s',time())."]:\n");
        //fclose($fileHandlerCreate);
        $link = $this->modx->getOption('manager_url') . '?a=' . $this->config['actionId'] . '#campaigner-tab-queue';
        $system_mail = array(
            'subject'   => $this->modx->lexicon('campaigner.system.mail.queue_new.subject'),
            'body'      => $this->modx->lexicon('campaigner.system.mail.queue_new.body', array('count' => $count, 'link' => $link)),
            );
        $this->sendSystemMail($system_mail);

        return $count;
    }

    /**
     * Process email queue and send messages.
     *
     * @param array $ids IDs to process
     * @access public
     */
    public function processQueue($ids = array())
    {
        $this->modx->getParser();

        // get queue items for the current batch
        $c = $this->modx->newQuery('Queue');

        // Check if only specific items need to be send
        if(count($ids) > 0 && is_array($ids))
            $c->where(array('id:IN' => $ids));
        
        if(count($ids) == 0 || empty($ids))
            $c->limit($this->modx->getOption('campaigner.batchsize'));

        //Nimm alle Elemente aus der Queue die noch zu senden sind (0) oder ein Resend sind (8)
        // $c->where('`Queue`.`state`=0 OR `Queue`.`state`=8');
        $c->where(array('state:IN' => array(0,8)));
        // Check if this item is scheduled
        // and the schedule is reached
        $c->where(array('scheduled:<=' => time()));

        //$c->where('`Queue`.`state` = 0');
        $c->leftJoin('Subscriber');
        $c->select('`Queue`.*, `Queue`.`key` AS queue_key, `Subscriber`.`firstname`, `Subscriber`.`lastname`, `Subscriber`.`email`, `Subscriber`.`text`, `Subscriber`.`key`, `Subscriber`.`address`, `Subscriber`.`title`');
        $c->sortby('`Queue`.`priority`');
        $c->prepare();
        
        $queue = $this->modx->getCollection('Queue', $c);

        $ids = array();
        foreach($queue as $item) {
          // echo $item->get('id') . ' - ' . $item->get('email') . $item->get('state') . "\n";
          $ids[] = $item->get('id');
        }

        // $this->modx->query('UPDATE `camp_queue` SET `state` = 3 WHERE `id` IN('. implode(',', $ids) .')');	

        $sumTime = 0;
        $cnt = 0;

        // and process each one of them
        $newsletter = null;
        foreach($queue as $item) {
            $tStart = $this->modx->getMicroTime();
            //Testing - only sends to the 2 first recipients
            // if($cnt > 1) break;

    		// don't fetch allready loaded newsletters
            if(!$newsletter || $newsletter->get('id') !== $item->get('newsletter')) {
                $c = $this->modx->newQuery('Newsletter');
                $c->innerJoin('modDocument', 'modDocument', '`modDocument`.`id` = `Newsletter`.`docid`');
                $c->where('`Newsletter`.`id` = '. $item->get('newsletter'));
                $c->select('`Newsletter`.*, `modDocument`.`content`, `modDocument`.`pagetitle`, `modDocument`.`publishedon`');
    				//$c->prepare(); var_dump($c->toSql()); die;
                $newsletter = $this->modx->getObject('Newsletter', $c);       
            }
            
            $mailer = $this->getMailer(
                array(
                   'sender' => $newsletter->get('sender'),
                   'sender_email' => $newsletter->get('sender_email')
                ));
            $mailer->set(modMail::MAIL_SUBJECT, $newsletter->get('pagetitle'));

            $tags = $this->getNewsletterTags($newsletter);
			// compose message
            $subscriber = $this->modx->newObject('Subscriber', $item->toArray());

            /**
             * Get newsletter via cache resource
             * @since v1.0.0
             */
            $cacheOptions = array(
                xPDO::OPT_CACHE_KEY => '',
                xPDO::OPT_CACHE_HANDLER => 'xPDOFileCache',
                xPDO::OPT_CACHE_EXPIRES => 0,
                );
            $content = $this->modx->cacheManager->get('newsletter/' . $newsletter->get('docid'), $cacheOptions);

            if ( (boolean) $this->modx->getOption('campaigner.tracking_enabled', '', FALSE) )
                $this->makeUrl('trackingImage', $newsletter->get('id'), 'image', $subscriber);

            $message = $this->makeTrackingUrls($content, $newsletter, $subscriber);
            $message = $this->processNewsletter($message, $subscriber, $tags);
            
			// $message = $this->processNewsletter($newsletter->get('content'), $subscriber, $tags);
            $textual = $this->textify($message);
            // echo $message;
			// compose mail
			/**
			 * For testing reasons use any available account
			 * Uncomment / Comment the lines
			 * Don't forget to reverse your commentary lines when done!
			 */
			//$mailer->address('to', 'andreas@subsolutions.at' );
			
			/**
			 * Normal behaviour picks the current subscriber
			 */
			$mailer->address('to', $subscriber->get('email'));
			if($subscriber->get('text')) {
				$mailer->setHtml(false);
				$mailer->set(modMail::MAIL_BODY, $textual);
			} else {
				$mailer->setHtml(true);
				$mailer->set(modMail::MAIL_BODY, $message);
				$mailer->set(modMail::MAIL_BODY_TEXT, $textual);
				//$mailer->mailer->MsgHTML($message, $this->modx->getOption('base_path'));
			}
			
			$item_array = $item->toArray();
			$mailer->mailer->AddCustomHeader('X-Campaigner-Mail-ID:'. $item->get('queue_key'));
			
			/**
			* Add attachment(s) to mail
			* @todo: none yet */
			$tv = $this->modx->getObject('modTemplateVar',array('name'=>'tvAttach'));

			/* get the raw content of the TV */
			$val = $tv->getValue($newsletter->get('docid'));
			if($val) {
				$vals = explode(',', $val);
				$vals = array_filter($vals, 'trim');

				foreach($vals as $val) {
					$mailer->mailer->AddAttachment($this->modx->getOption('base_path').$val);
				}
			}
			
            // Prepare array for log file action
            $options = array(
                'newsletter'    => $newsletter->toArray(),
                'item'          => $item->toArray(),
                'subscriber'    => $subscriber->toArray(),
                );

            // Check sending status and write to log file
			if (!$mailer->send()) {
				//If the mail was not sent, set state to 0.
				$item->set('state', 6);
                // var_dump($mailer->mailer->ErrorInfo);
                // return;
                $item->set('error', $mailer->mailer->ErrorInfo);
                $options['message'] = $mailer->mailer->ErrorInfo;
				$this->logFile($options);
                // $this->modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the an email to '.$subscriber->get('email') .' ### '. $mailer->mailer->ErrorInfo);
			} else {
				//Wenn es sich bei diesem Item um ein Resend handelt (state = 8) oder einen der gerade bearbeitet wird (state = 10)
				if($item->get('state') == 8) {
					$item->set('state', 9);
					//Dazugehoerigen ResendCheck Eintrag auf erfolgreich (state = 1) setzen
					//ResendCheck Objekt holen
					$resend_check = $this->modx->getObject('ResendCheck', array('queue_id' => trim($item->get('id'))));
					if($resend_check) {
						$resend_check->set('state',1);
						$resend_check->save();
					}
				} else {
                    $options['message'] = $mailer->mailer->ErrorInfo;
                    $this->logFile($options);
					$item->set('state', 1);
				}
                // Set time of delivery
				$item->set('sent', time());
			}
			// partial reset
			$mailer->mailer->ClearAllRecipients();
			$mailer->mailer->ClearCustomHeaders();

            // Add timing information
            $tEnd = $this->modx->getMicroTime();
            $properties = unserialize($item->get('properties'));
            $properties['processed'] = sprintf("%2.4f", $tEnd - $tStart);
            $item->set('properties', serialize($properties));
            $item->save();
			$cnt++;
        }

        // Write batch information to manager log
        if($cnt > 0) {
            // Set sent count
            $newsletter->set('sent', $newsletter->get('sent') + $cnt);
            $newsletter->save();

            // Feed the manager log
            $l = $this->modx->newObject('modManagerLog');
            $data = array(
              'user'      => 41,
              'occurred'  => date('Y-m-d H:i:s'),
              'action'    => 'sent_newsletter_items (' . $cnt . ')',
              'classKey'  => 'Newsletter',
              'item'      => $newsletter->get('id')
              );

            $l->fromArray($data);
            $l->save();

            // Set tvCampaignerData
            $tv = $this->modx->getObject('modTemplateVar',array('name'=>'tvCampaignerData'));
            $tv->setValue($newsletter->get('docid'), json_encode($newsletter->toArray()));
            $tv->save();

            // Set tvCampaignerSent
            $tv_sent = $this->modx->getObject('modTemplateVar',array('name'=>'tvCampaignerSent'));
            $tv_sent->setValue($newsletter->get('docid'), 1);
            $tv_sent->save();
        }

        // Check for last batch and send system mail
        // WHERE all queue items which are not sent of this newsletter
        $c = $this->modx->newQuery('Queue');
        $c->where(array('newsletter' => $newsletter->get('id'), 'state' => 0));
        
        // Get count of unsent queue items
        $unsent = $this->modx->getCount('Queue', $c);
        if($unsent == 0) {
            $c = $this->modx->newQuery('Queue');
            $c->where(array('newsletter' => $newsletter->get('id'), 'state' => 1));
            $sent = $this->modx->getCount('Queue', $c);
            $link = $this->modx->getOption('manager_url') . '?a=' . $this->config['actionId'] . '#campaigner-tab-queue';
            $system_mail = array(
            'subject'   => $this->modx->lexicon('campaigner.system.mail.queue_finished.subject'),
            'body'      => $this->modx->lexicon('campaigner.system.mail.queue_finished.body', array('count' => $sent, 'link' => $link)),
            );
            
            $this->sendSystemMail($system_mail);
        }

        //fclose($fileHandler);
       return;
    }

    public function logFile($options = array())
    {
        // error_reporting(-1);
        $log_target = array(
            'target'=>'FILE',
            'options' => array(
                'filename'=> 'campaigner/' . $options['newsletter']['id'] . '.log'
            )
        );
        // $this->modx->setLogTarget($log_target);
        // var_dump($log_target);
        $message = 'SUBSCRIBER: ' . $options['subscriber']['email'] . ' QUEUE-ITEM: ' . $options['item']['id'] . ' - ' . $options['message'];
        $this->modx->log(MODX_LOG_LEVEL_ERROR, $message, $log_target);
    }

    /**
     * Preprocesses a newsletter to just leave personalization
     *
     * @access public
     * @param object $document The document object
     * @return string The composed newsletter
     */
    public function composeNewsletter($resource)
    {
        $this->modx->resource = $resource;
        $this->modx->resourceIdentifier = $resource->get('id');
        $this->modx->elementCache = array();
        $resourceOutput = $this->modx->resource->process();
        
        $resourceOutput = $this->parseCampaignerTags($resourceOutput);

        $this->modx->parser->processElementTags('', $resourceOutput, true, false, '[[', ']]', array(), $maxIterations);
        $this->modx->parser->processElementTags('', $resourceOutput, true, true, '[[', ']]', array(), $maxIterations);
        $resourceOutput = $this->unparseCampaignerTags($resourceOutput);
        return $resourceOutput;
    }

    /**
     * Process a newsletter for a single subscriber
     *
     * @access public
     * @param string $newsletter The preprocessed newsletter text
     * @param object $subscriber The subscriber object
     * @param array $tags Additional tags to parse in the newsletter
     * @return string The fully personalized newsletter text
     */
    public function processNewsletter($newsletter, $subscriber, $tags = array())
    {
        $newsletter = str_replace(array('%5B%5B%2B','%5B%5B&#43;', '%5B%5B', '%5D%5D'), array('[[+', '[[+', '[[', ']]'), $newsletter);
        $allTags = array_merge($tags, $this->getSubscriberTags($subscriber));
        // var_dump($allTags);
        // die();
        // var_dump($tags);
        $this->modx->unsetPlaceholders(array_keys($allTags));
        $this->modx->setPlaceholders($allTags);
        if($tags['process'] === 'false')
            return $newsletter;
        $this->modx->parser->processElementTags('', $newsletter, true, false, '[[', ']]', array(), $maxIterations);
        $this->modx->parser->processElementTags('', $newsletter, true, true, '[[', ']]', array(), $maxIterations);
        //$newsletter = $this->parsePathes($newsletter);
        return $newsletter;
    }

    /**
     * Add attachments to a newsletter
     *
     * @access: public
     * @param: object $resource MODx resource
     * @return array Array of filepath(s) to add as attachments
     */
    public function getAttachments($mailer, $resource)
    {
        /* Get the TV */
        $tv = $this->modx->getObject('modTemplateVar',array('name' => $this->modx->getOption('campaigner.attachment_tv')));
        $element = $this->modx->getOption('campaigner.attachment_tv_element');
        $ms = $this->modx->getObject('modMediaSource', $this->modx->getOption('campaigner.attachment_mediasource'));
        $ms_props = $ms->getPropertyList();

        if(!$tv)
            return $mailer;

        /* get the raw content of the TV */
        $vals = json_decode($tv->getValue($resource->get('id')));

        // die();
        if(is_string($vals) && !is_array($vals))
            $vals = array_filter(explode(',', $val), 'trim');

        $mailer->mailer->ClearAttachments();
        foreach($vals as $val) {
            $success = false;
            if($mailer->mailer->AddAttachment($this->modx->getOption('base_path').$ms_props['basePath'].$val->$element))
                $success = true;
        }
        return $mailer;
    }

    /**
     * Make all pathes to absolute ones
     *
     * @access public
     * @param string $message   The allready processed message
     * @return string   The message with absolute pathes
     */
    public function parsePathes($message)
    {
        $siteUrl = 'http://www.bundesliga.at/';
        return preg_replace('#src=[\'"]{1}('.$siteUrl.'|http://)?([^\'"]*)[\'"]{1}#i', 'src="'. $siteUrl .'\\2"', $message);
    }

    /**
     * Get campaigner subscriber tags
     *
     * @access public
     * @param object $subscriber The subscriber object
     * @return array
     */
    public function getSubscriberTags($subscriber = null)
    {
        if(is_null($subscriber)) {
            return array(
                'campaigner.email'       => null,
                'campaigner.address'     => null,
                'campaigner.title'       => null,
                'campaigner.salutation'  => null,
                'campaigner.firstname'   => null,
                'campaigner.lastname'    => null,
                'campaigner.unsubscribe' => null,
                'campaigner.istext'      => null,
                'campaigner.key'         => null,
                'campaigner.tracking_image' => null,
                );
        }
        $values = array();
        $c = $this->modx->newQuery('SubscriberFields');
        $c->leftJoin('Fields', 'Fields');
        $c->where(array('`SubscriberFields`.`subscriber`' => $subscriber->get('id')));
        $c->select($this->modx->getSelectColumns('SubscriberFields', 'SubscriberFields', '', array('id', 'value')));
        $c->select(array(
            '`Fields`.`name`'
        ));
        // $c->prepare();
        // echo $c->toSQL();
        $customs = $this->modx->getCollection('SubscriberFields', $c);

        foreach($customs as $custom) {
            $values['campaigner.custom_'.$custom->get('name')] = $custom->get('value');
        }

        $salutation = $this->modx->getOption('campaigner.salutation') . ' ' . ($subscriber->get('firstname') && $subscriber->get('lastname') ? $subscriber->get('firstname') . ' ' . $subscriber->get('lastname') : $subscriber->get('email'));
        
        $params = array(
            'subscriber' => $subscriber->get('email'),
            'key' => $subscriber->get('key'),
            'letter' => $subscriber->get('newsletter'),
            );

        $sub_tags = array_merge(
            $values,
            // $customs->toArray('campaigner.custom_', false, true),
            $subscriber->toArray('campaigner.', false, true),
            array(
                'campaigner.salutation'  => $salutation,
                'campaigner.unsubscribe' => 'href="'. $this->modx->makeUrl($this->modx->getOption('campaigner.unsubscribe_page'), '', $params, 'full') . '"',
                'campaigner.istext'      => $subscriber->get('text') ? 1 : null,
                'campaigner.tracking_image' => $this->modx->getOption('site_url').'assets/components/campaigner/?t='.base_convert($this->created_urls['trackingImage'],10,36).'|[[+campaigner.key]]&amp;',
            )
        );
        foreach ($sub_tags as $key => $value) {
            $dump[] = $key . ': ' . $value;
        }
        $sub_tags['campaigner.dump'] = '<pre>' . implode('<br/>', $dump) . '</pre>';
        // var_dump($this->created_urls['trackingImage']);
        // echo base_convert($this->created_urls['trackingImage'],10,36);
        // die();
        // var_dump($sub_tags);
        return $sub_tags;
    }
    
    /**
     * Get campaigner newsletter tags
     *
     * @access public
     * @param object $newsletter The newsletter object
     * @return array
     */
    public function getNewsletterTags($newsletter)
    {
        $nl_tags = array(
            'campaigner.newsletter_total'        => $newsletter->get('total'),
            'campaigner.newsletter_sender'       => $newsletter->get('sender') ? $newsletter->get('sender') : $this->modx->getOption('campaigner.default_name'),
            'campaigner.newsletter_sender_email' => $newsletter->get('sender_email') ? $newsletter->get('sender_email') : $this->modx->getOption('campaigner.default_from'),
            'campaigner.newsletter_date'         => date('d.m.Y H:i', $newsletter->get('publishedon')),
            'campaigner.newsletter_send_date'    => date('d.m.Y', $newsletter->get('sent_date')),
            'campaigner.newsletter_letter'       => $newsletter->get('id'),
            'campaigner.newsletter_instructions' => $newsletter->get('instructions'),
            );
        
        foreach($nl_tags as $key => $value) {
            $dump[] = $key . ': ' . $value;
        }
        $nl_tags['campaigner.newsletter_dump'] = '<pre>' . implode('<br/>', $dump) . '</pre>';

        return $nl_tags;
    }

    /**
     * Replaces normal modx tags with campaigner tags
     *
     * @access public
     * @param string $content The string to parse campaignertags
     * @return string The processed content with cleared campaigner tags
     */
    public function parseCampaignerTags($content)
    {
        if(!is_array($content)) {
           $content = preg_replace('#\[\[\!?CampaignerExec\? ((&([A-Za-z0-9]+)=`[^`]*`\s*)*)\]\]#ui', '{{CampaignerExec? \\1}}', $content);
           return preg_replace_callback('#\[\[\+campaigner\.([a-z_]+)((:[a-zA-Z0-9]+(=`[^`]*`)?){0,})\]\]#ui',  array($this, 'parseCampaignerTags'), $content);
        }
        $add = '';
        if(strlen($content[2]) > 0) {
            if(strpos($content[2], '[[') !== false) {
                $add = preg_replace_callback('#\[\[\+campaigner\.([a-z_]+)((:[a-zA-Z0-9]+(=`[^`]*`)?){0,})\]\]#ui', array($this, 'parseCampaignerTags'), $content[2]);
            } else {
                $add = $content[2];
            }
        }
        return '{{+campaigner.'. $content[1] . $add .'}}';
    }

    /**
     * Replaces campaignertags with normal modx tags again
     *
     * @access public
     * @param string $content The string to unparse campaignertags
     * @return string The processed content with cleared campaigner tags
     */
    public function unparseCampaignerTags($content)
    {
        if(!is_array($content)) {
           $content = preg_replace('#\{\{\CampaignerExec\? ((&([A-Za-z0-9]+)=`[^`]*`\s*)*)\}\}#ui', '[[!CampaignerExec? &isText=`[[+campaigner.istext]]` \\1]]', $content);
           return preg_replace_callback('#\{\{\+campaigner\.([a-z_]+)((:[a-zA-Z0-9]+(=`[^`]*`)?){0,})\}\}#ui',  array($this, 'unparseCampaignerTags'), $content);
        }
        $add = '';
        if(strlen($content[2]) > 0) {
            if(strpos($content[2], '{{') !== false) {
                $add = preg_replace_callback('#\{\{\+campaigner\.([a-z_]+)((:[a-zA-Z0-9]+(=`[^`]*`)?){0,})\}\}#ui', array($this, 'unparseCampaignerTags'), $content[2]);
            } else {
                $add = $content[2];
            }
        }
        return '[[+campaigner.'. $content[1] . $add .']]';
    }

    /**
     * Parses placeholders in pagetitle
     * 
     * @access public
     * @param string $title The pagetitle to parse
     * @param int $time The timestamp to use
     * @return string The parsed pagetitle
     */
    public function parsePagetitle($title, $time = null)
    {
        return str_replace(array(
            '{d}', '{D}', '{j}', '{l}', '{z}', '{W}', '{m}', '{F}', '{M}', '{Y}', '{y}'
            ), array(
            date('d', $time),
            date('D', $time),
            date('j', $time),
            date('l', $time),
            date('z', $time),
            date('W', $time),
            date('m', $time),
            date('F', $time),
            date('M', $time),
            date('Y', $time),
            date('y', $time),
            ), $title);
    }
    
    public function createAutoresponder()
    {
        $active = $this->modx->getCollection('Autoresponder', array('active' => 1));
        foreach ($active as $item) {
            $subscriber = array();
            $newsletter = '';
            $options = json_decode($item->get('options'));

            if($options->event == 'birthday') {
                // Get the custom field id
                $field = $this->modx->getObject('Fields', array('name' => $options->field));
                // Get the related value elements with a value of this field stored
                $values = $this->modx->getCollection('SubscriberFields', array('field' => $field->get('id')));
                // Iterate through the found values
                foreach ($values as $item) {
                    if(date('m-d', strtotime($item->get('value'))) == date('m-d'))
                        $subscriber[] = $item->get('subscriber');
                }
                if(count($subscriber) > 0)
                    $newsletter = 120;
            }

            // Set the time to send the response items
            // Sum-up today's date (00:00) + delayed (1... days/weeks/months/years) + time of day
            // All in seconds
            $scheduled = strtotime(date('Y-m-d')) + $options->delay_sec + $options->time_sec - 3600;
            
            $count = $this->createQueue(array('nl' => $newsletter, 'subscriber' => $subscriber, 'type' => 'AR', 'scheduled' => $scheduled));
            if($count > 0)
                echo $count . ' Elemente zur Queue';
        }
    }

    /**
     * Resets the mailer instance and sets credentials
     *
     * @access public
     * @param array $options Credentials to be set
     * @return bool
     */
    public function getMailer($options = array())
    {
        if($this->modx->getOption('campaigner.use_modxmailer')) {
            $mailer = $this->modx->getService('mail', 'mail.modPHPMailer');
            $mailer->reset();

            $mailer->mailer->CharSet = $this->modx->getOption('campaigner.mail_charset');;
			//$mailer->set(modMail::MAIL_FROM, !empty($options['sender_email']) ? $options['sender_email'] : $this->modx->getOption('campaigner.default_from') );
            $mailer->set(modMail::MAIL_FROM,!empty($options['sender_email']) ? $options['sender_email'] : $this->modx->getOption('campaigner.default_from'));
            $mailer->set(modMail::MAIL_FROM_NAME,!empty($options['sender']) ? $options['sender'] : $this->modx->getOption('campaigner.default_name'));
            $mailer->set(modMail::MAIL_SENDER,!empty($options['sender']) ? $options['sender'] : $this->modx->getOption('campaigner.default_from'));

			//$mailer->set(modMail::MAIL_FROM_NAME, !empty($options['sender']) ? $options['sender'] : $this->modx->getOption('campaigner.default_name'));
			//$mailer->set(modMail::MAIL_FROM_NAME, !empty($options['sender']) ? $options['sender'] : '=?UTF-8?B?'.base64_encode($this->modx->getOption('campaigner.default_name')).'?=' );
			//$mailer->set(modMail::MAIL_SENDER, !empty($options['sender']) ? $options['sender'] : '=?UTF-8?B?'.base64_encode($this->modx->getOption('campaigner.default_name')).'?=' );
			//$mailer->set(modMail::MAIL_SENDER, !empty($options['sender']) ? $options['sender'] : 'news@bundesliga.at' );

			//$mailer->set(modMail::MAIL_SENDER, !empty($options['sender']) ? $options['sender'] : $this->modx->getOption('campaigner.default_name'));
            $mailer->address('reply-to', $this->modx->getOption('campaigner.return_path'));
            $mailer->mailer->SMTPSecure = $this->modx->getOption('campaigner.mail_smtp_prefix');
            return $mailer;
        }

        $mailer = new modPHPMailer($this->modx, array(
            // modMail::PHPMAILER_LANG => 'de',
            modMail::MAIL_CHARSET => $this->modx->getOption('campaigner.mail_charset'),	
            modMail::MAIL_ENCODING => $this->modx->getOption('campaigner.mail_encoding'),	
            modMail::MAIL_SMTP_AUTH => $this->modx->getOption('campaigner.mail_smtp_auth'),	
            modMail::MAIL_SMTP_HELO => $this->modx->getOption('campaigner.mail_smtp_helo'),	
            modMail::MAIL_SMTP_HOSTS => $this->modx->getOption('campaigner.mail_smtp_hosts'),	
            modMail::MAIL_SMTP_KEEPALIVE => $this->modx->getOption('campaigner.mail_smtp_keepalive'),		
            modMail::MAIL_SMTP_PASS => $this->modx->getOption('campaigner.mail_smtp_pass'),		
            modMail::MAIL_SMTP_PORT => $this->modx->getOption('campaigner.mail_smtp_port'),		
            modMail::MAIL_SMTP_PREFIX => $this->modx->getOption('campaigner.mail_smtp_prefix'),			
            modMail::MAIL_SMTP_TIMEOUT => $this->modx->getOption('campaigner.mail_smtp_timeout'),				
            modMail::MAIL_SMTP_USER => $this->modx->getOption('campaigner.mail_smtp_user'),
            modMail::MAIL_FROM => !empty($options['sender_email']) ? $options['sender_email'] : $this->modx->getOption('campaigner.default_from'),
            modMail::MAIL_FROM_NAME => !empty($options['sender']) ? $options['sender'] : $this->modx->getOption('campaigner.default_name'),
            modMail::MAIL_SENDER => !empty($options['sender']) ? $options['sender'] : $this->modx->getOption('campaigner.default_from')	    
         ));

		//echo "Verschluesselung: ".$mailer->mailer->SMTPSecure."\n";
		//echo "Authentifizierung: ".$mailer->mailer->SMTPAuth." - ".$mailer->mailer->Username." / ".$mailer->mailer->Password."\n";

        $mailer->address('reply-to', $this->modx->getOption('campaigner.return_path'));
        $mailer->mailer->CharSet = 'utf-8';
        return $mailer;   
    }

    /**
     * Textify a html message
     *
     * @access public
     * @param string $message The HTML newsletter
     * @return string The textual representation of the newsletter
     */
    public function textify($message)
    {
        $this->modx->loadClass('HTML_To_Markdown', $this->config['basePath'] . 'vendor/html-to-markdown/', true, true);

        // consider the body only
        $bodyBegin = strpos($message, '<body');
        $bodyEnd   = strpos($message, '</body>');
        if(false !== $bodyBegin && false !== $bodyEnd) {
            $message = substr($message, $bodyBegin + 5, $bodyEnd - $bodyBegin - 6);
            $message = substr($message, strpos($message, '>'));
        }
        $markdown = new HTML_To_Markdown($message);
        return $markdown;

        // $md = new \Michelf\Markdown;
        // $md_html = $md->defaultTransform($input);

        //Remove linebreaks (code) and multiple spaces (code by tab-indention)
        $message = str_replace("\n", '', $message);
        $message = preg_replace('/\t+/', '', $message);
        $message = preg_replace('/\s+/', ' ',$message);
		// now some simple rules
        $ruler = str_repeat('-', 50);
        $message = str_replace(array('<h1>'), "\n" . '# ' , $message);
        $message = str_replace(array('</h1>'), "\n" . $ruler . "\n", $message);
        $message = str_replace(array('<h2>'), "\n" . '## ' , $message);
        $message = str_replace(array('</h2>'), "\n" . $ruler . "\n", $message);
        $message = str_replace(array('<h3>'), "\n" . '### ', $message);
        $message = str_replace(array('</h3>'), "\n" . $ruler . "\n", $message);
        $message = str_replace(array('<h4>'), "\n" . '#### ', $message);
        $message = str_replace(array('</h4>'), "\n" . $ruler, $message);
		//$message = str_replace(array('<h5>', '<h6>'), '=== ', $message);
		//$message = str_replace(array('</h5>', '</h6>'), ' ===' . "\n", $message);

        $message = str_replace(array('</p>'), "\n", $message);
        
        $message = str_replace('<li>', "* ", $message);
        $message = str_replace('</td>', '', $message);
        $message = str_replace('</tr>', '<br/>', $message);

        $message = preg_replace('#<br\s*/?>#', "\n\n", $message);
        
        // $message = preg_replace('#<a\\s[^>]*href="([^"]+)"[^>]*>(.+)</a>#iUm', '\\2 ( \\1 )', $message);
        $message = preg_replace('#(^[ \\t]*$\\r?\\n){2,}#iU', "", $message);

		// kill the rest of the html stuff
        $message = strip_tags($message);
        return $message;
    }

    /**
     * Generate tracking urls
     * @param  string $html       Newsletter content
     * @param  object $newsletter Newsletter object
     * @return string             Prepared HTML
     */
    public function makeTrackingUrls ($html, $newsletter, $subscriber) {
        // 1. get all existing tracking URLs for current newsletter:
        $this->getTrackingUrls($newsletter->get('id'));
        
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        // $aTags = $doc->find('[title^=Download:]');
        // $aTags = $doc->getElementsByTagName('a');
        $xpath = new DOMXPath($doc);
        $aTags = $xpath->query('//a');
        foreach($aTags as $aTag) {
            $text = $aTag->nodeValue;
            $url = $aTag->getAttribute('href');
            
            // URL is empty => skip
            if(empty($url) || $url == '')
                continue;

            if(strpos($url, 'mailto:') !== false)
                continue;

            if(strpos($url, '[[') !== false)
                continue;

            $aTag->setAttribute('href', $this->makeUrl($url, $newsletter->get('id'), 'click', $subscriber));
        }
        return $doc->saveHTML();
    }
    
    /**
     * [makeUrl description]
     * @param  [type] $url          [description]
     * @param  [type] $newsletterID [description]
     * @param  string $type         [description]
     * @return [type]               [description]
     */
    public function makeUrl ($url,$newsletterID, $type='click', $subscriber) {
        if ( $type == 'image' ) {
            $this->getTrackingUrls($newsletterID);
        }
        if ( empty($this->created_urls[$url]) ){

            $link = $this->modx->newObject('NewsletterLink');
            $link->set('url', $url);
            $link->set('newsletter', $newsletterID);
            $link->set('type', $type);
            $link->save();

            $this->created_urls[$url] = $link->get('id');
            if ( $this->debug ){
                $this->modx->log(modX::LOG_LEVEL_ERROR,'CampaignerNewsletter->makeUrl() - LinkID: '.$link->get('id').' URL: '.$url );
            }
        }
        if ( $type == 'image' ) {
            $url = $this->modx->getOption('site_url').$this->modx->getOption('assets_url').'components/groupeletters/?t='.base_convert($this->created_urls[$url],10,36).'|'. $subscriber->get('key') .'&amp;';
        } else {
            $url = $this->modx->makeUrl($this->modx->getOption('campaigner.tracking_page', '', 1),'', array('t' => base_convert($this->created_urls[$url],10,36).'|' . $subscriber->get('key')), 'full');
        }
        // $url = str_replace(array('%5B%5B%2B','%5B%5B&#43;', '%5B%5B', '%5D%5D'), array('[[+', '[[+', '[[', ']]'), $url);
        return str_replace('https://', 'http://', $url);
    }
    
    /**
     * [getTrackingUrls description]
     * @param  [type] $newsletterID [description]
     * @return [type]               [description]
     */
    protected function getTrackingUrls($newsletterID) {
        $savedList = $this->modx->getCollection('NewsletterLink', array('newsletter' => $newsletterID ) );
        $this->created_urls = array();
        foreach ( $savedList as $aLink ) {
            $tmp = $aLink->toArray();
            $this->created_urls[$tmp['url']] = $tmp['id'];
        }
    }

    /**
     * [logAction description]
     * @param  string $type [description]
     * @return [type]       [description]
     */
    public function logAction($type='click') {
        $t = $key = $link_id = 0;
        // Nothing set => Return
        if ( !isset($_REQUEST['t']) && $type == 'click' )
            return;

        if ( isset($_REQUEST['t']) ) {
            list($t, $key ) = explode('|',$_REQUEST['t'], 2 );
            $link_id = base_convert($t, 36, 10);
        }

        if ( $this->debug )
            $this->modx->log(modX::LOG_LEVEL_ERROR,'Newsletter->logAction() - LinkID: '.$link_id.' type: '.$type );

        if ( is_numeric($link_id) ) {
            // get the link:
            $conditions = array('id' => $link_id, 'type' => $type );
            $link = $this->modx->getObject('NewsletterLink', $conditions );
            
            if ( is_object($link) ) {
                $placeholders = array();
                // get the subscriber:
                $subscriber = $this->modx->getObject('Subscriber', array('key' => $key) );

                if ( is_object($subscriber) ){
                    // log the click:
                    $click = $this->modx->getObject('SubscriberHits', array('link' => $link_id, 'subscriber' => $subscriber->get('id')));
                    if ( is_object($click) ) {
                        // it all ready has been recorded:
                        $click->set('view_total', $click->get('view_total')+1);
                    } else {
                        $click = $this->modx->newObject('SubscriberHits');

                        //Test if it is a shared client
                        // if (!empty($_SERVER['HTTP_CLIENT_IP'])){
                        //     $ip=$_SERVER['HTTP_CLIENT_IP'];
                        // //Is it a proxy address
                        // } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
                        //   $ip= $_SERVER['HTTP_X_FORWARDED_FOR'];
                        // }else{
                        //   $ip= $_SERVER['REMOTE_ADDR'];
                        // }
                        //The value of $ip at this point would look something like: "192.0.34.166"
                        // $_SERVER['REMOTE_ADDR'] = '193.83.128.238';
                        $_SERVER['REMOTE_ADDR'] = '8.8.8.8';
                        $ip = ip2long($_SERVER['REMOTE_ADDR']);
                        // var_dump($_SERVER);
                        // Track sharing
                        $shares = array(
                            'facebook'  => 'http://www.facebook.com',
                            'twitter'   => 'http://www.twitter.com',
                            );
                        foreach($shares as $key => $value) {
                            if(strpos($link->get('url'), $value) !== FALSE)
                                $type = $key;
                        }

                        // Get the geo-location
                        $geo_attr = array(
                            'key' => '7284bdc956d8d0c4a9283d5fa0676ad7c6df84c389c7c85f6f8a9fe2d809c459',
                            'format' => 'json',
                            'ip' => $_SERVER['REMOTE_ADDR'],
                            );
                        $geo_url = 'http://api.ipinfodb.com/v3/ip-city/?' . http_build_query($geo_attr);
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_RETURNTRANSFER => 1,
                            CURLOPT_URL => $geo_url,
                        ));
                        $geo_loc = json_decode(curl_exec($curl));
                        curl_close($curl);

                        $result = false;
                        if($geo_loc->statusCode === 'OK')
                            $result = json_encode($geo_loc);

                        $data = array(
                                'newsletter' => $link->get('newsletter'),
                                'subscriber' => $subscriber->get('id'),
                                'link' => $link_id,
                                'hit_type' => $type,
                                'hit_date' => date('Y-m-d H:i:s'),
                                'view_total' => 1,
                                'client' => $_SERVER['HTTP_USER_AGENT'],
                                'ip' => $ip,
                                'loc' => $result,
                            );
                        $click->fromArray($data);
                    }
                    $click->save();
                    $placeholders = $subscriber->toArray();
                }
                
                // set cookies?
                if ( $type == 'click' ) {
                    // set all info and process the string:
                    $chunk = $this->modx->newObject('modChunk');
                    $chunk->setContent($link->get('url'));
                    $url = $chunk->process($placeholders);

                    // add image(open) stat if not already:
                    $conditions = array('newsletter' => $link->get('newsletter'), 'url' => 'trackingImage', 'type' => 'image' );
                    $image = $this->modx->getObject('NewsletterLink', $conditions );
                    
                    if ( is_object($image) && is_object($subscriber) ){
                        $opened = $this->modx->getObject('SubscriberHits', array('link' => $image->get('id'), 'subscriber' => $subscriber->get('id')));
                        if ( is_object($opened) ) {
                            // it all ready has been recorded so no need to do anything
                        } else {
                            $opened = $this->modx->newObject('SubscriberHits');
                            $data = array(
                                    'newsletter' => $link->get('newsletter'),
                                    'subscriber' => $subscriber->get('id'),
                                    'link' => $image->get('id'),
                                    'hit_type' => 'image',
                                    'hit_date' => date('Y-m-d H:i:s'),
                                    'view_total' => 1,
                                );
                            $opened->fromArray($data);
                            $opened->save();
                        }
                    }
                    
                    $c = $this->modx->newQuery('modResource');
                    $c->leftJoin('Newsletter', 'Newsletter', '`modResource`.`id` = `Newsletter`.`docid`');
                    $c->leftJoin('Autonewsletter', 'Autonewsletter', '`modResource`.`id` = `Autonewsletter`.`docid`');
                    $c->select(array(
                        'pagetitle'  => $this->modx->getSelectColumns('modResource', 'modResource', '', array('id', 'pagetitle'))
                        ));
                    $c->where(array('Newsletter.id' => $link->get('newsletter')));
                    $c->where(array('Autonewsletter.id' => $link->get('newsletter')), xPDOQuery::SQL_OR);
                    $newsletter = $this->modx->getObject('modResource', $c);
                    // $url .= strpos($url, '?') === FALSE ? '?' : '&';
                    // if(is_object($newsletter))
                    //     $url .= 'campaign='.urlencode(str_replace(' ', '-', $newsletter->get('pagetitle')));
                    // echo 'URL ' . $url;
                    $this->modx->sendRedirect($url);
                }
            }
        }

        if ( $type == 'image') {
            // show the image:
            $display_name = 'clear';
            if ( isset($_GET['image']) ) {
                $filename = $display_name = $_GET['image'];
                $filename = str_replace(array('../', './', '%2F','.php', '.inc', chr(0)), '', $filename);
                $filename = MODX_ASSETS_PATH.'components'.DIRECTORY_SEPARATOR.'campaigner'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$filename;
                if ( !file_exists($filename) ) {
                    $filename = 'clear.gif';
                }
            } else {
                $filename = MODX_ASSETS_PATH.'components'.DIRECTORY_SEPARATOR.'campaigner'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'sent.png';
            }
            
            $filesize = filesize($filename);
            
            # get the type of file
            $file_ext = substr($filename, strripos($filename, '.')+1 );
            
            $mime = '';
            switch ($file_ext) {
                case 'jpeg':
                case 'jpg':
                    $mime = 'image/jpeg';
                    break;
                case 'png':
                    $mime = 'image/png';
                    break;
                case 'tif':
                case 'tiff':
                    $mime = 'image/tiff';
                    break;
                case 'gif':
                default:
                    $mime = 'image/gif';
                    break;
            }
            
            // /** 
            // header_remove("Pragma");
            // header_remove("Cache-Control");
            // header_remove("Expires");
            // header_remove("Set-Cookie");
            // */
            // header("Content-type: " . $mime );//.$mime_type );
            // header("Content-length: ".$filesize);
            //header("Content-Transfer-Encoding: binary");
            //header('Accept-Ranges: bytes');
            
            /* Read It */
            $handle = fopen($filename, "rb");
            $contents = fread($handle, $filesize);
            fclose($handle);
            
            //$this->modx->log(modX::LOG_LEVEL_ERROR,'EletterNewsletter->logAction() headers: '. print_r(headers_list(),TRUE) );
            /* Print It */
            echo $contents;
            // $this->modx->log(modX::LOG_LEVEL_ERROR,'EletterNewsletter->logAction() filename: '.$filename );
            //exit;
        }
    }

    /**
     * [getGroups description]
     * @param  [type] $email [description]
     * @return [type]        [description]
     */
    public function getGroups($email = false) {

        if(!$email)
            return $this->modx->getCollection('Group', array('public' => 1));

        $c = $this->modx->newQuery('Subscriber');
        $c->where(array(
            'email' => $email
            ));

        // they have to be in the group
        $c->innerJoin('GroupSubscriber', 'GroupSubscriber', '`GroupSubscriber`.`subscriber` = `Subscriber`.`id`');
        $c->leftJoin('Group', 'Group', '`Group`.id = `GroupSubscriber`.`group`');
        
        $c->select($this->modx->getSelectColumns('Subscriber', 'Subscriber', '', array('id')));
        $c->select($this->modx->getSelectColumns('Group', 'Group', 'g_'));
        $c->select($this->modx->getSelectColumns('GroupSubscriber', 'GroupSubscriber', 'gs_'));
        // $c->select(array(
        //     'group_id' => '`GroupSubscriber`.`group`',
        //     'group_name' => '`Group`.`name`',
        //     )
        // );
        // $c->prepare();
        // echo $c->toSql();
        //$this->modx->log($this->modx->LOG_LEVEL_ERROR, "SQL NEWSLETTER QUERY --> " . $c->toSql());
        return $this->modx->getCollection('Subscriber', $c);
    }

    /**
     * Send system emails if enabled
     *
     * Sends emails with useful information on the just processed action
     * if the system setting ('campaigner.system_mails') is set to true.
     * Setting can be overruled to force specific system mails
     *
     * Parameter for $options:
     * * overrule - Overrules the system setting to force this mail
     * * recipients - A comma-separated list of recipients to receive this mail
     * * subject - The subject of this system mail
     * * body - The prepared content of this mail
     * 
     * @param  array  $options Optional properties
     * @return bool          Either true or false
     */
    public function sendSystemMail($options = array()) {
        $system_mails = $this->modx->getOption('campaigner.system_mails');
        $mail_addresses = explode(',', $this->modx->getOption('campaigner.system_mail.addresses', '', $options['recipients']));
        if($options['overrule'])
            $system_mails = true;
        if(!$system_mails)
            return;
        $mailer = $this->getMailer();
        foreach($mail_addresses as $address) {
            $mailer->address('to', $address);    
        }
        $mailer->setHtml(true);

        if(is_array($options['body'])) {
            $content = $options['body']['html'];
        } else {
            $content = '<html><head><base href="' . $this->modx->getOption('site_url') . '"/></head><body>';
            $content .= nl2br($options['body']);
            $content .= '</body></html>';
        }

        $mailer->set(modMail::MAIL_SUBJECT, $options['subject']);
        $mailer->set(modMail::MAIL_BODY, $content);
        $mailer->send();
        $mailer->mailer->ClearAllRecipients();
        $mailer->mailer->ClearCustomHeaders();
    }
    /**
     * Get calendar for upcoming campaigns
     * @return ICS Calendar (ICS) items
     */
    public function getCalendar()
    {
        header('Content-Type: text/calendar');
        // Get the Autonewsletter elements
        $items = $this->modx->getCollection('Autonewsletter', array('id' => 2));
        $head =<<<CAL_HEAD
BEGIN:VCALENDAR\r\n
X-WR-CALNAME:AUTO-NL
X-WR-CALDESC:Autonewsletter zur Anzeige im Kalender
VERSION:2.0\r\n
PRODID:-//hacksw/handcal//NONSGML v1.0//EN\r\n
CAL_HEAD;
        $foot =<<<CAL_FOOT
END:VCALENDAR
CAL_FOOT;

        foreach($items as $item) {
            while ($i <= 10) {
                $j++;
                if($item->get('start') + ($item->get('frequency') * $j) < time())
                    continue;
                $list[] = array('start' => date('D, d.m.Y', $item->get('start') + ($item->get('frequency') * $j)));
                // $start = date('Ymd\THis', $item->get('start') + ($item->get('frequency') * $j));
                $start = date('Ymd', $item->get('start') + ($item->get('frequency') * $j));
                $title = 'AUTO-NL ' . $item->get('id');
                $o[] =<<<CAL_ITEM
BEGIN:VEVENT\r\n
UID:$start@example.com\r\n
DTSTAMP:$start\r\n
#ORGANIZER;CN=John Doe:MAILTO:john.doe@example.com\r\n
DTSTART:$start\r\n
DTEND:$start\r\n
SUMMARY:$title\r\n
END:VEVENT\r\n
CAL_ITEM;
                $i++;
            }
        }
        // var_dump($o);
        echo $head . implode("\r\n", $o) . $foot;
    }

    /**
     * Gets a Chunk and caches it; also falls back to file-based templates
     * for easier debugging.
     *
     * @access public
     * @param string $name The name of the Chunk
     * @param array $properties The properties for the Chunk
     * @return string The processed content of the Chunk
     */
    public function getChunk($name,$properties = array()) {
        $chunk = null;
        if (!isset($this->chunks[$name])) {
            $chunk = $this->_getTplChunk($name);
            if (empty($chunk)) {
                $chunk = $this->modx->getObject('modChunk',array('name' => $name),true);
                if ($chunk == false) return false;
            }
            $this->chunks[$name] = $chunk->getContent();
        } else {
            $o = $this->chunks[$name];
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($o);
        }
        $chunk->setCacheable(false);
        return $chunk->process($properties);
    }
    /**
     * Returns a modChunk object from a template file.
     *
     * @access private
     * @param string $name The name of the Chunk. Will parse to name.chunk.tpl
     * @return modChunk/boolean Returns the modChunk object if found, otherwise
     * false.
     */
    private function _getTplChunk($name) {
        $chunk = false;
        $f = $this->config['chunksPath'].strtolower($name).'.chunk.tpl';
        if (file_exists($f)) {
            $o = file_get_contents($f);
            $chunk = $this->modx->newObject('modChunk');
            $chunk->set('name',$name);
            $chunk->setContent($o);
        }
        return $chunk;
    }
}