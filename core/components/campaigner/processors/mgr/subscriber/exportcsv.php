<?php
if(!$_GET['export'] && !$_GET['export'] == 1)
    return $modx->error->success('');
// header("Content-type: text/plain");
// header("Content-Disposition: attachment; filename=subscribers.csv");

$out = $modx->lexicon('campaigner.subscriber.email').';'
   . $modx->lexicon('campaigner.subscriber.firstname').';'
   . $modx->lexicon('campaigner.subscriber.lastname').';'
   . $modx->lexicon('campaigner.subscriber.active').';'
   . $modx->lexicon('campaigner.subscriber.groups'). PHP_EOL;


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
    $c->where("CONCAT(' ', `Subscriber`.`firstname`, `Subscriber`.`lastname`) LIKE '%$search%'");
}

$c->select('`Subscriber`.*');

$subscribers = $modx->getCollection('Subscriber',$c);

foreach($subscribers as $subscriber)
{
    $out .= $subscriber->get('email').';';
    $out .= $subscriber->get('firstname').';';
    $out .= $subscriber->get('lastname').';';
    $out .= $subscriber->get('active').';';

    $grpArray = array();
    $c = $modx->newQuery('Group');
    $c->leftJoin('GroupSubscriber', 'GroupSubscriber', '`GroupSubscriber`.`group` = `Group`.`id`');
    $c->where('`GroupSubscriber`.`subscriber` = '. $subscriber->get('id'));
    $c->select('`Group`.*');
    $c->sortby('`Group`.`id`');
    $groups = $modx->getCollection('Group', $c);
    foreach($groups as $grp) {
        $grpArray[] = $grp->get('name');
    }
    $out .= implode(',', $grpArray);
    $out .= PHP_EOL;
}
// echo $out;

// exit;
header('Content-Type: application/force-download');
header('Content-Disposition: attachment; filename="subscriber.csv"'); 
return $out;