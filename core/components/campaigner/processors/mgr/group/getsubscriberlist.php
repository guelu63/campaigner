<?php
/**
 * Get list of subscribers
 */
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,10);

$c = $modx->newQuery('Subscriber');
$c->limit($limit,$start);
$subscribers = $modx->getCollection('Subscriber', $c);
$count = $modx->getCount('Subscriber',$c);
$list = array();
foreach($subscribers as $item) {
	$list[] = $item->toArray();
}
return $this->outputArray($list,$count);
// return $modx->error->success('', $list);