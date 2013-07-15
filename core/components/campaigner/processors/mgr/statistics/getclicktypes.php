<?php
/**
 * Get the distinct click types
 */
$c = $modx->newQuery('SubscriberHits');
$c->groupBy('hit_type');
$types = $modx->getCollection('SubscriberHits', $c);
foreach($types as $type) {
	$type = $type->toArray();
	$type['name'] = $type['hit_type'];
	$type['value'] = $type['hit_type'];
	$list[] = $type;
}
return $this->outputArray($list,count($list));