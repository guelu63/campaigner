<?php
$stats_id = $_POST['statistics_id'];
$search = $modx->getOption('search',$_REQUEST, 0);
$open = $modx->getOption('open', $_REQUEST, 0);

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
    $list[] = $item;
}
return $this->outputArray($list,$count);