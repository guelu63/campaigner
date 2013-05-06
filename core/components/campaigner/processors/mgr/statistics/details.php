<?php

$id = $_POST['id'];
$search = $modx->getOption('search',$_REQUEST,0);

$c = $modx->newQuery('SubscriberHits');

$c->leftJoin('NewsletterLink', 'NewsletterLink', 'NewsletterLink.id = SubscriberHits.link');
$c->leftJoin('Subscriber', 'Subscriber', 'SubscriberHits.subscriber = Subscriber.id');

$c->where(array('newsletter' => 1));
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
    // $item['sent_date'] = ($item['sent_date']) ? date('d.m.Y H:i:s', $item['sent_date']) : '';
    $list[] = $item;
}
return $this->outputArray($list,$count);