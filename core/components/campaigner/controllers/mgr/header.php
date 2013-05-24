<?php
$modx->regClientCss($campaigner->config['assetsUrl'].'css/mgr/style.css');
$modx->regClientStartupScript($campaigner->config['jsUrl'].'campaigner.js');
$modx->regClientStartupScript($campaigner->config['jsUrl'].'colorpicker.js');

$perms = $modx->getCollection('modAccessPermission', array('name:LIKE' => 'campaigner.%'));
foreach($perms as $perm) {
	$js_perms .= 'MODx.perm.' . str_replace('campaigner.', '', $perm->get('name')) . ' = ' . ($modx->hasPermission($perm->get('name')) ? 1 : 0) . ';' . "\n";
}
// echo $js_perms;

$modx->regClientStartupHTMLBlock('<script type="text/javascript">
Ext.onReady(function() {
	'.$js_perms.'
    Campaigner.config = '.$modx->toJSON($campaigner->config).';
    Campaigner.config.connector_url = "'.$campaigner->config['connectorUrl'].'";
    Campaigner.config.base_url = "'.$campaigner->config['baseUrl'].'";
    Campaigner.request = '.$modx->toJSON($_GET).';
    Campaigner.action = "'.(!empty($_REQUEST['a']) ? $_REQUEST['a'] : 0).'";
    Campaigner.site_id = "'. $modx->site_id .'";
});
</script>');

return '';
