<?php
/**
 * Get a list of bounce messages
 *
 *
 * @package 
 * @subpackage 
 */

$modx->lexicon->load('campaigner:default');

/* setup default properties */
$isLimit        = !empty($_REQUEST['limit']);
$start          = $modx->getOption('start',$_REQUEST,0);
$limit          = $modx->getOption('limit',$_REQUEST,20);
$sort           = $modx->getOption('sort',$_REQUEST,'id');
$dir            = $modx->getOption('dir',$_REQUEST,'DESC');

/*if($sort == "newsletterTitle") {
    $sort = `Document`.`pagetitle`;
}*/

/* query for bouncing messages */
$c = $modx->newQuery('Bounces');

//$c->sortby("`Bounces`.`date`",$dir);
if ($isLimit) $c->limit($limit,$start);

/*
SELECT b.*,s.`firstname`,s.`lastname`,s.`email`, n.`sent_date` FROM testx.`camp_bounces` b
LEFT JOIN testx.`camp_newsletter` n ON b.`newsletter`=n.`id`
LEFT JOIN testx.`camp_subscriber` s ON b.`subscriber`=s.`id`
WHERE b.`type`='h'
ORDER BY n.`sent_date` DESC;
*/

$count = $modx->getCount('Bounces',$c);

$c->leftJoin('Newsletter', 'Newsletter', '`Newsletter`.`id` = `Bounces`.`newsletter`');
$c->leftJoin('Subscriber', 'Subscriber', '`Subscriber`.`id` = `Bounces`.`subscriber`');

$c->select('`Bounces`.*, `Subscriber`.`firstname`, `Subscriber`.`lastname`, `Subscriber`.`email`, `Newsletter`.`sent_date` AS date');
$c->where('`Bounces`.`type`="h"');
$c->sortby('`date`','DESC');

//$c->prepare(); var_dump($c->toSQL()); die;

$bounce = $modx->getCollection('Bounces',$c);

/* iterate through bounce messages */
$list = array();
foreach ($bounce as $item) {
    $bounceItem = $item->toArray();
    
    if($bounceItem['recieved'] == 0) {
        $bounceItem['recieved'] = "-";
    }
    else {
        $bounceItem['recieved'] = date('d.m.Y H:i:s', $bounceItem['recieved']);    
    }
        
    if($bounceItem['firstname']==NULL) {
        $bounceItem['firstname'] = "-";
    }
    if($bounceItem['lastname']==NULL) {
        $bounceItem['lastname'] = "-";
    }
    
    //Hier gibt es fuer jeden Error einen eigenen Eintrag im Lexikon (zb.: campaigner.bounce.error.1.0)
    //Wenn das Ergebnis des Lexikoneintrags gleich dem Namen des Lexikoneintrags ist, dann gibt es ihn nicht!
    $codes = explode(".", $bounceItem['code']);
    $lexicon_entry_name = 'campaigner.bounce.error.'.$codes[1].'.'.$codes[2];
    if($modx != null && $modx->lexicon($lexicon_entry_name) != $lexicon_entry_name) {
        $bounceItem['reason'] = $modx->lexicon($lexicon_entry_name);    
    }
    else {
        $bounceItem['reason'] = "Kein Text zu diesem Error Code";
    }    
    
    $bounceItem['name'] = $bounceItem['firstname'].' '.$bounceItem['lastname'];
    
    $c = $modx->newQuery('Group');
    $c->leftJoin('GroupSubscriber', 'GroupSubscriber', '`GroupSubscriber`.`group` = `Group`.`id`');
    $c->where('`GroupSubscriber`.`subscriber` = '. $bounceItem['subscriber']);
    $c->select('`Group`.*');
    $c->sortby('`Group`.`id`');
    $groups = $modx->getCollection('Group', $c);
    foreach($groups as $grp) {
        $grpArray[] = array($grp->get('id'), $grp->get('name'), $grp->get('color'));
    }
    $bounceItem['groups'] = $grpArray;
    unset($grpArray);
    //
    //foreach($bounceItem as $key => $value) {
    //    $bounceItem['name'] .= $key.": ".$value." ";
    //}
    
    $list[] = $bounceItem;
}

return $this->outputArray($list,$count);