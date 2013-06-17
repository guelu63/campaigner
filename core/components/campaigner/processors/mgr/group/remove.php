<?php
$id = (int) $_REQUEST['id'];

$group = $modx->getObject('Group', $id);

if ($group == null) {
	return $modx->error->failure($modx->lexicon('campaigner.group.notfound'));
}

$sql = 'DELETE FROM `camp_group_subscriber` WHERE `group` = '. $id;
$modx->query($sql);

// Remove group
$group->remove();

return $modx->error->success('campaigner.group.removed');