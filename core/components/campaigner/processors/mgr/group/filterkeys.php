<?php
/**
 * Processor: Get the keys for the filter combo box
 */
$fields = $modx->getFields('Subscriber');
$flds = array();
$i = 0;
foreach($fields as $key => $value) {
	$flds[$i]['key'] = $key;
	$flds[$i]['value'] = strtoupper($key);
	$i++;
}
return $this->outputArray($flds);
return $modx->error->success('',$flds);
return json_encode($flds);