<?php
/**
 * Get a list of newsletters
 *
 *
 * @package ditsnews
 * @subpackage processors.newsletters.list
 */


/* setup default properties */
$isLimit    = !empty($_REQUEST['limit']);
$start      = $modx->getOption('start',$_REQUEST,0);
$limit      = $modx->getOption('limit',$_REQUEST,10);
$sort       = $modx->getOption('sort',$_REQUEST,'Autonewsletter.id');
$dir        = $modx->getOption('dir',$_REQUEST,'DESC');
$state      = $modx->getOption('state',$_REQUEST,null);

/* query for newsletters */
$c = $modx->newQuery('Autonewsletter');
if(isset($state)) {
        $c->where(array('state' => $state));
}
$count = $modx->getCount('Autonewsletter',$c);

$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);

$c->leftJoin('modDocument', 'Document', '`Document`.`id` = `Autonewsletter`.`docid` AND `Document`.`published` = 1');

//Added by andreas @ 2012-07-20
//Fetches only published documents
$c->where(array('Document.published' => 1));

$c->leftJoin('AutonewsletterGroup', 'AutonewsletterGroup', '`AutonewsletterGroup`.`autonewsletter` = `Autonewsletter`.`id`');
$c->leftJoin('Group', 'Group', '`Group`.`id` = `AutonewsletterGroup`.`group`');
$c->groupby('`Autonewsletter`.`id`');

$c->select(array(
    'autonewsletter' => '`Autonewsletter`.*',
    'subject' => '`Document`.`pagetitle`',
    'date' => '`Document`.`publishedon`',
    'groups' => 'GROUP_CONCAT(";", CONCAT_WS(",", Group.id,Group.name,Group.color))'
));

$newsletters = $modx->getCollection('Autonewsletter',$c);

/* iterate through newsletters */
$list = array();
foreach ($newsletters as $newsletter) {
    $newsletter = $newsletter->toArray();
    
    $newsletter['last'] = $newsletter['last'] ? date('D, d.m.Y', $newsletter['last']) : null;
    $newsletter['start'] = date('D, d.m.Y', $newsletter['start']);
        
    $groups = explode(';', $newsletter['groups']);
    $grpArray = array();
    foreach($groups as $grp) {
      if(false === strpos($grp, ',')) continue;
        $grpArray[] = explode(',', $grp);
    }
    $newsletter['groups'] = $grpArray;
        
    $list[] = $newsletter;
}
return $this->outputArray($list,$count);