<?php
// use modx
require_once dirname(__FILE__) . '/../../../../config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';

include_once (MODX_CORE_PATH . "model/modx/modx.class.php");

$modx= new modX();
$modx->initialize('web');

// get the model
$campaigner = $modx->getService('campaigner', 'Campaigner', $modx->getOption('core_path'). 'components/campaigner/model/campaigner/');
if (!($campaigner instanceof Campaigner)) return;

$campaigner->sheduleAutoNewsletter();
$campaigner->createQueue();
$campaigner->processQueue();