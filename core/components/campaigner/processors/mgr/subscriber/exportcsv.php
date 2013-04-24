<?php
header("Content-type: text/plain");
header("Content-Disposition: attachment; filename=subscribers.csv");

echo $modx->lexicon('campaigner.subscriber.email').';'
   . $modx->lexicon('campaigner.subscriber.firstname').';'
   . $modx->lexicon('campaigner.subscriber.lastname').';'
   . $modx->lexicon('campaigner.subscriber.active').';'
   . $modx->lexicon('campaigner.subscriber.groups').';'. PHP_EOL;


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
    echo $subscriber->get('email').';';
    echo $subscriber->get('firstname').';';
    echo $subscriber->get('lastname').';';
    echo $subscriber->get('active').';';

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
    echo implode(',', $grpArray);
    echo PHP_EOL;
}

exit;