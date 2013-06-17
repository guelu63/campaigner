<?php
/**
 * systemSettings transport file for campaigner extra
 *
 * Copyright 2013 by Subsolutions <http://www.subsolutions.at>
 * Created on 06-17-2013
 *
 * @package campaigner
 * @subpackage build
 */

if (! function_exists('stripPhpTags')) {
    function stripPhpTags($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<' . '?' . 'php', '', $o);
        $o = str_replace('?>', '', $o);
        $o = trim($o);
        return $o;
    }
}
/* @var $modx modX */
/* @var $sources array */
/* @var xPDOObject[] $systemSettings */


$systemSettings = array();

$systemSettings[1] = $modx->newObject('modSystemSetting');
$systemSettings[1]->fromArray(array(
    'key' => 'campaigner.mail_smtp_hosts',
    'value' => 'localhost',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[2] = $modx->newObject('modSystemSetting');
$systemSettings[2]->fromArray(array(
    'key' => 'campaigner.mail_smtp_pass',
    'value' => '',
    'xtype' => 'text-password',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[3] = $modx->newObject('modSystemSetting');
$systemSettings[3]->fromArray(array(
    'key' => 'campaigner.mail_smtp_port',
    'value' => '25',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[4] = $modx->newObject('modSystemSetting');
$systemSettings[4]->fromArray(array(
    'key' => 'campaigner.mail_smtp_prefix',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[5] = $modx->newObject('modSystemSetting');
$systemSettings[5]->fromArray(array(
    'key' => 'campaigner.mail_smtp_keepalive',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[6] = $modx->newObject('modSystemSetting');
$systemSettings[6]->fromArray(array(
    'key' => 'campaigner.default_name',
    'value' => 'You',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[7] = $modx->newObject('modSystemSetting');
$systemSettings[7]->fromArray(array(
    'key' => 'campaigner.return_path',
    'value' => 'rt@domain.com',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[8] = $modx->newObject('modSystemSetting');
$systemSettings[8]->fromArray(array(
    'key' => 'campaigner.autonewsletter_folder',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'site',
), '', true, true);
$systemSettings[9] = $modx->newObject('modSystemSetting');
$systemSettings[9]->fromArray(array(
    'key' => 'campaigner.confirm_mail',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'site',
), '', true, true);
$systemSettings[10] = $modx->newObject('modSystemSetting');
$systemSettings[10]->fromArray(array(
    'key' => 'campaigner.newsletter_folder',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'site',
), '', true, true);
$systemSettings[11] = $modx->newObject('modSystemSetting');
$systemSettings[11]->fromArray(array(
    'key' => 'campaigner.newsletter_subfolders',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'campaigner',
    'area' => 'site',
), '', true, true);
$systemSettings[12] = $modx->newObject('modSystemSetting');
$systemSettings[12]->fromArray(array(
    'key' => 'campaigner.unsubscribe_page',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'site',
), '', true, true);
$systemSettings[13] = $modx->newObject('modSystemSetting');
$systemSettings[13]->fromArray(array(
    'key' => 'campaigner.default_from',
    'value' => 'you@domain.com',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[14] = $modx->newObject('modSystemSetting');
$systemSettings[14]->fromArray(array(
    'key' => 'campaigner.mail_smtp_timeout',
    'value' => '10',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[15] = $modx->newObject('modSystemSetting');
$systemSettings[15]->fromArray(array(
    'key' => 'campaigner.mail_smtp_user',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[16] = $modx->newObject('modSystemSetting');
$systemSettings[16]->fromArray(array(
    'key' => 'campaigner.batchsize',
    'value' => '100',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[17] = $modx->newObject('modSystemSetting');
$systemSettings[17]->fromArray(array(
    'key' => 'campaigner.default_groups',
    'value' => '3',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[18] = $modx->newObject('modSystemSetting');
$systemSettings[18]->fromArray(array(
    'key' => 'campaigner.mail_smtp_helo',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[19] = $modx->newObject('modSystemSetting');
$systemSettings[19]->fromArray(array(
    'key' => 'campaigner.mail_smtp_auth',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[20] = $modx->newObject('modSystemSetting');
$systemSettings[20]->fromArray(array(
    'key' => 'campaigner.mail_encoding',
    'value' => '8bit',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'email',
), '', true, true);
$systemSettings[21] = $modx->newObject('modSystemSetting');
$systemSettings[21]->fromArray(array(
    'key' => 'campaigner.mail_charset',
    'value' => 'UTF-8',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'email',
), '', true, true);
$systemSettings[22] = $modx->newObject('modSystemSetting');
$systemSettings[22]->fromArray(array(
    'key' => 'campaigner.tracking_enabled',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[23] = $modx->newObject('modSystemSetting');
$systemSettings[23]->fromArray(array(
    'key' => 'campaigner.tracking_page',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[24] = $modx->newObject('modSystemSetting');
$systemSettings[24]->fromArray(array(
    'key' => 'campaigner.unsubscribe_reasons',
    'value' => 'No interest==interest||Too much content==content_big||Content boring==content_boring',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[25] = $modx->newObject('modSystemSetting');
$systemSettings[25]->fromArray(array(
    'key' => 'campaigner.tac',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'campaigner',
    'area' => 'mailing',
), '', true, true);
$systemSettings[26] = $modx->newObject('modSystemSetting');
$systemSettings[26]->fromArray(array(
    'key' => 'campaigner.test_mail',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[27] = $modx->newObject('modSystemSetting');
$systemSettings[27]->fromArray(array(
    'key' => 'campgaigner.has_autonewsletter',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'campaigner',
    'area' => 'system',
), '', true, true);
$systemSettings[28] = $modx->newObject('modSystemSetting');
$systemSettings[28]->fromArray(array(
    'key' => 'campaigner.confirm_page',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'site',
), '', true, true);
$systemSettings[29] = $modx->newObject('modSystemSetting');
$systemSettings[29]->fromArray(array(
    'key' => 'campaigner.attachment_tv',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'file',
), '', true, true);
$systemSettings[30] = $modx->newObject('modSystemSetting');
$systemSettings[30]->fromArray(array(
    'key' => 'campaigner.salutation',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'campaigner',
    'area' => 'language',
), '', true, true);
return $systemSettings;
