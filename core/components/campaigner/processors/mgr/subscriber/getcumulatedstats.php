<?php
/**
 * Get cumulated subscriber statistics
 */
$c = $modx->newQuery('SubscriberHits');
$c->where(array('subscriber' => $_POST['subscriber']));
$c->select('`id`, `newsletter`, SUM(CASE WHEN hit_type = \'click\' THEN view_total ELSE 0 END) AS clicks, SUM(CASE WHEN hit_type LIKE \'%image%\' THEN view_total ELSE 0 END) AS opens');
$c->groupBy('newsletter');
$c->sortby('opens', 'DESC');
$c->limit(8);
$result = $modx->getCollection('SubscriberHits', $c);
foreach($result as $item) {
	$list[] = $item->toArray();
}
return $this->outputArray($list,count($list));