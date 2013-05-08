<?php
/**
 * Get accumulated statistics of newsletters 
 *
 * @package campaigner
 */


/* setup default properties */
// $isLimit        = !empty($_REQUEST['limit']);
// $start          = $modx->getOption('start',$_REQUEST,0);
// $limit          = $modx->getOption('limit',$_REQUEST,20);
// $sort           = $modx->getOption('sort',$_REQUEST,'id');
// $dir            = $modx->getOption('dir',$_REQUEST,'ASC');
// $showProcessed  = $modx->getOption('showProcessed',$_REQUEST,0);
// $search         = $modx->getOption('search',$_REQUEST,0);

// $sort = '`Queue`.'. $sort;

/* query for subscribers */
$c = $modx->newQuery('Newsletter');

/* get the newsletters of a subscriber */
// if(!empty($search)) {
//     $c->where("`Subscriber`.`email` LIKE '%$search%'");
// }

$c->leftJoin('modDocument', 'Document', '`Document`.`id` = `Newsletter`.`docid`');
$c->leftJoin('SubscriberHits', 'SubscriberHits', '`Newsletter`.`id` = `SubscriberHits`.`newsletter`');

$c->select(array(
    'newsletter'	=> $modx->getSelectColumns('Newsletter', 'Newsletter', '', array('id', 'docid', 'state', 'sent_date', 'total', 'sent', 'bounced')),
    'pagetitle'		=> 'Document.pagetitle',
    'hits'			=> 'SUM(CASE WHEN SubscriberHits.hit_type != \'image\' THEN view_total ELSE 0 END)',
    'opened'		=> 'SUM(CASE WHEN SubscriberHits.hit_type = \'image\' THEN view_total ELSE 0 END)',
    'subscriber'	=> 'COUNT(DISTINCT(SubscriberHits.subscriber))',
    )
);
$c->groupby('SubscriberHits.newsletter');
$c->prepare();
// echo $c->toSQL() . '<br/>';
$nl = $modx->getCollection('Newsletter',$c);

/* iterate through subscribers */
$list = array();
$sum = 0;
foreach ($nl as $item) {
    $item = $item->toArray();
    $item['perc_open'] = number_format(($item['subscriber'] / $item['total']) * 100, 2);
    $item['sent_date'] = ($item['sent_date']) ? date('d.m.Y H:i:s', $item['sent_date']) : '';
    $list[] = $item;
}
return $this->outputArray($list,$count);