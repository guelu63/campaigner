<?php
$subscriber = (int) $_REQUEST['subscriber'];
$newsletter = (int) $_REQUEST['newsletter'];

//return $modx->error->failure("Subscriber: ".$subscriber." - Newsletter: ".$newsletter);

//Neues Queue Element erstellen
$newQueueItem = $modx->newObject('Queue');
$newQueueItem->fromArray(array(
    'newsletter' => $newsletter,
    'subscriber' => $subscriber,
    'state'      => 8,
    'priority'  => 1
));
$newQueueItem->save();

//md5 verschluesselte QueueID als key-Attribut mitspeichern
$newQueueItem->set('key', md5($newQueueItem->get('id')));
$newQueueItem->save();

//return $modx->error->failure("Queue-ID-Test: ".$newQueueItem->get('id'));

//Neues ResendCheck Element erstellen
$newResendCheckItem = $modx->newObject('ResendCheck');
$newResendCheckItem->fromArray(array(
    'queue_id' => $newQueueItem->get('id'),
    'state'    => 0
));
$newResendCheckItem->save();

return $modx->error->success('campaigner.subscriber.removed');