<?php
/**
 * Processor: Remove queue items
 * 
 * Remove single or multiple queue items and reduce the total of sent items
 * in the newsletter (which sound weird afterwards)
 *
 * @return JSON Error or success message
 */
$ids = explode(',', $_REQUEST['marked']);

$queue = $modx->getCollection('Queue', array('id:IN' => $ids));

if ($queue == null)
    return $modx->error->failure($modx->lexicon('campaigner.queue.error.notfound'));

foreach($queue as $item) {
	// Reduce newsletter total when unsend
	if($item->get('state') === 0) {
		$nl = $modx->getObject('Newsletter', $item->get('newsletter'));
		$nl->set('total', $nl->get('total') - 1);
		$nl->save();
	}
	$item->remove();
}

return $modx->error->success('campaigner.queue.removed');