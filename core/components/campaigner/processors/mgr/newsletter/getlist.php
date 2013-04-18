<?php
/**
 * Get a list of newsletters
 * 
 * Optional filtering parameters
 *
 * @package ditsnews
 * @subpackage processors.newsletters.list
 */


/* setup default properties */
$isLimit    = !empty($_REQUEST['limit']);
$start      = $modx->getOption('start',$_REQUEST,0);
$limit      = $modx->getOption('limit',$_REQUEST,10);
$sort       = $modx->getOption('sort',$_REQUEST,'id');
$dir        = $modx->getOption('dir',$_REQUEST,'DESC');
$state      = $modx->getOption('state',$_REQUEST,null);
$sent       = $modx->getOption('sent',$_REQUEST,null);
$media      = $modx->getOption('media',$_REQUEST,null);
$auto	    = $modx->getOption('auto',$_REQUEST,null);
$sort = 'sent_date';

/* query for newsletters */
$c = $modx->newQuery('Newsletter');
if(isset($state)) {
        $c->where(array('state' => $state));
}
if(isset($sent)) {
        $op = $sent == 1 ? ':!=' : ':IS';
        $c->where(array('sent_date'.$op => null));
}

/**
 * @todo Create system settings
 */
if(isset($media) && $media == 1) {
    // Fetch all season-containers
    $containers = $modx->getChildIds(18787, 10, array('context' => 'web', 'isfolder' => 1));
    $containers = array(18787,561357);
    // 'width:IN' => array(15,16,17,20))
    // $c->where('`Document`.`parent`:IN "18787"');
    $c->where(array('`Document`.`parent`:IN' => $containers));
}

if(isset($auto) && $auto == 1) {
	$c->where(array('auto' => NULL));
}

$c->leftJoin('modDocument', 'Document', '`Document`.`id` = `Newsletter`.`docid`');

$count = $modx->getCount('Newsletter',$c);

$c->leftJoin('NewsletterGroup', 'NewsletterGroup', '`NewsletterGroup`.`newsletter` = `Newsletter`.`id`');
$c->leftJoin('Group', 'Group', '`Group`.`id` = `NewsletterGroup`.`group`');
$c->groupby('`Newsletter`.`id`');

$c->select(array(
    'newsletter' => '`Newsletter`.*',
    'subject' => '`Document`.`pagetitle`',
    'date' => '`Document`.`publishedon`',
    'groups' => 'GROUP_CONCAT(";", CONCAT_WS(",", Group.id,Group.name,Group.color))'
));

$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);

$newsletters = $modx->getCollection('Newsletter',$c);

/* iterate through newsletters */
$list = array();
foreach ($newsletters as $newsletter) {
    $newsletter              = $newsletter->toArray();
    $newsletter['sent_date'] = ($newsletter['sent_date']) ? date('d.m.Y H:i:s', $newsletter['sent_date']) : '';
	
    #$newsletter['date']      = $newsletter['date'];
    $newsletter['date']      = ($newsletter['date'] > 0) ? date('d.m.Y H:i:s', $newsletter['date']) : $modx->lexicon('unpublished');
        
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
