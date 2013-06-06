<?php
/**
 * Assign multiple subscribers to a group
 */
// var_dump($_REQUEST);
// return $modx->error->success();
$params = $_REQUEST;
if(empty($_REQUEST['id']))
	return $modx->error->success();
if(empty($_REQUEST['assigned']))
	return $modx->error->success();
$subscribers = explode(',', $_REQUEST['assigned']);
if(count($subscribers) <= 0)
	return $modx->error->success();

$grp_subs = $modx->getCollection('GroupSubscriber', array('group' => $_REQUEST['id']));
foreach($grp_subs as $grp_sub) {
	$existing[] = $grp_sub->get('subscriber');
}
$modx->removeCollection('GroupSubscriber', array('subscriber:IN' => array_diff($existing, $subscribers)));

foreach($subscribers as $subscriber) {
	$sub = $modx->getObject('GroupSubscriber', array('subscriber' => $subscriber, 'group' => $_REQUEST['id']));
	if($sub)
		continue;
	$sub = $modx->newObject('GroupSubscriber');
	$data = array(
		'subscriber'	=> $subscriber,
		'group'			=> $_REQUEST['id']
		);
	$sub->fromArray($data);
	$sub->save();
}
return $modx->error->success();