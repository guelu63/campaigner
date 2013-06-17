<?php
/**
 * Remove test items from the queue
 */
$rt = $modx->removeCollection('Queue', array('priority' => 0));
return $modx->error->success('',$queue);