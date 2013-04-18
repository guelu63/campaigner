<?php
/**
 * Get a list of subscribers
 *
 *
 * @package ditsnews
 * @subpackage processors.subscribers.list
 */


/* setup default properties */
$isLimit    = !empty($_REQUEST['limit']);
$start      = $modx->getOption('start',$_REQUEST,0);
$limit      = $modx->getOption('limit',$_REQUEST,9999999);
$sort       = $modx->getOption('sort',$_REQUEST,'id');
$dir        = $modx->getOption('dir',$_REQUEST,'ASC');
$text       = $modx->getOption('text',$_REQUEST,null);
$group      = $modx->getOption('group',$_REQUEST,null);
$active     = $modx->getOption('active',$_REQUEST,null);
$search     = trim($modx->getOption('search',$_REQUEST,null));

if($sort != 'type') {
    $sort = '`Subscriber`.'.$sort;
} else {
    $sort = '`Subscriber`.`text`';
}

/* query for subscribers */
$c = $modx->newQuery('Subscriber');

if(isset($text)) {
    if($text == 0) $text = null;
    $c->where(array('text' => $text));
}
if(isset($active)) {
    $c->where(array('active' => $active));
}
if(!empty($group)) {
    $c->innerJoin('GroupSubscriber', 'GW', '`GW`.`subscriber` = `Subscriber`.`id` AND `GW`.`group` = '. $group);
}
if(!empty($search)) {
    $c->where("CONCAT(' ', `Subscriber`.`firstname`, `Subscriber`.`lastname`, `Subscriber`.`email`) LIKE '%$search%'");
}

$count = $modx->getCount('Subscriber',$c);

$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);

#$c->leftJoin('GroupSubscriber', 'GroupSubscriber', '`GroupSubscriber`.`subscriber` = `Subscriber`.`id`');
#$c->leftJoin('Group', 'Group', '`Group`.`id` = `GroupSubscriber`.`group`');
$c->select('`Subscriber`.*');
#$c->groupby('`Subscriber`.`id`');

#$c->prepare(); var_dump($c->toSQL()); die;

$subscribers = $modx->getCollection('Subscriber',$c);

/* iterate through subscribers */
$list = array();
foreach ($subscribers as $subscriber) {
    $subscriber = $subscriber->toArray();
    $subscriber['type'] = $subscriber['text'] == 1 ? 'text' : 'html';
    $subscriber['since'] = date('d.m.Y', $subscriber['since']);
    $grpArray = array();
    $c = $modx->newQuery('Group');
    $c->leftJoin('GroupSubscriber', 'GroupSubscriber', '`GroupSubscriber`.`group` = `Group`.`id`');
    $c->where('`GroupSubscriber`.`subscriber` = '. $subscriber['id']);
    $c->select('`Group`.*');
    $c->sortby('`Group`.`id`');
    $groups = $modx->getCollection('Group', $c);
    foreach($groups as $grp) {
        $grpArray[] = array($grp->get('id'), $grp->get('name'), $grp->get('color'));
    }

/*
        $groups = explode(';', $subscriber['groups']);
        $grpArray = array();
        foreach($groups as $grp) {
              if(false === strpos($grp, ',')) continue;
              $grpArray[] = explode(',', $grp);
        }
        */
    $subscriber['groups'] = $grpArray;
    $list[] = $subscriber;
}
return $this->outputArray($list,$count);