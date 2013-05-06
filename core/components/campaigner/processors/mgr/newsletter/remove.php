<?php
/**
 * Remove newsletter and its related resource
 * @package  campaigner
 */
if(!empty($_POST['id'])) {
    $newsletter = $modx->getObject('Newsletter', array('id' => $_POST['id']));
}

if(!$newsletter) return $modx->error->failure($modx->lexicon('campaigner.newsletter.error.notfound'));

// Get the modResource object
$res = $modx->getObject('modResource', $newsletter->get('docid'));

if(!$res)
	return $modx->error->failure($modx->lexicon('campaigner.newsletter.error.notfound'));

if($res->remove() && $newsletter->remove())
	return $modx->error->success('',$newsletter);