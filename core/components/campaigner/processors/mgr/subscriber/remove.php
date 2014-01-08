<?php
/**
 * Processor: Remove subscribers
 *
 * Remove single or multiple subscribers via CheckboxSelectionModel
 * For each removed subscriber a 'Unsubscriber' will be created, with the information
 * of an admin-controlled removal
 * 
 * @param marked string Comma-separated list of subscriber IDs
 * @return JSON
 */

$subscriber = $modx->getCollection('Subscriber', array('id:IN' => explode(',', $_REQUEST['marked'])));

if ($subscriber == null)
	return $modx->error->failure($modx->lexicon('campaigner.subscriber.notfound'));

$error = true;
foreach($subscriber as $item) {
	$unsub = $this->modx->newObject('Unsubscriber');
	$data = array(
	    'subscriber' => $item->get('email'),
	    'date' => time(),
	    'via' => 'admin',
	    );
	$unsub->fromArray($data);
	if($unsub->save())
		if($item->remove())
			$error = false;
}

if($error)
	return $modx->error->success('campaigner.subscriber.error.unknown');

return $modx->error->success('campaigner.subscriber.removed');