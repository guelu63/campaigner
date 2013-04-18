<?php
/**
 * Get a list of subscribers
 *
 *
 * @package ditsnews
 * @subpackage processors.subscribers.list
 */


/* setup default properties */
$isLimit        = !empty($_REQUEST['limit']);
$start          = $modx->getOption('start',$_REQUEST,0);
$limit          = $modx->getOption('limit',$_REQUEST,20);
$sort           = $modx->getOption('sort',$_REQUEST,'id');
$dir            = $modx->getOption('dir',$_REQUEST,'ASC');
$showProcessed  = $modx->getOption('showProcessed',$_REQUEST,0);
$search         = $modx->getOption('search',$_REQUEST,0);

$sort = '`Queue`.'. $sort;

/* query for subscribers */
$c = $modx->newQuery('Queue');

$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);

/* only get the already sent newsletters */
if(!$showProcessed) {
    $c->where(array('state:!=' => '1'));
}

/* get the newsletters of a subscriber */
if(!empty($search)) {
    $c->where("`Subscriber`.`email` LIKE '%$search%'");
}

$count = $modx->getCount('Queue',$c);

$c->leftJoin('Newsletter', 'Newsletter', '`Newsletter`.`id` = `Queue`.`newsletter`');
$c->leftJoin('modDocument', 'Document', '`Document`.`id` = `Newsletter`.`docid`');
$c->leftJoin('Subscriber', 'Subscriber', '`Subscriber`.`id` = `Queue`.`subscriber`');

$c->select('`Newsletter`.`docid`, `Newsletter`.`total`, `Newsletter`.`state`, `Newsletter`.`bounced`, `Document`.`pagetitle` AS subject, `Document`.`publishedon` AS date, `Queue`.*, `Subscriber`.`firstname`, `Subscriber`.`lastname`, `Subscriber`.`email`, `Subscriber`.`text`');

$queue = $modx->getCollection('Queue',$c);

/* iterate through subscribers */
$list = array();
foreach ($queue as $item) {
        $queueItem = $item->toArray();
        $queueItem['sent'] = ($queueItem['sent']) ? date('d.m.Y H:i:s', $queueItem['sent']) : '';
        $list[] = $queueItem;
}
return $this->outputArray($list,$count);