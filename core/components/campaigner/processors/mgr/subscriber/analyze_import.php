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
$concept['header'] = explode(',', $file->current());
$concept['line'] = explode(',', $file->next());

return $modx->error->success($modx->lexicon('campaigner.subscriber.import.analyze.valid'), $concept);