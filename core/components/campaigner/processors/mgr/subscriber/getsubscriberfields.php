<?php

// $c = $modx->newQuery('SubscriberFields');
// $c->where(array('subscriber' => $_POST['subscriber']));
// $c->leftJoin('Fields', 'Fields');
// $c->select($modx->getSelectColumns('SubscriberFields', 'SubscriberFields'));
// $c->select(array(
//   '`Fields`.`name`, `Fields`.`label`, `Fields`.`type`'
// ));
// 
$c = $modx->newQuery('Fields');
$c->leftJoin('SubscriberFields', 'SubscriberFields', 'SubscriberFields.field = Fields.id AND SubscriberFields.subscriber = ' . $_POST['subscriber']);
$c->select($modx->getSelectColumns('Fields', 'Fields', '', array('id', 'name', 'label', 'type', 'required', 'values')));
$c->select(array(
  '`SubscriberFields`.`value`'
));
$c->where(array('Fields.active' => 1));
$c->prepare();
// echo $c->toSQL();
$subfields = $modx->getCollection('SubscriberFields', $c);
$count = $modx->getCount('SubscriberFields',$c);
/* iterate through subscribers */
$list = array();
foreach ($subfields as $subfield) {
	$subfield = $subfield->toArray();
	$i = 0;
	if(empty($subfield['values'])) {
		$list[] = $subfield;
		continue;
	}
	$values = explode(',', $subfield['values']);
	foreach($values as $value) {
		$val_arr[] = array('id' => $value, 'name' => $value);
	}
	$subfield['values'] = json_encode($val_arr);
    $list[] = $subfield;
}
return $modx->error->success('',$list);