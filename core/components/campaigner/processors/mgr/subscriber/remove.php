<?php
$id = (int) $_REQUEST['id'];

$subscriber = $modx->getObject('Subscriber', $id);

if ($subscriber == null) {
	return $modx->error->failure($modx->lexicon('campaigner.subscriber.notfound'));
}

$unsub = $this->modx->newObject('Unsubscriber');
$data = array(
    'subscriber' => $subscriber->get('email'),
    'date' => time(),
    'via' => 'admin',
    );
$unsub->fromArray($data);
if($unsub->save())

// Remove group
$subscriber->remove();

return $modx->error->success('campaigner.subscriber.removed');