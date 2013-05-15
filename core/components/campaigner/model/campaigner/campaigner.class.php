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
        $assetsUrl = $this->modx->getOption('campaigner.assets_url',$config,$this->modx->getOption('assets_url').'components/campaigner/');
        $this->config = array_merge(array(
            'basePath'       => $basePath,
            'corePath'       => $basePath,
            'modelPath'      => $basePath.'model/',
            'processorsPath' => $basePath.'processors/',
            'chunksPath'     => $basePath.'elements/chunks/',
            'jsUrl'          => $assetsUrl.'js/',
            'baseUrl'        => $assetsUrl,
            'cssUrl'         => $assetsUrl.'css/',
            'assetsUrl'      => $assetsUrl,
            'connectorUrl'   => $assetsUrl.'connector.php'
            ),$config);

        // package and lexicon
        $this->modx->addPackage('campaigner', $this->config['modelPath'], 'camp_');
        $this->modx->lexicon->load('campaigner:default');
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
            return $this->reason;

        // if(count($this->errormsg) > 0)
        //     return false;

        $groups = $data['groups'];
        unset($data['groups']);

        var_dump($groups);
        
        // new subscriber
        $subscriber     = $this->modx->newObject('Subscriber');
        $data['key']    = md5(time() . substr($data['email'], rand(1,3), rand(-4, -1)));
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
                    var_dump($_grpsub);
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
        $mailer->address('to', $subscriber->get('email') );
        
        if (!$mailer->send()) {
           $this->modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the confirmation email to '.$subscriber->get('email') .' ### '. $mailer->mailer->ErrorInfo);
       }

       return true;
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
     * Move sheduled autonewsletters to newsletters
     *
     * @access public
     * @return bool
     */
    public function sheduleAutoNewsletter()
    {
		//$fileHandlerShedule=fopen("/var/www/modx/buli/v1.1/core/components/campaigner/send_log.txt",a);
		//fwrite($fileHandlerShedule,"Start sheduleAutoNewsletter [".date('d.m.Y H:i:s',time())."]:\n");

        // the newsletters
        $c = $this->modx->newQuery('Autonewsletter');
        $c->where('`Autonewsletter`.`state` = 1 AND UNIX_TIMESTAMP() > (GREATEST((`Autonewsletter`.`start`), COALESCE(`Autonewsletter`.`last`, 0)) + `Autonewsletter`.`frequency` + TIME_TO_SEC(`Autonewsletter`.`time`))');

		//$c->prepare(); var_dump($c->toSQL()); die;
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

                // duplicate document into newsletter folder
    			//plus 3600 at the end: for example "14:00:00" results in 13 hours
            $timestamp = max($newsletter->get('last'), $newsletter->get('start')) + $newsletter->get('frequency') + strtotime('1970-01-01 ' . $newsletter->get('time'))+3600;
            $newDoc   = $document->duplicate(array(
                'parent'  => $this->modx->getOption('campaigner.newsletter_folder'),
                'newName' => $this->parsePagetitle($document->get('pagetitle'), $timestamp)
                ));
                // new alias because we cant set it
            if(false === strpos($document->get('pagetitle'), '{')) {
                $newDoc->set('alias', $document->get('alias') .'-'. date('Ymd', $timestamp));
            }

            $newDoc->set('publishedon', $timestamp);
            $newDoc->save();

                // create the newsletter
            $newNl = $this->modx->newObject('Newsletter');
            $newNl->fromArray(array(
                'docid'        => $newDoc->get('id'),
                'state'        => 1,
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
	   //fwrite($fileHandlerShedule,"End sheduleAutoNewsletter [".date('d.m.Y H:i:s',time())."]:\n");
	   //fclose($fileHandlerShedule);
        return true;
    }

    /**
     * Create the email queue
     *
     * @access public
     * @return int Number of entrys added to the queue
     */
    public function createQueue()
    {
		//$fileHandlerCreate=fopen("/var/www/modx/buli/v1.1/core/components/campaigner/send_log.txt",a);
		//fwrite($fileHandlerCreate,"Start createQueue [".date('d.m.Y H:i:s',time())."]:\n");

        // create the query for the newsletter
        $c = $this->modx->newQuery('Newsletter');
        $c->innerJoin('modDocument', 'modDocument', '`modDocument`.`id` = `Newsletter`.`docid`');
        $c->where('`Newsletter`.`state` = 1');
        #$c->where(array('sent_date:IS' => null));
        $c->where('((sent_date IS NULL AND `modDocument`.`publishedon` < UNIX_TIMESTAMP() AND `modDocument`.`publishedon` > 0) OR (`modDocument`.`pub_date` < UNIX_TIMESTAMP() AND `modDocument`.`publishedon` < `modDocument`.`pub_date` AND `modDocument`.`pub_date` > 0))');
        $c->sortby('`Newsletter`.`priority`', 'ASC');
		$c->sortby('`modDocument`.`publishedon`', 'ASC'); // first document first
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
        // echo 'THE COMPOSED CONTENT ' . $composedNewsletter . "\n";
        $cacheElementKey = 'newsletter/' . $document->get('id');
        // echo 'RES ID ' . $document->get('id') . "\n";
        $this->modx->cacheManager->set($cacheElementKey, $composedNewsletter, 0, $cacheOptions);
            // echo 'Cache File created!';


        // store the composed newsletter
        // $document->setContent($composedNewsletter);
        // $document->set('template', 0);
        // if(!$document->save()) {
        //     $this->modx->log(modX::LOG_LEVEL_ERROR, 'An error occurred while saving composing newsletter.');
        // }
        
        // fixes some kind of odd bug, not letting me delete the template in 2.0.4
        // $this->modx->query('UPDATE `modx_site_content` SET `template` = "0" WHERE `id` = '. $document->get('id'));

        // now subscribers
        $c = $this->modx->newQuery('Subscriber');
        // they have to be in the group
        $c->innerJoin('GroupSubscriber', 'GroupSubscriber', '`GroupSubscriber`.`subscriber` = `Subscriber`.`id`');
        $c->innerJoin('NewsletterGroup', 'NewsletterGroup', '`NewsletterGroup`.`group` = `GroupSubscriber`.`group` AND `NewsletterGroup`.`newsletter` = '. $newsletter->get('id'));
        $c->leftJoin('Group', 'Group', '`Group`.id = `NewsletterGroup`.`group`');
		$c->where(array('active:=' => 1)); // only active ones
        $c->select('`Subscriber`.`id`');
        $c->groupby('`Subscriber`.`id`'); // only send once per subscriber
        $c->sortby('`Group`.`priority`', 'ASC');
        $c->prepare();
		//$this->modx->log($this->modx->LOG_LEVEL_ERROR, "SQL NEWSLETTER QUERY --> " . $c->toSql());
        $subscribers = $this->modx->getCollection('Subscriber', $c);
        
		//fwrite($fileHandlerCreate,"build queue for [".$document->get('pagetitle')."]:\n");
        // build queue
        $count = 0;
        foreach($subscribers as $subscriber) {
            $queueItem = $this->modx->newObject('Queue');
            $queueItem->fromArray(array(
                'newsletter' => $newsletter->get('id'),
                'subscriber' => $subscriber->get('id'),
                'state'      => 0,
                'priority'   => $newsletter->get('priority')
                ));
            $queueItem->save();
			//md5 verschluesselte QueueID als key-Attribut mitspeichern
			//fwrite($fileHandlerCreate,"CREATE KEY FOR QUEUE ELEMENT ".$queueItem->get('id')." [".md5($queueItem->get('id'))."]:\n");
            $queueItem->set('key', md5($queueItem->get('id')));
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
        // save newsletter composal date
        $newsletter->set('sent_date', time());
        $newsletter->set('total', $count);
        if(!$newsletter->save()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'An error occurred while saving the newsletter.');
        }
		//fwrite($fileHandlerCreate,"End createQueue [".date('d.m.Y H:i:s',time())."]:\n");
        //fclose($fileHandlerCreate);
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

        $c->limit($this->modx->getOption('campaigner.batchsize'));

        //Nimm alle Elemente aus der Queue die noch zu senden sind (0) oder ein Resend sind (8)
        $c->where('`Queue`.`state`=0 OR `Queue`.`state`=8');

        //$c->where('`Queue`.`state` = 0');
        $c->leftJoin('Subscriber');
        $c->select('`Queue`.*, `Queue`.`key` AS queue_key, `Subscriber`.`firstname`, `Subscriber`.`lastname`, `Subscriber`.`email`, `Subscriber`.`text`, `Subscriber`.`key`, `Subscriber`.`address`, `Subscriber`.`title`');
        $c->sortby('`Queue`.`priority`');
        $c->prepare();
        //$this->modx->log($this->modx->LOG_LEVEL_ERROR, "SQL PROCESS QUEUE QUERY --> " . $c->toSql());

        $queue = $this->modx->getCollection('Queue', $c);

        $ids = array();
        foreach($queue as $item) {
          echo $item->get('id') . ' - ' . $item->get('email') . $item->get('state') . "\n";
          $ids[] = $item->get('id');
        }

        $this->modx->query('UPDATE `camp_queue` SET `state` = 3 WHERE `id` IN('. implode(',', $ids) .')');	

        $sumTime = 0;
        $cnt = 0;

        // and process each one of them
        $newsletter = null;
        foreach($queue as $item) {
            //Testing - only sends to the 2 first recipients
            // if($cnt > 1) break;
            	// Start time
            $mtime = microtime(); 
            $mtime = explode(" ",$mtime); 
            $mtime = $mtime[1] + $mtime[0]; 
            $starttime = $mtime;

            $start_sec = (float)$mtime;

    			// don't fetch allready loaded newsletters
            if(!$newsletter || $newsletter->get('id') !== $item->get('newsletter')) {
                $c = $this->modx->newQuery('Newsletter');
                $c->innerJoin('modDocument', 'modDocument', '`modDocument`.`id` = `Newsletter`.`docid`');
                $c->where('`Newsletter`.`id` = '. $item->get('newsletter'));
                $c->select('`Newsletter`.*, `modDocument`.`content`, `modDocument`.`pagetitle`, `modDocument`.`publishedon`');
    				//$c->prepare(); var_dump($c->toSql()); die;
                $newsletter = $this->modx->getObject('Newsletter', $c);
                $mailer = $this->getMailer(array(
                   'sender' => $newsletter->get('sender'),
                   'sender_email' => $newsletter->get('sender_email')
                   ));
                $mailer->set(modMail::MAIL_SUBJECT, $newsletter->get('pagetitle'));
                $tags = $this->getNewsletterTags($newsletter);
            }

			// compose message
            $subscriber = $this->modx->newObject('Subscriber', $item->toArray());

            /**
             * Get newsletter via cache resource
             * @since 2013-04-11
             */
            $cacheOptions = array(
                xPDO::OPT_CACHE_KEY => '',
                xPDO::OPT_CACHE_HANDLER => 'xPDOFileCache',
                xPDO::OPT_CACHE_EXPIRES => 0,
                );
            $content = $this->modx->cacheManager->get('newsletter/' . $newsletter->get('docid'), $cacheOptions);

            if ( (boolean) $this->modx->getOption('campaigner.tracking_enabled', '', FALSE) )
                $this->makeUrl('trackingImage', $newsletter->get('id'), 'image');

            $message = $this->makeTrackingUrls($content, $newsletter);
            $message = $this->processNewsletter($message, $subscriber, $tags);
            
			// $message = $this->processNewsletter($newsletter->get('content'), $subscriber, $tags);
            $textual = $this->textify($message);

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
			$mailer->address('to', $subscriber->get('email') );
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
			
			// and send
			if (!$mailer->send()) {
				//If the mail was not sent, set state to 0.
				//$item->set('state', 0);
				$this->modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the an email to '.$subscriber->get('email') .' ### '. $mailer->mailer->ErrorInfo);
			} else {
				//End time
				$mtime = microtime();
				$mtime = explode(" ",$mtime); 
				$mtime = $mtime[1] + $mtime[0]; 
				$endtime = $mtime; 
				$totaltime = ($endtime - $starttime);

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
					$item->set('state', 1);
				}
				$item->set('sent', time());
			}
			
			// partial reset
			$mailer->mailer->ClearAllRecipients();
			$mailer->mailer->ClearCustomHeaders();

			$item->save();
			//$item->remove();
			$sumTime += $totaltime;
			$cnt++;
        }

        if($cnt > 0) {
            /**
            * Feed the manager log
            */
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
        }
        //fclose($fileHandler);
       return;
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
        // $this->modx->getParser();

        // if($this->modx->context->key != $document->context_key) {
        //     $this->modx->switchContext($document->context_key);
        // }

        // $template = $document->getOne('Template');
        // $document->content = $this->parseCampaignerTags($document->content);

        // if(!$template) {
        //     $message = $document->content;
        // 	// run the parser
        //     $maxIterations= intval($this->modx->getOption('parser_max_iterations', $options, 10));
        //     $this->modx->parser->processElementTags('', $message, true, false, '[[', ']]', array(), $maxIterations);
        //     $this->modx->parser->processElementTags('', $message, true, true, '[[', ']]', array(), $maxIterations);
        //     return $this->unparseCampaignerTags($message);
        // }

        // // strip campaigner tags from content
        // $template = $this->parseCampaignerTags($template->content);

        // // dirty fix to be able to partse a template
        // $template = str_replace('[[*', '[[+', $template);
        // $this->modx->setPlaceholders($document->toArray());

        // // run the parser
        // $maxIterations= intval($this->modx->getOption('parser_max_iterations', $options, 10));
        // //Fuer Newsetter Template NEU und Newsletter Template NEU BL1 und BL2
        
        // // $template = str_replace("[[mapGetArray]]", "[[mapGetArray? &newsletter_id=`".$document->id."`]]", $template);
        // //Fuer Newsletter Template BL2 NEU
        // // $template = str_replace("[[mapGetArray? &league=`BL2`]]", "[[mapGetArray? &league=`BL2` &newsletter_id=`".$document->id."`]]", $template);	
        // $this->modx->parser->processElementTags('', $template, true, false, '[[', ']]', array(), $maxIterations);
        // $this->modx->parser->processElementTags('', $template, true, true, '[[', ']]', array(), $maxIterations);
        // $template = $this->unparseCampaignerTags($template);

        // return $template;
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
        $this->modx->unsetPlaceholders(array_keys($allTags));
        $this->modx->setPlaceholders($allTags);
        
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
        
        $salutation = $this->modx->getOption('campaigner.salutation') . ' ' . ($subscriber->get('firstname') && $subscriber->get('lastname') ? $subscriber->get('firstname') . ' ' . $subscriber->get('lastname') : $subscriber->get('email'));
        
        $params = array(
            'subscriber' => $subscriber->get('email'),
            'key' => $subscriber->get('key'),
            'letter' => $subscriber->get('newsletter'),
            );
        
        return array(
            'campaigner.email'       => $subscriber->get('email'),
            'campaigner.address'     => $subscriber->get('address'),
            'campaigner.title'       => $subscriber->get('title'),
            'campaigner.salutation'  => $salutation,
            'campaigner.firstname'   => $subscriber->get('firstname'),
            'campaigner.lastname'    => $subscriber->get('lastname'),
            'campaigner.unsubscribe' => $this->modx->makeUrl($this->modx->getOption('campaigner.unsubscribe_page'), '', $params, 'full'),
            'campaigner.istext'      => $subscriber->get('text') ? 1 : null,
            'campaigner.key'         => $subscriber->get('key'),
            'campaigner.tracking_image' => $this->modx->getOption('site_url').'assets/components/campaigner/?t='.base_convert($this->created_urls['trackingImage'],10,36).'|[[+campaigner.key]]&amp;',
            );
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
        return array(
            'campaigner.total'        => $newsletter->get('total'),
            'campaigner.sender'       => $newsletter->get('sender') ? $newsletter->get('sender') : $this->modx->getOption('campaigner.default_name'),
            'campaigner.sender_email' => $newsletter->get('sender_email') ? $newsletter->get('sender_email') : $this->modx->getOption('campaigner.default_from'),
            'campaigner.date'         => date('d.m.Y H:i', $newsletter->get('publishedon')),
            'campaigner.send_date'    => date('d.m.Y', $newsletter->get('sent_date')),
            'campaigner.letter'       => $newsletter->get('id'),
            );
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
        // consider the body only
        $bodyBegin = strpos($message, '<body');
        $bodyEnd   = strpos($message, '</body>');
        if(false !== $bodyBegin && false !== $bodyEnd) {
            $message = substr($message, $bodyBegin + 5, $bodyEnd - $bodyBegin - 6);
            $message = substr($message, strpos($message, '>'));
        }

        //Remove linebreaks (code) and multiple spaces (code by tab-indention)
        $message = str_replace("\n", '', $message);
        $message = preg_replace('/\s+/', ' ',$message);
		// now some simple rules
        $message = str_replace(array('<h1>'), "\n" . '-----------------------------------------------------------------------------------------------------------------------' . "\n", $message);
        $message = str_replace(array('</h1>'), "\n" . '-----------------------------------------------------------------------------------------------------------------------' . "\n", $message);
        $message = str_replace(array('<h2>'), '' . "\n", $message);
        $message = str_replace(array('</h2>'), "\n" . '' . "\n", $message);
        $message = str_replace(array('<h3>'), '---------------------------------------------------------' . "\n", $message);
        $message = str_replace(array('</h3>'), "\n" . '---------------------------------------------------------' . "\n\n", $message);
        $message = str_replace(array('<h4>'), "\n", $message);
        $message = str_replace(array('</h4>'), "\n" . '---------------------------------------------------------', $message);
		//$message = str_replace(array('<h5>', '<h6>'), '=== ', $message);
		//$message = str_replace(array('</h5>', '</h6>'), ' ===' . "\n", $message);

        $message = str_replace(array('</p>'), "\n", $message);
        $message = str_replace(array('<br>', '<br />'), "\n", $message);
        $message = str_replace('<li>', "    - ", $message);
        $message = str_replace('</td>', "\n", $message);
        $message = str_replace('</tr>', "\r\n", $message);

        $message = preg_replace('#<a\\s[^>]*href="([^"]+)"[^>]*>(.+)</a>#iUm', '\\2 ( \\1 )', $message);
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
    public function makeTrackingUrls ($html, $newsletter) {
        // 1. get all existing tracking URLs for current newsletter:
        $this->getTrackingUrls($newsletter->get('id'));
        
        // 2. Convert all links to /tiny/Link id(base 64)/Person ID(base 64?) -> email_links
        $link_list = explode("<a ",$html);
        $tracking_email = NULL;
        
        foreach ($link_list as $line ) {
            // get the first occurance of href
            $position = strpos($line, 'href="');
            if ( $position === false ) {
                // no link:
                $tracking_email .= $line;
                continue;
            }
            $before = substr($line, 0, $position );
            $line = substr($line, ($position+6) );// remove the href=" and everything before it
            $end_quote_position = strpos($line, '"');// get the postion of the next " 
            $after = substr($line, ($end_quote_position+1) );
            $href = substr($line, 0, $end_quote_position );
            
            // strpos($href, 'http') !== false
            if ( strpos($href, 'mailto:') === false  && strpos($href, '[[') === false ) {
            
            } else {
                // mailto link:
                $tracking_email .= '<a '.$line;
                continue;
            }
            // now reconstruct the link_str:
            $tracking_email .= '<a '.$before.' href="'.$this->makeUrl($href, $newsletter->get('id')).'"'.$after;
        }
        if ( empty($tracking_email) ) {
            $tracking_email = $html;
        }
        return $tracking_email;
    }
    
    /**
     * [makeUrl description]
     * @param  [type] $url          [description]
     * @param  [type] $newsletterID [description]
     * @param  string $type         [description]
     * @return [type]               [description]
     */
    public function makeUrl ($url,$newsletterID, $type='click') {
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
            $url = $this->modx->getOption('site_url').$this->modx->getOption('assets_url').'components/groupeletters/?t='.base_convert($this->created_urls[$url],10,36).'|[[+campaigner.key]]&amp;';
        } else {
            $url = $this->modx->makeUrl($this->modx->getOption('campaigner.tracking_page', '', 1),'', array('t' => base_convert($this->created_urls[$url],10,36).'|[[+campaigner.key]]'), 'http');
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
                        $data = array(
                                'newsletter' => $link->get('newsletter'),
                                'subscriber' => $subscriber->get('id'),
                                'link' => $link_id,
                                'hit_type' => $type,
                                'hit_date' => date('Y-m-d H:i:s'),
                                'view_total' => 1,
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
                    // add campaign/newsletter title for analytics:
                    // 
                    // getCollectionGraph('Box', '{"BoxColors":{"Color":{}}}', array('Box.width' => 40));
                    // 
                    
                    $c = $this->modx->newQuery('modResource');
                    $c->leftJoin('Newsletter', 'Newsletter', '`modResource`.`id` = `Newsletter`.`docid`');
                    $c->leftJoin('Autonewsletter', 'Autonewsletter', '`modResource`.`id` = `Autonewsletter`.`docid`');
                    $c->select(array(
                        'pagetitle'  => $this->modx->getSelectColumns('modResource', 'modResource', '', array('id', 'pagetitle'))
                        ));
                    $c->where(array('Newsletter.id' => $link->get('newsletter')));
                    $c->where(array('Autonewsletter.id' => $link->get('newsletter')), xPDOQuery::SQL_OR);
                    $newsletter = $this->modx->getObject('modResource', $c);

                    if ( strpos($url, '?') === FALSE ) {
                        $url .= '?';
                    } else {
                        $url .= '&';
                    }
                    if(is_object($newsletter))
                        $url .= 'campaign='.urlencode(str_replace(' ', '-', $newsletter->get('pagetitle')));
                    echo 'URL ' . $url;

                    // $this->modx->sendRedirect($url);
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
            // $handle = fopen($filename, "rb");
            // $contents = fread($handle, $filesize);
            // fclose($handle);
            
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