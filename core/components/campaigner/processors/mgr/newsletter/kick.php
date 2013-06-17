<?php
/**
 * Sends newsletters immediately
 *
 * Creates the newsletter dependent queue and immediately sends out
 * the first batch of them.
 *
 * @todo Report after successful sending as console: Sent / Time / ...
 */
// return $modx->error->success('',$newsletter);

if(!empty($_POST['id'])) {
    $newsletter = $modx->getObject('Newsletter', array('id' => $_POST['id']));
}

if(!$newsletter) return $modx->error->failure($modx->lexicon('campaigner.newsletter.error.notfound'));

$now = time();
$today = mktime(0,0,0,strftime('%m'),strftime('%d'),strftime('%Y'));
$start = $today - $newsletter->get('frequency');
$time = strftime('%H:%M:%S', $now-120);

// Set state to 1 => Approve
$data = array(
    'state' => '1',
    'sent_date' => NULL,
);

$newsletter->fromArray($data);

if ($newsletter->save() == false)
    return $modx->error->failure($modx->lexicon('campaigner.error.save'));

// Create queue and process immediately
$modx->campaigner->createQueue();
// $modx->campaigner->processQueue();

// Set sent_date to now
$newsletter->fromArray(array('state' => '1', 'sent_date' => time()));

if ($newsletter->save() == false)
    return $modx->error->failure($modx->lexicon('campaigner.error.save'));

/**
 * Feed the manager log
 */
$user = $modx->getAuthenticatedUser('mgr');
$l = $modx->newObject('modManagerLog');
$data = array(
    'user'      => $user->get('id'),
    'occurred'  => date('Y-m-d H:i:s'),
    'action'    => 'trigger_autonewsletter',
    'classKey'  => 'Autonewsletter',
    'item'      => $newsletter->get('id')
);

$l->fromArray($data);
$l->save();

return $modx->error->success('',$newsletter);