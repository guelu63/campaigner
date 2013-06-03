<?php
$field = $modx->getObject('Fields', $_POST['key']);
$list = array();
$values = explode(',', $field->get('values'));
foreach($values as $value) {
	$list[] = array('id' => $value, 'name' => $value);
}
$modx->log(xPDO::LOG_LEVEL_ERROR, 'Values sent back to JS: '. $this->outputArray($list));
return $this->outputArray($list);
return $modx->error->success('',$list);