<?php
header("Content-type: text/xml");
header("Content-Disposition: attachment; filename=subscribers.xml");

$names = array(
    str_replace(' ', '_', $modx->lexicon('campaigner.subscriber')),
    str_replace(' ', '_', $modx->lexicon('campaigner.subscriber.email')),
    str_replace(' ', '_', $modx->lexicon('campaigner.subscriber.firstname')),
    str_replace(' ', '_', $modx->lexicon('campaigner.subscriber.lastname')),
    str_replace(' ', '_', $modx->lexicon('campaigner.subscriber.active')),
    str_replace(' ', '_', $modx->lexicon('campaigner.subscriber.groups')),
    str_replace(' ', '_', $modx->lexicon('campaigner.group'))
);


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

#$c->leftJoin('GroupSubscriber', 'GroupSubscriber', '`GroupSubscriber`.`subscriber` = `Subscriber`.`id`');
#$c->leftJoin('Group', 'Group', '`Group`.`id` = `GroupSubscriber`.`group`');
$c->select('`Subscriber`.*');
#$c->groupby('`Subscriber`.`id`');

$subscribers = $modx->getCollection('Subscriber',$c);

echo '<'. $modx->lexicon('campaigner.subscribers') .'>' . "\n\r";
foreach($subscribers as $subscriber)
{
    echo "\t". '<'. $names[0] .'>' . "\n\r";
    echo "\t\t". '<'. $names[1] .'>' . $subscriber->get('email') . '</'. $names[1] .'>' . "\n\r";
    echo "\t\t". '<'. $names[2] .'>' . $subscriber->get('firstname') . '</'. $names[2] .'>' . "\n\r";
    echo "\t\t". '<'. $names[3] .'>' . $subscriber->get('lastname') . '</'. $names[3] .'>' . "\n\r";
    echo "\t\t". '<'. $names[4] .'>' . $subscriber->get('active') . '</'. $names[4] .'>' . "\n\r";
    
    echo "\t\t". '<'. $names[5] .'>' . "\n\r";
    $grpArray = array();
    $c = $modx->newQuery('Group');
    $c->leftJoin('GroupSubscriber', 'GroupSubscriber', '`GroupSubscriber`.`group` = `Group`.`id`');
    $c->where('`GroupSubscriber`.`subscriber` = '. $subscriber->get('id'));
    $c->select('`Group`.*');
    $c->sortby('`Group`.`id`');
    
    $groups = $modx->getCollection('Group', $c);
    foreach($groups as $grp) {
        echo "\t\t\t". '<'. $names[6] .'>' . $grp->get('name') . '</'. $names[6] .'>' . "\n\r";
    }
    echo "\t\t". '</'. $names[5] .'>' . "\n\r";
    echo "\t". '</'. $names[0] .'>';
}

echo '</'. $modx->lexicon('campaigner.subscribers') .'>';
exit;