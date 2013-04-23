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

$subsriber_id = empty($_POST['subscriber_id']) ? -1 : $_POST['subscriber_id'];

/*if($sort == "newsletterTitle") {
    $sort = `Document`.`pagetitle`;
}*/

/* query for bouncing messages */
$c = $modx->newQuery('Bounces');

//$c->sortby("`Bounces`.`date`",$dir);
if ($isLimit) $c->limit($limit,$start);

/*
SELECT b.*, m.pagetitle AS newsletterTitle, n.docid, n.sent_date
FROM camp_bounces b
LEFT JOIN camp_newsletter n ON b.newsletter = n.id
LEFT JOIN modx_site_content m ON m.id = n.docid
WHERE b.type="s" AND b.subscriber=X
*/

$count = $modx->getCount('Bounces',$c);

$c->leftJoin('Newsletter', 'Newsletter', '`Newsletter`.`id` = `Bounces`.`newsletter`');
$c->leftJoin('modDocument', 'Document', '`Document`.`id` = `Newsletter`.`docid`');

$c->select('`Bounces`.*, `Document`.`pagetitle` AS newsletterTitle, `Newsletter`.`docid`, `Newsletter`.`sent_date`, `Newsletter`.`id` AS newsletter');
$c->where('`Bounces`.`type`="s" AND `Bounces`.`subscriber`="'.$subsriber_id.'"');
$c->sortby('`Newsletter`.`sent_date`','DESC');

//$c->prepare(); var_dump($c->toSQL());

$bounce = $modx->getCollection('Bounces',$c);

/* iterate through bounce messages */
$list = array();
foreach ($bounce as $item) {
    $bounceItem = $item->toArray();
    
    $bounceItem['sent_date'] = date('d.m.Y H:i:s', $bounceItem['sent_date']);
    
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
    //
    //foreach($bounceItem as $key => $value) {
    //    $bounceItem['name'] .= $key.": ".$value." ";
    //}
    
    $list[] = $bounceItem;
}

//var_dump($list);
return $this->outputArray($list,$count);