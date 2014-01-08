<?php
$modx->regClientCss($campaigner->config['assetsUrl'].'css/mgr/style.css');
$modx->regClientCss($campaigner->config['assetsUrl'].'js/utils/colorfield/ext-ux/ColorField/Ext.ux.Colorfield.css');
$modx->regClientCss($campaigner->config['assetsUrl'].'css/mgr/fileuploader.css');

$modx->regClientStartupScript($campaigner->config['jsUrl'].'utils/fileuploader.js');
$modx->regClientStartupScript($campaigner->config['jsUrl']. '/utils/colorfield/ext-ux/ColorField/Ext.ux.Colorfield.js');
$modx->regClientStartupScript($campaigner->config['jsUrl']. '/utils/ExtJS.ux.GMapPanel/src/Ext.ux.GMapPanel3.js');
$modx->regClientStartupScript($campaigner->config['jsUrl'].'campaigner.js');
// $modx->regClientStartupScript($campaigner->config['jsUrl'].'colorpicker.js');
$modx->regClientStartupScript($campaigner->config['jsUrl'].'utils/ddview.js');
$modx->regClientStartupScript('http://maps.google.com/maps/api/js?sensor=false');
// $modx->regClientStartupScript('http://maps.google.com/maps?file=api&amp;v=3.x&amp;key=ABQIAAAAJDLv3q8BFBryRorw-851MRT2yXp_ZAY8_ufC3CFXhHIE1NvwkxTyuslsNlFqyphYqv1PCUD8WrZA2A');
$ckeditor = $modx->getService('ckeditor','CKEditor',$modx->getOption('ckeditor.core_path',null,$modx->getOption('core_path').'components/ckeditor/').'model/ckeditor/');
$ckeditor->initialize();

// $tinyCorePath = $modx->getOption('tiny.core_path',null,$modx->getOption('core_path').'components/tinymce/');
// if (file_exists($tinyCorePath.'tinymce.class.php')) {

//     $plugins =  $modx->getOption('gallery.tiny.custom_plugins');
//     $theme =  $modx->getOption('gallery.tiny.theme');
    
//     /* If the settings are empty, override them with the generic tinymce settings. */
//     $tinyProperties = array(
//         'height' => $modx->getOption('gallery.tiny.height',null,200),
//         'width' => $modx->getOption('gallery.tiny.width',null,400),
//         'valid_elements' => '*[*]',
//         'tiny.custom_plugins' => (!empty($plugins)) ? $plugins : $modx->getOption('tiny.custom_plugins'),
//         'tiny.editor_theme' => (!empty($theme)) ? $theme : $modx->getOption('tiny.editor_theme'),
//     );
    
//     require_once $tinyCorePath.'tinymce.class.php';
//     $tiny = new TinyMCE($modx,$tinyProperties);
//     $tiny->setProperties($tinyProperties);
//     $html = $tiny->initialize();
//     $modx->regClientHTMLBlock($html);
// }
// PRODUCTION
// $perms = $modx->getCollection('modAccessPermission', array('name:LIKE' => 'campaigner.%'));

// DEVELOPMENT
$perms = array(
	'campaigner.tab_newsletter' => 1,
	'campaigner.tab_autonewsletter' => 1,
	'campaigner.tab_groups' => 1,
	'campaigner.tab_subscriber' => 1,
	'campaigner.tab_bouncing' => 1,
	'campaigner.tab_queue' => 1,
	'campaigner.tab_statistics' => 1,
	'campaigner.tab_sharing' => 1,
	'campaigner.tab_fields' => 1,
	'campaigner.tab_autoresponders' => 1,

	'campaigner.newsletter_approve' => 1,
	'campaigner.newsletter_remove' => 1,
	'campaigner.newsletter_remove_batch' => 1,
	'campaigner.newsletter_kick' => 1,
	'campaigner.newsletter_edit' => 1,
	'campaigner.newsletter_editprops' => 1,
	'campaigner.newsletter_editgroups' => 1,
	'campaigner.newsletter_editarticles' => 1,
	'campaigner.newsletter_clean' => 1,
	'campaigner.newsletter_clean_dehtml' => 1,
	'campaigner.newsletter_clean_trash' => 1,
	'campaigner.newsletter_clean_archive' => 1,
	'campaigner.newsletter_preview' => 1,
	'campaigner.newsletter_sendtest' => 1,

	'campaigner.autonewsletter_approve' => 1,
	'campaigner.autonewsletter_kick' => 1,
	'campaigner.autonewsletter_editgroups' => 1,
	'campaigner.autonewsletter_editprops' => 1,
	'campaigner.autonewsletter_edit' => 1,
	'campaigner.autonewsletter_preview' => 1,
	'campaigner.autonewsletter_sendtest' => 1,
	
	'campaigner.group_create' => 1,
	'campaigner.group_edit' => 1,
	'campaigner.group_remove' => 1,
	'campaigner.group_assignment' => 1,
	
	'campaigner.subscriber_create' => 1,
	'campaigner.subscriber_edit' => 1,
	'campaigner.subscriber_remove' => 1,
	'campaigner.subscriber_togglestatus' => 1,
	'campaigner.subscriber_remove_batch' => 1,
	'campaigner.subscriber_togglestatus_batch' => 1,
	'campaigner.subscriber_import' => 1,
	'campaigner.subscriber_export' => 1,
	'campaigner.subscriber_export_csv' => 1,
	'campaigner.subscriber_export_xml' => 1,
	'campaigner.subscriber_showstats' => 1,
	
	'campaigner.bounce_fetch' => 1,
	'campaigner.bounce_hard_remove' => 1,
	'campaigner.bounce_hard_togglestatus' => 1,
	'campaigner.bounce_soft_remove' => 1,
	'campaigner.bounce_soft_togglestatus' => 1,
	'campaigner.bounce_resend_remove' => 1,
	'campaigner.bounce_soft_remove_batch' => 1,
	'campaigner.bounce_soft_activate_batch' => 1,
	'campaigner.bounce_soft_deactivate_batch' => 1,
	
	'campaigner.queue_process' => 1,
	'campaigner.queue_remove' => 1,
	'campaigner.queue_remove_tests' => 1,
	'campaigner.queue_remove_batch' => 1,
	'campaigner.queue_send' => 1,
	'campaigner.queue_send_batch' => 1,
	'campaigner.queue_set_state' => 1,
	
	'campaigner.statistics_showdetails' => 1,
	'campaigner.statistics_export' => 1,
	'campaigner.statistics_opens_export' => 1,
	'campaigner.statistics_clicks_export' => 1,
	'campaigner.statistics_unsubscriptions_export' => 1,
	
	'campaigner.sharing_create' => 1,
	'campaigner.sharing_edit' => 1,
	'campaigner.sharing_remove' => 1,
	'campaigner.sharing_togglestatus' => 1,
	'campaigner.sharing_dragsort' => 1,

	'campaigner.field_create' => 1,
	'campaigner.field_edit' => 1,
	'campaigner.field_remove' => 1,
	);

foreach($perms as $perm => $value) {
	// DEVELOPMENT
	$js_perms .= 'MODx.perm.' . str_replace('campaigner.', '', $perm) . ' = '. ($value === 1 ? 1 : 'false') .';' . "\n";
	
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
    Campaigner.config.assets_url = "'.$campaigner->config['assetsUrl'].'";
    Campaigner.request = '.$modx->toJSON($_GET).';
    Campaigner.action = "'.(!empty($_REQUEST['a']) ? $_REQUEST['a'] : 0).'";
    Campaigner.site_id = "'. $modx->site_id .'";
});
</script>');

return '';
