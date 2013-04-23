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

/* query for subscribers */
$c = $modx->newQuery('ResendCheck');

$count = $modx->getCount('ResendCheck',$c);

$c->leftJoin('Queue', 'Queue', '`Queue`.`id` = `ResendCheck`.`queue_id`');
$c->leftJoin('Newsletter', 'Newsletter', '`Newsletter`.`id` = `Queue`.`newsletter`');
$c->leftJoin('modDocument', 'Document', '`Document`.`id` = `Newsletter`.`docid`');
$c->leftJoin('Subscriber', 'Subscriber', '`Subscriber`.`id` = `Queue`.`subscriber`');

$c->select('`ResendCheck`.*, `Queue`.`subscriber`, `Queue`.`newsletter`, `Queue`.`sent`, `Newsletter`.`docid`, `Document`.`pagetitle` AS newsletterTitle, `Document`.`publishedon` AS date, `Subscriber`.`firstname`, `Subscriber`.`lastname`, `Subscriber`.`email`, `Queue`.`sent` IS NULL AS isnull');
$c->sortby('isnull DESC, `Queue`.`sent`','DESC');

$resends = $modx->getCollection('ResendCheck',$c);

//$c->prepare(); var_dump($c->toSQL());

/* iterate through bounce messages */
$list = array();
foreach ($resends as $item) {
    $bounceItem = $item->toArray();
    
    //Wenn der Status keinen gueltigen Wert hat
    if($bounceItem['state'] < 0) {
        //Dann soll eine Standardausgabe ausgegeben werden
        $bounceItem['state_msg'] = $modx->lexicon('campaigner.bounce.resend.state');
    }
    //Wenn der Status gueltig ist
    else {
        //Dann gibt es im Lexikon einen dazugehoerigen Eintrag, der dann angezeigt wird
        $bounceItem['state_msg'] = $modx->lexicon('campaigner.bounce.resend.state'.$bounceItem['state']);
    }
    
    if(empty($bounceItem['sent'])) {
        $bounceItem['sent'] = "-";
    }
    else {
        $bounceItem['sent'] = date('d.m.Y H:i:s', $bounceItem['sent']);
        //Sicherheitsabfrage
        //Wenn die Queue das Element schon gesendet hat, dann sollte auch der state nicht 0 sein
        if($bounceItem['state'] == 0) {
            $bounceItem['state_msg'] = "Fehler (inkonsistente Daten)";
        }
    }
        
    if($bounceItem['firstname']==NULL) {
        $bounceItem['firstname'] = "-";
    }
    if($bounceItem['lastname']==NULL) {
        $bounceItem['lastname'] = "-";
    }
    
    $bounceItem['name'] = $bounceItem['firstname'].' '.$bounceItem['lastname'];
    
    $list[] = $bounceItem;
}
return $this->outputArray($list,$count);