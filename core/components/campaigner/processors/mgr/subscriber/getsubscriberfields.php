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
$c->leftJoin('SubscriberFields', 'SubscriberFields');
$c->select($modx->getSelectColumns('Fields','Fields','',array('id', 'name', 'label', 'type', 'required', 'values')));
$c->select(array(
  '`SubscriberFields`.`value`'
));
$c->prepare();
// echo $c->toSQL();
$subfields = $modx->getCollection('SubscriberFields', $c);
$count = $modx->getCount('SubscriberFields',$c);
/* iterate through subscribers */
$list = array();
foreach ($subfields as $subfield) {
	$subfield = $subfield->toArray();
	if(!empty($subfield['values']))
		$subfield['values'] = json_encode(explode(',',$subfield['values']));
    $list[] = $subfield;
}
return $modx->error->success('',$list);