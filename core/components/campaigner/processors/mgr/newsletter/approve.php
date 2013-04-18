<?php

if(!empty($_POST['id'])) {
    $newsletter = $modx->getObject('Newsletter', array('id' => $_POST['id']));
}

if(!$newsletter) return $modx->error->failure($modx->lexicon('campaigner.newsletter.error.notfound'));

$newsletter->fromArray(array('state' => '1'));

if ($newsletter->save() == false) {
    return $modx->error->failure($modx->lexicon('campaigner.error.save'));
}

/**
 * Feed the manager log
 */
$user = $modx->getAuthenticatedUser('mgr');
$l = $modx->newObject('modManagerLog');
$data = array(
    'user'      => $user->get('id'),
    'occurred'  => date('Y-m-d H:i:s'),
    'action'    => 'approve_newsletter',
    'classKey'  => 'Newsletter',
    'item'      => $newsletter->get('id')
);

$l->fromArray($data);
$l->save();

return $modx->error->success('',$newsletter);