<?php
$id = (int) $_REQUEST['id'];

$queue = $modx->getObject('Queue', $id);

if ($queue == null) {
    return $modx->error->failure($modx->lexicon('campaigner.queue.error.notfound'));
}

if($queue->state == 0) {
    $sql = 'UPDATE `camp_newsletter` SET `total` = `total` - 1 WHERE `id` = '. $queue->get('newsletter');
    $modx->query($sql);
}

// Remove group
$queue->remove();

return $modx->error->success('campaigner.queue.removed');