<?php
require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$campaignerCorePath = $modx->getOption('campaigner.core_path',null,$modx->getOption('core_path').'components/campaigner/');
require_once $campaignerCorePath.'model/campaigner/campaigner.class.php';
$modx->campaigner = new Campaigner($modx);

$modx->lexicon->load('campaigner:default');
// $modx->lexicon->load('campaigner:inspector');
$modx->addPackage('campaigner', $campaignerCorePath .'model/', 'camp_');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->campaigner->config,$campaignerCorePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
