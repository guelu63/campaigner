<?php
/**
 * Processor: Get the newsletter content
 */

// get the object
$newsletter = $modx->getObject('Newsletter', array('id' => $_POST['newsletter']));
if(!$newsletter) return $modx->error->failure($modx->lexicon('campaigner.newsletter.error.notfound'));

$document = $modx->getObject('modResource', array('id' => $newsletter->get('docid')));
if(!$document) return $modx->error->failure($modx->lexicon('campaigner.newsletter.error.notfound'));

$message = $modx->campaigner->composeNewsletter($document);
// var_dump($message);
if(!$_POST['email'] || empty($_POST['email']))
	return $modx->error->success('Content loaded',array('html' => $message));

if($_POST['email']) {
    $subscriber = $modx->getObject('Subscriber', array('email' => $_POST['email']));
}
$message = $modx->campaigner->processNewsletter($message, $subscriber, array('process' => $_POST['tags']));

return $modx->error->success('',array('html' => $message));