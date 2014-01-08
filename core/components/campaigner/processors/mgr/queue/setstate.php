<?php
/**
 * Processor: Set state of queue items
 */
$marked = explode(',', $_REQUEST['marked']);
if(!is_array($marked) || empty($marked))
	return $modx->error->failure();

$state = $scriptProperties['state'];

$c = $modx->newQuery('Queue');
$c->where(array('id:IN' => $marked));

$items = $modx->getCollection('Queue', $c);
$success = true;
foreach($items as $item) {
	$item->set('state', $state);
	if($state == 0)
		$item->set('sent', null);
	if(!$item->save())
		$success = false;
}
if($success)
	return $modx->error->success('');
return $modx->error->failure('');