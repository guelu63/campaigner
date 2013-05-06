<?php
// validate properties
if (empty($_POST['id'])) {
    return $modx->error->failure($modx->lexicon('campaigner.newsletter.error.notfound'));
}
if (empty($_POST['email']) && empty($_POST['groups'])) {
    return $modx->error->failure($modx->lexicon('campaigner.newsletter.error.noreceiver'));
}

// get the object
$newsletter = $modx->getObject('Newsletter', array('id' => $_POST['id']));
if(!$newsletter) return $modx->error->failure($modx->lexicon('campaigner.newsletter.error.notfound'));

$document = $modx->getObject('modDocument', array('id' => $newsletter->get('docid')));
if(!$document) return $modx->error->failure($modx->lexicon('campaigner.newsletter.error.notfound'));

// start message composing
$message = $modx->campaigner->composeNewsletter($document);
$subscriber = null;

// prepare the mailer
$mailer = $modx->campaigner->getMailer(array(
    'sender_email' => $sender_email,
    'sender' => $sender
));
$mailer->set(modMail::MAIL_SUBJECT, $document->get('pagetitle'));


/**
 * Add attachment to mail
 */
$mailer = $modx->campaigner->getAttachments($mailer, $document);
// $modx->log(MODX_LOG_LEVEL_INFO, 'Attachments were successfully added!');

// $tv = $modx->getObject('modTemplateVar',array('name' => 'tvAttach'));
// if($tv) {
//     /* get the raw content of the TV */
//     $val = $tv->getValue($document->get('id'));
//     if(!empty($val)) {
//         $vals = explode(',', $val);
//         $vals = array_filter($vals, 'trim');

//         $mailer->mailer->ClearAttachments();

//         foreach($vals as $val) {
//             //echo $modx->getOption('base_path').$val;
//             $mailer->mailer->AddAttachment($modx->getOption('base_path').$val);
//         }
//     }
// }

/**
 * Do the test sending
 */
$sent = true;
if(!empty($_POST['email'])) {
    // send to a single email
    $mailer->setHTML(true);
    
    // check for personalization
    if($_POST['personalize']) {
        $subscriber = $modx->getObject('Subscriber', array('email' => $_POST['email']));
    }
    
    // the messages
    $message = $modx->campaigner->processNewsletter($message, $subscriber);
    $textual = $modx->campaigner->textify($message);
    
    // set properties
    if($subscriber && $subscriber->get('text')) {
        $mailer->setHTML(false);
        $mailer->set(modMail::MAIL_BODY, $textual);
    } else {
        $mailer->set(modMail::MAIL_BODY, $message);
    }
    $mailer->set(modMail::MAIL_BODY_TEXT, $textual);
    $mailer->address('to', $_POST['email'] );
    
    // and send!
    if (!$mailer->send()) {
        $sent = false;
        $modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the test email to '.$subscriber->get('email') .' ### '. $mailer->mailer->ErrorInfo);
    }
    $mailer->ClearAllRecipients();
    $mailer->ClearCustomHeaders();
} else {
    // send to a group of subscribers
    $c = $modx->newQuery('Subscriber');
    $c->innerJoin('GroupSubscriber', 'GroupSubscriber', '');
    $c->where('`Subscriber`.`active` = 1 AND `GroupSubscriber`.`group` IN('. implode(',', $_POST['groups']) .')');
    $c->groupby('`Subscriber`.`id`');
    $subs = $modx->getCollection('Subscriber', $c);
    
    // personalized or not
    if(!$_POST['personalize']) {
        // the messages
        $mailer->setHTML(true);
        $message = $modx->campaigner->processNewsletter($message, null);
        $textual = $modx->campaigner->textify($message);
        
        // set properties
        $mailer->set(modMail::MAIL_BODY, $message);
        $mailer->set(modMail::MAIL_BODY_TEXT, $textual);
        foreach($subs as $sub) {
            $mailer->address('to', $sub->get('email') );
        }
        
        
        // and send
        if (!$mailer->send()) {
            $sent = false;
            $modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the confirmation email to '.$subscriber->get('email'));
        }
    } else {
        // personilzed for every subscriber
        foreach($subs as $sub) {
            // the messages
            $tmpMessage = $modx->campaigner->processNewsletter($message, $sub);
            $tmpTextual = $modx->campaigner->textify($tmpMessage);
            
            // html or text and other properties
            if($sub->get('text')) {
                $mailer->setHTML(false);
                $mailer->set(modMail::MAIL_BODY, $tmpTextual);
            } else {
                $mailer->setHTML(true);
                $mailer->set(modMail::MAIL_BODY, $tmpMessage);
            }
            $mailer->set(modMail::MAIL_BODY_TEXT, $tmpTextual);
            $mailer->address('to', $sub->get('email') );
            
            // and send
            if (!$mailer->send()) {
                $sent = false;
                $modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the confirmation email to '.$sub->get('email'));
            }
            $mailer->mailer->ClearAllRecipients();
        }
    }
}

//function getAttachments($document) {
//	/* Get the TV */
//	$tv = $modx->getObject('modTemplateVar',array('name'=>'tvAttach'));
//
//	/* get the raw content of the TV */
//	$rawValue = $tv->getValue($document->get('id'));
//	return $rawValue;
//}

// Store the test message in the queue-list
if($sent) {
    // Find the test recipient in our subscribers
    $recipient = $modx->getObject('Subscriber', array('email' => $_POST['email']));
    $sub_id = 0;
    if($recipient)
        $sub_id = $recipient->get('id');

    $queue = $modx->newObject('Queue');
    $data = array(
        'subscriber'    => $sub_id,
        'newsletter'    => $_POST['id'],
        'state'         => 1,
        'sent'          => time(),
        'priority'      => 0,
        );
    $queue->fromArray($data);
    $queue->save();
}

$mailer->reset();

/**
 * Feed the manager log
 */
$user = $modx->getAuthenticatedUser('mgr');
$l = $modx->newObject('modManagerLog');
$data = array(
    'user'      => $user->get('id'),
    'occurred'  => date('Y-m-d H:i:s'),
    'action'    => 'test_newsletter',
    'classKey'  => 'Newsletter',
    'item'      => $newsletter->get('id')
);

$l->fromArray($data);
$l->save();

return $modx->error->success('',$newsletter);