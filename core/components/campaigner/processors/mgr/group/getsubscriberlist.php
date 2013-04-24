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

/* query for groups */
$c = $modx->newQuery('Group');
if(!empty($public)) {
    $c->where(array('public' => $public));
}

$count = $modx->getCount('Group',$c);

$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);
$groups = $modx->getCollection('Group',$c);

/* iterate through groups */
$list = array();
foreach ($groups as $group) {
        $group = $group->toArray();
        $list[] = $group;
}
return $modx->error->success('',$list);