<?php
// use modx
require_once dirname(__FILE__) . '/../../../../config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';

include_once (MODX_CORE_PATH . "model/modx/modx.class.php");

$modx= new modX();
$modx->initialize('web');
// $modx->setOption('cultureKey', 'en');
// get the model
$campaigner = $modx->getService('campaigner', 'Campaigner', $modx->getOption('campaigner.core_path'). 'model/campaigner/');
if (!($campaigner instanceof Campaigner)) return;

echo $campaigner->createAutoresponder();
die();
$campaigner->sheduleAutoNewsletter();
$campaigner->createQueue();
$campaigner->processQueue();
// return;
// NEW
// Inspect the data and send some useful information
// $inspector = $modx->getService('inspector', 'Inspector', $modx->getOption('campaigner.core_path'). 'model/campaigner/');
// if (!($inspector instanceof Inspector)) return;
// $options = array(
// 	'recipients' => 'andreas.bilz@gmail.com',
// 	'debug' => true,
// 	'limit'	=> 3,
// 	// 'mode'	=> 'strict',
// 	'message' => 'Ein Beispiel eines Inspektor-Emails zur Ansicht',
// 	'reports' => array(
// 		0 => 'newsletter',
// 		1 => 'overdue',
// 		2 => 'failed',
// 		// 3 => 'unsubs',
// 		)
// 	);
// $inspector->run($options);