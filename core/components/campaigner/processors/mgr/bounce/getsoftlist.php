<?php
/**
 * Get a list of bounce messages
 *
 *
 * @package 
 * @subpackage 
 */

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
SELECT b.*,MAX(n.`sent_date`) AS `last`,s.`firstname`,s.`lastname`,s.`email`, COUNT(b.`id`) AS `count` FROM testx.`camp_bounces` b
LEFT JOIN testx.`camp_newsletter` n ON b.`newsletter`=n.`id`
LEFT JOIN testx.`camp_subscriber` s ON b.`subscriber`=s.`id`
WHERE b.`type`='s'
GROUP BY b.`subscriber` 
ORDER BY n.`sent_date` DESC;
*/

$count = $modx->getCount('Bounces',$c);

$c->leftJoin('Newsletter', 'Newsletter', '`Newsletter`.`id` = `Bounces`.`newsletter`');
$c->leftJoin('Subscriber', 'Subscriber', '`Subscriber`.`id` = `Bounces`.`subscriber`');

$c->select('`Bounces`.*, `Subscriber`.`firstname`, `Subscriber`.`lastname`, `Subscriber`.`email`, `Subscriber`.`active`, MAX(`Bounces`.`recieved`) AS `last`, COUNT(`Bounces`.`id`) AS `count`');
$c->where('`Bounces`.`type`="s"');
$c->groupby('`Bounces`.`subscriber`');
$c->sortby('`last`','DESC');

//$c->prepare(); var_dump($c->toSQL()); die;

$bounce = $modx->getCollection('Bounces',$c);

/* iterate through bounce messages */
$list = array();
foreach ($bounce as $item) {
    $bounceItem = $item->toArray();
    
    if($bounceItem['last'] == 0) {
        $bounceItem['last'] = "-";
    }
    else {
        $bounceItem['last'] = date('d.m.Y H:i:s', $bounceItem['last']);    
    }
    
    if($bounceItem['firstname']==NULL) {
        $bounceItem['firstname'] = "-";
    }
    if($bounceItem['lastname']==NULL) {
        $bounceItem['lastname'] = "-";
    }
    
    $bounceItem['name'] = $bounceItem['firstname'].' '.$bounceItem['lastname'];
    
    //
    //foreach($bounceItem as $key => $value) {
    //    $bounceItem['name'] .= $key.": ".$value." ";
    //}
    
    $list[] = $bounceItem;
}
return $this->outputArray($list,$count);