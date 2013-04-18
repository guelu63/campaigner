<?php
$id = (int) $_REQUEST['id'];

$subscriber = $modx->getObject('Subscriber', $id);

if ($subscriber == null) {
	return $modx->error->failure($modx->lexicon('campaigner.subscriber.notfound'));
}

// Remove group
$subscriber->remove();

return $modx->error->success('campaigner.subscriber.removed');