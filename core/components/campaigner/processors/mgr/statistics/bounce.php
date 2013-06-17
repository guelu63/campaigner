<?php
$stats_id = $_POST['statistics_id'];

$c = $modx->newQuery('Bounces');

$c->where(array('newsletter' => $stats_id));

$c->sortby("`Bounces`.`recieved`",$dir);
if ($isLimit) $c->limit($limit,$start);

$count = $modx->getCount('Bounces',$c);

$c->leftJoin('Newsletter', 'Newsletter', '`Newsletter`.`id` = `Bounces`.`newsletter`');
$c->leftJoin('Subscriber', 'Subscriber', '`Subscriber`.`id` = `Bounces`.`subscriber`');
$c->leftJoin('modDocument', 'Document', '`Document`.`id` = `Newsletter`.`docid`');

//$c->select('`Newsletter`.`docid`, `Newsletter`.`total`, `Newsletter`.`sent`, `Newsletter`.`state`, `Newsletter`.`bounced`, `Document`.`pagetitle` AS subject, `Document`.`publishedon` AS date, `Queue`.*, `Subscriber`.`firstname`, `Subscriber`.`lastname`, `Subscriber`.`email`, `Subscriber`.`text`');
$c->select('`Subscriber`.`firstname`, `Subscriber`.`lastname`, `Subscriber`.`email`, `Document`.`pagetitle` AS newsletterTitle, `Bounces`.*, `Newsletter`.`docid`');
//$c->select('`Subscriber`.`firstname`, `Subscriber`.`lastname`, `Subscriber`.`email`, `Bounces`.*');
// $c->prepare();
// echo $c->toSQL();
//$c->prepare(); var_dump($c->toSQL()); die;

$bounce = $modx->getCollection('Bounces',$c);

/* iterate through bounce messages */
$list = array();
foreach ($bounce as $item) {
    $bounceItem = $item->toArray();
    //today - startDate = timeOfBouncesActivity -> /60 = min, /60 = h, /24 = tage
    $now = time();
    $diff = $modx->getOption('campaigner.days_until_deletion')*24*60*60 - (($now-$bounceItem['date']));
    //Wenn erst in Zukunft geloescht werden soll
    if($diff > 0) {
        $days = floor($diff/60/60/24);
        $hours = floor($diff/60/60 - $days*24);
        $minutes = floor($diff/60 - $hours*60 - $days*24*60);
        $seconds = floor($diff - $minutes*60 - $hours*60*60 - $days*60*60*24);
        $bounceItem['state'] = $modx->lexicon('campaigner.bounce.deactivateIn')." ".$days."d ".$hours."h ".$minutes."m ".$seconds."s";
    }
    elseif($diff < 0) {
        $bounceItem['state'] = $modx->lexicon('campaigner.bounce.alreadyDeactivated');
    }
    
    $bounceItem['date'] = date('d.m.Y H:i:s', $bounceItem['date']);
    if($bounceItem['firstname']==NULL) {
        $bounceItem['firstname'] = "-";
    }
    if($bounceItem['lastname']==NULL) {
        $bounceItem['lastname'] = "-";
    }
    
    $bounceItem['name'] = $bounceItem['firstname'].' '.$bounceItem['lastname'];
    $bounceItem['count'] = "TODO";
    $bounceItem['last'] = "TODO";
    
    $list[] = $bounceItem;
}
return $this->outputArray($list,$count);