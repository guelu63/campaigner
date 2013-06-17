<?php
/**
 * Updates the sort order of the fields
 */
// var_dump($_REQUEST);
$order = json_decode($_REQUEST['fields'], true);
$fields = $modx->getCollection('Fields', array_values($order));
foreach ($fields as $field) {
	$field->set('menuindex', array_search($field->get('id'), $order));
	// echo array_search($field->get('id'), $order);
	$field->save();
}
return $modx->error->success();