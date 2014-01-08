<?php
/**
 * Processor: Create segment
 *
 * Creates a segment from the current grid
 */
$keys = json_decode($_REQUEST['keys']);

$data = array(
	'name'	=> 'Segment '.date('Y-m-d'),
	'public' => 1,
	'subscribers' => count($keys),
	);

$group = $modx->newObject('Group');
$group->fromArray($data);

// save it
if (!$group->save())
	return $modx->error->failure();

foreach($keys as $key) {
	$subscriber = $modx->getObject('Subscriber', array('id' => $key));
	if(!$subscriber)
		continue;
	$sub_group = $modx->newObject('GroupSubscriber');
	$data = array(
		'subscriber'	=> $subscriber->get('id'),
		'group'			=> $group->get('id')
		);
	$sub_group->fromArray($data);
	$sub_group->save();
}
return $modx->error->success();