<?php

// validate properties
if (empty($_POST['id'])) {
    return $modx->error->failure($modx->lexicon('campaigner.newsletter.error.notfound'));
}

// get the object
$newsletter = $modx->getObject('Newsletter', array('id' => $_POST['id']));
if(!$newsletter) return $modx->error->failure($modx->lexicon('campaigner.newsletter.error.notfound'));


$document = $modx->getObject('modDocument', array('id' => $newsletter->get('docid')));
if(!$document) return $modx->error->failure($modx->lexicon('campaigner.newsletter.error.notfound'));

$message = $modx->campaigner->composeNewsletter($document);
$subscriber = null;

if($_POST['email']) {
    $subscriber = $modx->getObject('Subscriber', array('email' => $_POST['email']));
}
$message = $modx->campaigner->makeTrackingUrls($message, $newsletter, $subscriber);
$message = $modx->campaigner->processNewsletter($message, $subscriber, array('process' => $_POST['tags']));
$textual = $modx->campaigner->textify($message);

$msg['message'] = $message;
$msg['text'] = '<pre>'. $textual .'</pre>';

return $modx->error->success('',$msg);
