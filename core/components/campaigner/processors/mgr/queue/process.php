<?php
$ids = explode(',', $_REQUEST['marked']);
$modx->campaigner->processQueue($ids);
// /**
//  * Feed the manager log
//  */
// $user = $modx->getAuthenticatedUser('mgr');
// $l = $modx->newObject('modManagerLog');
// $data = array(
//     'user'      => $user->get('id'),
//     'occurred'  => date('Y-m-d H:i:s'),
//     'action'    => 'trigger_queue',
//     'classKey'  => 'Queue',
//     'item'      => $newsletter->get('id')
// );

// $l->fromArray($data);
// $l->save();
return $modx->error->success('',$newsletter);