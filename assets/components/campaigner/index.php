<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('web');

$campaignerCorePath = $modx->getOption('campaigner.core_path',null,$modx->getOption('core_path').'components/campaigner/');
require_once $campaignerCorePath.'model/campaigner/campaigner.class.php';
$modx->campaigner = new Campaigner($modx);
echo $_SERVER['REMOTE_ADDR'];
$modx->lexicon->load('campaigner:default');
$modx->addPackage('campaigner', $campaignerCorePath .'model/', 'camp_');
// $modx->log(MODX_LOG_LEVEL_ERROR, 'tracking image requested');
$modx->campaigner->logAction('image');