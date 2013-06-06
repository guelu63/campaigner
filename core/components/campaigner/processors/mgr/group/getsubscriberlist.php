<?php
/**
 * Get list of subscribers
 */
$modx->log(MODX_LOG_LEVEL_ERROR, $_REQUEST['id'] . ' - ' . $_REQUEST['group_id']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,10);

$c = $modx->newQuery('Subscriber');
$c->limit($limit,$start);
$c->leftJoin('GroupSubscriber', 'GroupSubscriber', '`GroupSubscriber`.`subscriber` = `Subscriber`.`id` AND `GroupSubscriber`.`group` = ' . $_REQUEST['group_id']);
$c->select($modx->getSelectColumns('Subscriber', 'Subscriber', '', array('id', 'email', 'firstname', 'lastname')));
$c->select(array(
  '`GroupSubscriber`.`subscriber` AS group_subscriber'
));
// $c->prepare();
// echo $c->toSQL();
$subscribers = $modx->getCollection('Subscriber', $c);
$count = $modx->getCount('Subscriber',$c);
$list = array();
foreach($subscribers as $item) {
	$prep = $item->toArray();
	$prep['assigned'] = $prep['id'] == $prep['group_subscriber'] ? true : false;
	$list[] = $prep;
}
return $this->outputArray($list,$count);
// return $modx->error->success('', $list);