<?php
/**
 * Get a list of subscribers
 *
 *
 * @package ditsnews
 * @subpackage processors.subscribers.list
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

// $c->sortby($sort,$dir);
// if ($isLimit) $c->limit($limit,$start);

/* only get the already sent newsletters */
// if(!$showProcessed) {
//     $c->where(array('state:!=' => '1'));
// }

/* get the newsletters of a subscriber */
// if(!empty($search)) {
//     $c->where("`Subscriber`.`email` LIKE '%$search%'");
// }

// $count = $modx->getCount('Queue',$c);
// $c->leftJoin('Autonewsletter', 'Autonewsletter', '`Autonewsletter`.`id` = `Queue`.`newsletter`');
// $c->leftJoin('Newsletter', 'Newsletter', '`Newsletter`.`id` = `Queue`.`newsletter`');
$c->leftJoin('modDocument', 'Document', '`Document`.`id` = `Newsletter`.`docid`');
$c->leftJoin('SubscriberHits', 'SubscriberHits', '`Newsletter`.`id` = `SubscriberHits`.`newsletter`');
// $c->leftJoin('Subscriber', 'Subscriber', array('`Subscriber`.`id`' => '`Newsletter`.`id`'));

$c->select(array(
    'newsletter'	=> $modx->getSelectColumns('Newsletter', 'Newsletter', '', array('id', 'docid', 'state', 'sent_date', 'total', 'sent', 'bounced')),
    'pagetitle'		=> 'Document.pagetitle',
    'hits'			=> 'SUM(SubscriberHits.view_total)',
    'opened'		=> 'SUM(CASE WHEN SubscriberHits.hit_type = \'image\' THEN view_total ELSE 0 END)',
    'subscriber'	=> 'COUNT(SubscriberHits.subscriber)',
    )
);
$c->groupby('SubscriberHits.newsletter');
// $c->select('`Newsletter`.`docid`, `Newsletter`.`total`, `Newsletter`.`state`, `Newsletter`.`bounced`, `Document`.`pagetitle` AS subject, `Document`.`publishedon` AS date, `Queue`.*, `Subscriber`.`firstname`, `Subscriber`.`lastname`, `Subscriber`.`email`, `Subscriber`.`text`');
$c->prepare();
// echo $c->toSQL() . '<br/>';
$nl = $modx->getCollection('Newsletter',$c);

/* iterate through subscribers */
$list = array();
foreach ($nl as $item) {
    $item = $item->toArray();
    $item['sent_date'] = ($item['sent_date']) ? date('d.m.Y H:i:s', $item['sent_date']) : '';
    $list[] = $item;
}
return $this->outputArray($list,$count);