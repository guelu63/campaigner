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
// $ckeditor = $modx->getService('ckeditor','CKEditor',$modx->getOption('ckeditor.core_path',null,$modx->getOption('core_path').'components/ckeditor/').'model/ckeditor/');
// $ckeditor->initialize();

// $tinyCorePath = $modx->getOption('tiny.core_path',null,$modx->getOption('core_path').'components/tinymcerte/');
// if (file_exists($tinyCorePath.'model/tinymcerte/tinymcerte.class.php')) {
//
//     $plugins =  $modx->getOption('gallery.tiny.custom_plugins');
//     $theme =  $modx->getOption('gallery.tiny.theme');
//
//     /* If the settings are empty, override them with the generic tinymce settings. */
//     $tinyProperties = array(
//         'height' => $modx->getOption('gallery.tiny.height',null,200),
//         'width' => $modx->getOption('gallery.tiny.width',null,400),
//         'valid_elements' => '*[*]',
//         'tiny.custom_plugins' => (!empty($plugins)) ? $plugins : $modx->getOption('tiny.custom_plugins'),
//         'tiny.editor_theme' => (!empty($theme)) ? $theme : $modx->getOption('tiny.editor_theme'),
//     );
//
//     require_once $tinyCorePath.'model/tinymcerte/tinymcerte.class.php';
//     $tiny = new TinyMCERTE($modx,$tinyProperties);
//     // $tiny->setProperties($tinyProperties);
//     $html = $tiny->initialize();
//     $modx->regClientHTMLBlock($html);
// }


$modx->regClientStartupScript($modx->getOption('assets_url') . 'components/tinymcerte/js/vendor/tinymce/tinymce.min.js');
$modx->regClientStartupScript($modx->getOption('assets_url') . 'components/tinymcerte/js/vendor/autocomplete.js');
$modx->regClientStartupScript($modx->getOption('assets_url') . 'components/tinymcerte/js/mgr/tinymcerte.js');

$language = $modx->getOption('manager_language');
$objectResizing = $modx->getOption('object_resizing', array(), '1');

if ($objectResizing === '1' || $objectResizing === 'true') {
    $objectResizing = true;
}

if ($objectResizing === '0' || $objectResizing === 'false') {
    $objectResizing = false;
}

$config = array(
    'plugins' => $modx->getOption('plugins', array(), 'advlist autolink lists link image charmap print preview anchor visualblocks searchreplace code fullscreen insertdatetime media table contextmenu paste modxlink'),
    'toolbar1' => $modx->getOption('toolbar1', array(), 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'),
    'toolbar2' => $modx->getOption('toolbar2', array(), ''),
    'toolbar3' => $modx->getOption('toolbar3', array(), ''),
    'modxlinkSearch' => $modx->getOption('jsUrl').'vendor/tinymce/plugins/modxlink/search.php',
    'language' => $language,
    'directionality' => $this->modx->getOption('manager_direction', array(), 'ltr'),
    'menubar' => $modx->getOption('menubar', array(), 'file edit insert view format table tools'),
    'statusbar' => $modx->getOption('statusbar', array(), 1) == 1,
    'image_advtab' => $modx->getOption('image_advtab', array(), true) == 1,
    'paste_as_text' => $modx->getOption('paste_as_text', array(), false) == 1,
    'style_formats_merge' => $modx->getOption('style_formats_merge', array(), false) == 1,
    'object_resizing' => $objectResizing,
    'link_class_list' => $modx->fromJSON($modx->getOption('link_class_list', array(), '[]')),
    'browser_spellcheck' => $modx->getOption('browser_spellcheck', array(), false) == 1,
    'content_css' => $modx->getOption('content_css', array(), ''),
    'image_class_list' => $modx->fromJSON($modx->getOption('image_class_list', array(), '[]')),
);

$styleFormats = $modx->getOption('style_formats', array(), '[]');
$styleFormats = $modx->fromJSON($styleFormats);

$finalFormats = array();

foreach ($styleFormats as $format) {
    if (!isset($format['items'])) continue;

    $items = $modx->getOption($format['items'], array(), '[]');
    $items = $modx->fromJSON($items);

    if (empty($items)) continue;

    $format['items'] = $items;

    $finalFormats[] = $format;
}

if (!empty($finalFormats)) {
    $config['style_formats'] = $finalFormats;
}

$externalConfig = $modx->getOption('external_config');
if (!empty($externalConfig)) {
    if (file_exists($externalConfig) && is_readable($externalConfig)) {
        $externalConfig = file_get_contents($externalConfig);
        $externalConfig = $modx->fromJSON($externalConfig);
        if (is_array($externalConfig)) {
            $config = array_merge($config, $externalConfig);
        }
    }
}

$modx->regClientStartupHTMLBlock('<script type="text/javascript">
    TinyMCERTE.editorConfig = ' . $modx->toJSON($config) . ';
</script>');

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
  'campaigner.newsletter_editautoprops' => 1,
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
