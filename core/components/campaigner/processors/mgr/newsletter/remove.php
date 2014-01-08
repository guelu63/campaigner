<?php
/**
 * Remove newsletter and its related resource
 * @package  campaigner
 */
$ids = explode(',', $_REQUEST['marked']);
$newsletters = $modx->getCollection('Newsletter', array('id:IN' => $ids));

if(!$newsletters)
	return $modx->error->failure($modx->lexicon('campaigner.newsletter.error.notfound'));

$success = true;
foreach($newsletters as $newsletter) {
	// Get the modResource object
	$res = $modx->getObject('modResource', $newsletter->get('docid'));
	if($res)
		if($res->remove())
			$success = true;
	if(!$newsletter->remove())
		$success = false;
}

if($success)
	return $modx->error->success('',$newsletter);
return $modx->error->failure($modx->lexicon('campaigner.newsletter.batch_errors'));