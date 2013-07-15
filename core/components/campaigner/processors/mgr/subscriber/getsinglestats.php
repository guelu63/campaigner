<?php
/**
 * Get cumulated subscriber statistics
 */
// var_dump($_REQUEST);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties);
$date_from = $modx->getOption('date_from',$scriptProperties, null);
$date_to = $modx->getOption('date_to',$scriptProperties, null);
$hittype = $modx->getOption('hittype',$scriptProperties, null);
$search= trim($modx->getOption('search',$_REQUEST,null));

$c = $modx->newQuery('SubscriberHits');
$c->where(array('subscriber' => $_POST['subscriber']));
if($hittype)
	$c->where(array('hit_type' => $hittype));
if($date_from)
	$c->where(array('hit_date:>=' => strftime('%Y-%m-%d %H:%M:%S', strtotime($_POST['date_from']))));
if($date_to)
	$c->where(array('hit_date:<=' => strftime('%Y-%m-%d %H:%M:%S', strtotime($_POST['date_to']))));
$c->limit($limit,$start);
// $c->select('`id`, `newsletter`, SUM(CASE WHEN hit_type = \'click\' THEN view_total ELSE 0 END) AS clicks, SUM(CASE WHEN hit_type LIKE \'%image%\' THEN view_total ELSE 0 END) AS opens');
// $c->groupBy('newsletter');
// $c->sortby('opens', 'DESC');
// $c->limit(8);
$c->prepare();
$modx->log(MODX_LOG_LEVEL_ERROR, $c->toSQL());
$count = $modx->getCount('SubscriberHits',$c);
$result = $modx->getCollection('SubscriberHits', $c);
foreach($result as $item) {
	$list[] = $item->toArray();
}
return $this->outputArray($list, $count);