<?php
$modx->regClientCss($campaigner->config['assetsUrl'].'css/mgr/style.css');
$modx->regClientStartupScript($campaigner->config['jsUrl'].'campaigner.js');
$modx->regClientStartupScript($campaigner->config['jsUrl'].'colorpicker.js');

// PRODUCTION
// $perms = $modx->getCollection('modAccessPermission', array('name:LIKE' => 'campaigner.%'));

// DEVELOPMENT
$perms = array(
	'campaigner.newsletter_approve',
	'campaigner.newsletter_remove',
	'campaigner.newsletter_remove_batch',
	'campaigner.newsletter_kick',
	'campaigner.newsletter_edit',
	'campaigner.newsletter_editprops',
	'campaigner.newsletter_editgroups',
	'campaigner.newsletter_clean',
	'campaigner.newsletter_clean_dehtml',
	'campaigner.newsletter_clean_trash',
	'campaigner.newsletter_clean_archive',
	'campaigner.newsletter_preview',
	'campaigner.newsletter_sendtest',

	'campaigner.autonewsletter_approve',
	'campaigner.autonewsletter_kick',
	'campaigner.autonewsletter_editgroups',
	'campaigner.autonewsletter_editprops',
	'campaigner.autonewsletter_edit',
	'campaigner.autonewsletter_preview',
	'campaigner.autonewsletter_sendtest',
	
	'campaigner.groups_create',
	'campaigner.groups_edit',
	'campaigner.groups_remove',
	'campaigner.group_assignment',
	
	'campaigner.subscriber_create',
	'campaigner.subscriber_edit',
	'campaigner.subscriber_remove',
	'campaigner.subscriber_remove_batch',
	'campaigner.subscriber_togglestatus',
	'campaigner.subscriber_import',
	'campaigner.subscriber_export',
	'campaigner.subscriber_export_csv',
	'campaigner.subscriber_export_xml',
	'campaigner.subscriber_showstats',
	
	'campaigner.bounce_fetch',
	'campaigner.bounce_hard_remove',
	'campaigner.bounce_hard_togglestatus',
	'campaigner.bounce_soft_remove',
	'campaigner.bounce_soft_togglestatus',
	'campaigner.bounce_resend_remove',
	'campaigner.bounce_soft_remove_batch',
	'campaigner.bounce_soft_activate_batch',
	'campaigner.bounce_soft_deactivate_batch',
	
	'campaigner.queue_process',
	'campaigner.queue_remove',
	'campaigner.queue_remove_tests',
	'campaigner.queue_remove_batch',
	'campaigner.queue_send',
	'campaigner.queue_send_batch',
	
	'campaigner.statistics_showdetails',
	'campaigner.statistics_export',
	'campaigner.statistics_opens_export',
	'campaigner.statistics_clicks_export',
	'campaigner.statistics_unsubscriptions_export',
	
	'campaigner.sharing_create',
	'campaigner.sharing_edit',
	'campaigner.sharing_remove',
	'campaigner.sharing_togglestatus',
	'campaigner.sharing_dragsort',

	'campaigner.field_create',
	'campaigner.field_edit',
	'campaigner.field_remove',
	);

foreach($perms as $perm) {
	// DEVELOPMENT
	$js_perms .= 'MODx.perm.' . str_replace('campaigner.', '', $perm) . ' = 1;' . "\n";
	
	// PRODUCTION
	// $js_perms .= 'MODx.perm.' . str_replace('campaigner.', '', $perm->get('name')) . ' = ' . ($modx->hasPermission($perm->get('name')) ? 1 : 0) . ';' . "\n";
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
