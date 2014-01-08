<?php
$modx->lexicon->load('campaigner');
$file = $_REQUEST['file'];
if(pathinfo($file, PATHINFO_EXTENSION) !== 'csv')
	return $modx->error->failure($modx->lexicon('campaigner.subscriber.import.analyze.wrong_format'));

// $handle = fopen($modx->getOption('base_path') . $file, 'r');
// if($handle === FALSE)
// 	return $modx->error->failure($modx->lexicon('campaigner.subscriber.import.analyze.cannot_read'));	

$file = new SplFileObject($modx->getOption('base_path') . $file);
$concept['controls'] = $file->getCsvControl();
$header = explode($concept['controls'][0], $file->current());
$concept['line'] = explode($concept['controls'][0], $file->next());

// Prepare the header for ExtJS output
$head_arr = array();
foreach($header as $key => $value) {
	$head_arr[]['name'] = $value;
}
$concept['header'] = $head_arr;
// var_dump($concept);
return $modx->error->success($modx->lexicon('campaigner.subscriber.import.analyze.valid'), $concept);