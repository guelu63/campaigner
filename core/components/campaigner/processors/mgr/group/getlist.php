<?php
/**
 * Get a list of groups
 *
 *
 * @package ditsnews
 * @subpackage processors.groups.list
 */


/* setup default properties */
$isLimit    = !empty($_REQUEST['limit']);
$start      = $modx->getOption('start',$_REQUEST,0);
$limit      = $modx->getOption('limit',$_REQUEST,20);
$sort       = $modx->getOption('sort',$_REQUEST,'id');
$dir        = $modx->getOption('dir',$_REQUEST,'ASC');
$public     = $modx->getOption('public',$_REQUEST,null);

if(!in_array($sort, array('total', 'active'))) {
    $sort = '`Group`.'. $sort;
}

/* query for groups */
$c = $modx->newQuery('Group');
if(!empty($public)) {
    $c->where(array('public' => $public));
}

$count = $modx->getCount('Group',$c);

$c->leftJoin('GroupSubscriber', 'GroupSubscriber', '`GroupSubscriber`.`group` = `Group`.`id`');
$c->leftJoin('Subscriber', 'Subscriber', '`GroupSubscriber`.`subscriber` = `Subscriber`.`id`');

$c->sortby($sort,$dir);
$c->groupby('`Group`.`id`');
if ($isLimit) $c->limit($limit,$start);

$c->select('`Group`.*, COALESCE( COUNT(`Subscriber`.`id`) , 0 ) AS total, COALESCE( SUM(`Subscriber`.`active`) , 0 ) AS `active`');

$groups = $modx->getCollection('Group',$c);
/* iterate through groups */
$list = array();
foreach ($groups as $group) {
    $group = $group->toArray();
    $list[] = $group;
}
return $this->outputArray($list,$count);