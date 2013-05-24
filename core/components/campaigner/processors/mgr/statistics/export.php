<?php

$stats_id = $_POST['statistics_id'] ? $_POST['statistics_id'] : $_GET['statistics_id'];
$search = $modx->getOption('search',$_REQUEST, 0);
$open = $modx->getOption('open', $_REQUEST, 0);

if(!$_GET['export'] && !$_GET['export'] == 1)
    return $modx->error->success('');

$c = $modx->newQuery('SubscriberHits');

$c->leftJoin('NewsletterLink', 'NewsletterLink', 'NewsletterLink.id = SubscriberHits.link');
$c->leftJoin('Subscriber', 'Subscriber', 'SubscriberHits.subscriber = Subscriber.id');

$c->where(array('newsletter' => $stats_id));

if($open) {
	$c->where(array('SubscriberHits.hit_type' => 'image' ));
} else {
	$c->where(array('SubscriberHits.hit_type:!=' => 'image' ));
}
if($search)
	$c->where(array('Subscriber.email:LIKE' => '%'.$search.'%' ));

$c->select(array(
	'link'		=> $modx->getSelectColumns('NewsletterLink', 'NewsletterLink', '', array('url')),
	'hits'		=> $modx->getSelectColumns('SubscriberHits', 'SubscriberHits', '', array('id', 'view_total', 'hit_date')),
	'email'=> $modx->getSelectColumns('Subscriber', 'Subscriber', '', array('email')),
	));

$c->prepare();
// echo $c->toSQL();
// $modx->log(MODX_LOG_LEVEL_ERROR, 'STATISTICS DETAIL SQL ' . $c->toSQL());
$items = $modx->getCollection('SubscriberHits', $c);

$list = array();
foreach ($items as $item) {
    $item = $item->toArray();
    $item['ip'] = long2ip($item['ip']);
    $list[] = $item;
}

if(!$_GET['export'])
    return $this->outputArray($list,$count);

// Export specific statistic data
$delimiter = ',';
$nl = PHP_EOL;
$export_fields = array('newsletter', 'link', 'email', 'hit_date', 'view_total', 'client');
foreach($list as $item) {
	$row_values = array();
    if(!$header)
        $header = implode($delimiter, $export_fields);
    foreach($item as $key => $value) {
    	if(in_array($key, $export_fields)) {
    		$row_values[array_search($key,$export_fields)] = $value;
    	}
    }
    ksort($row_values, SORT_NUMERIC);
    $content[] = implode($delimiter, $row_values);
}
header('Content-Type: application/force-download');
header('Content-Disposition: attachment; filename="statistic-data.csv"'); 
return $header . $nl . implode($nl, $content);