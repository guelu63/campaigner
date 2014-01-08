<?php
/**
 * Processor Get filter results
 *
 * Returns count and subscriber ids
 */

$data = json_decode($_REQUEST['data'], true);
// var_dump($data);
$c = $modx->newQuery('Subscriber');

// Check if we are dealing with multiple filters
// In case an iteration is needed
if(!is_array($data['key'])) {
	if(empty($data['value']) || $data['value'] == '')
		return;
	$condition = $data['condition'];
	$c->where(array(
		$data['key'] . ':' . $data['operator'] => $data['value'] . ($data['operator'] == 'LIKE' ? '%' : '')
	), constant("xPDOQuery::SQL_$condition"));
} else {
	$i = 0;
	foreach($data['key'] as $k) {
		if(empty($data['value'][$i]) || $data['value'][$i] == '')
			return;
		$condition = $data['condition'][$i];
		$c->where(array(
			$k . ':' . $data['operator'][$i] => $data['value'][$i] . ($data['operator'][$i] == 'LIKE' ? '%' : '')
		), constant("xPDOQuery::SQL_$condition"));
		$i++;
	}
}

$c->select($modx->getSelectColumns('Subscriber', 'Subscriber', '', array('id', 'email', 'firstname', 'lastname', 'since')));
$c->prepare();
// echo $c->toSQL();
// echo "\n\n";
// return;
$subs = $modx->getCollection('Subscriber', $c);
$count = $modx->getCount('Subscriber', $c);
$sub_arr = array();
foreach($subs as $sub) {
	$sub_arr[] = $sub->toArray('', false, true);
}
return $modx->error->success('', array('count' => $count, 'data' => $sub_arr));