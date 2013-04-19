<?php
/**
 * systemSettings transport file for campaigner extra
 *
 * Copyright 2013 by Subsolutions <http://www.subsolutions.at>
 * Created on 04-19-2013
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
    'key' => 'campaigner.mail_charset',
    'name' => 'Campaigner Mail Charset',
    'description' => 'Campaigner Mail Charset',
    'namespace' => 'campaigner',
    'xtype' => 'textfield',
    'value' => 'UTF-8',
    'area' => 'email',
), '', true, true);
$systemSettings[2] = $modx->newObject('modSystemSetting');
$systemSettings[2]->fromArray(array(
    'key' => 'campaigner.mail_encoding',
    'name' => 'Campaigner Mail Encoding',
    'description' => 'Campaigner Mail Encoding',
    'namespace' => 'campaigner',
    'xtype' => 'textfield',
    'value' => '8bit',
    'area' => 'email',
), '', true, true);
$systemSettings[3] = $modx->newObject('modSystemSetting');
$systemSettings[3]->fromArray(array(
    'key' => 'campaigner.mail_smtp_auth',
    'name' => 'Campaigner SMTP Authentication',
    'description' => 'Campaigner SMTP Authentication',
    'namespace' => 'campaigner',
    'xtype' => 'combo-boolean',
    'value' => false,
    'area' => 'system',
), '', true, true);
$systemSettings[4] = $modx->newObject('modSystemSetting');
$systemSettings[4]->fromArray(array(
    'key' => 'campaigner.mail_smtp_helo',
    'name' => 'Campaigner SMTP HELO',
    'description' => 'Campaigner SMTP HELO',
    'namespace' => 'campaigner',
    'xtype' => 'textfield',
    'value' => '',
    'area' => 'system',
), '', true, true);
$systemSettings[5] = $modx->newObject('modSystemSetting');
$systemSettings[5]->fromArray(array(
    'key' => 'campaigner.mail_smtp_hosts',
    'name' => 'Campaigner SMTP Host',
    'description' => 'Campaigner SMTP Host',
    'namespace' => 'campaigner',
    'xtype' => 'textfield',
    'value' => 'localhost',
    'area' => 'system',
), '', true, true);
$systemSettings[6] = $modx->newObject('modSystemSetting');
$systemSettings[6]->fromArray(array(
    'key' => 'campaigner.mail_smtp_keepalive',
    'name' => 'Campaigner SMTP Keep-Alive',
    'description' => 'Campaigner SMTP Keep-Alive',
    'namespace' => 'campaigner',
    'xtype' => 'combo-boolean',
    'value' => true,
    'area' => 'system',
), '', true, true);
$systemSettings[7] = $modx->newObject('modSystemSetting');
$systemSettings[7]->fromArray(array(
    'key' => 'campaigner.mail_smtp_pass',
    'name' => 'Campaigner SMTP Password',
    'description' => 'Campaigner SMTP Password',
    'namespace' => 'campaigner',
    'xtype' => 'text-password',
    'inputType' => 'password',
    'value' => '',
    'area' => 'system',
), '', true, true);
$systemSettings[8] = $modx->newObject('modSystemSetting');
$systemSettings[8]->fromArray(array(
    'key' => 'campaigner.mail_smtp_port',
    'name' => 'Campaigner SMTP Port',
    'description' => 'Campaigner SMTP Port',
    'namespace' => 'campaigner',
    'xtype' => 'textfield',
    'value' => '25',
    'area' => 'system',
), '', true, true);
$systemSettings[9] = $modx->newObject('modSystemSetting');
$systemSettings[9]->fromArray(array(
    'key' => 'campaigner.mail_smtp_prefix',
    'name' => 'Campaigner SMTP Prefix',
    'description' => 'Campaigner SMTP Prefix',
    'namespace' => 'campaigner',
    'xtype' => 'textfield',
    'value' => '',
    'area' => 'system',
), '', true, true);
$systemSettings[10] = $modx->newObject('modSystemSetting');
$systemSettings[10]->fromArray(array(
    'key' => 'campaigner.mail_smtp_timeout',
    'name' => 'Campaigner SMTP Timeout',
    'description' => 'Campaigner SMTP Timeout',
    'namespace' => 'campaigner',
    'xtype' => 'textfield',
    'value' => '10',
    'area' => 'system',
), '', true, true);
$systemSettings[11] = $modx->newObject('modSystemSetting');
$systemSettings[11]->fromArray(array(
    'key' => 'campaigner.mail_smtp_user',
    'name' => 'Campaigner SMTP User',
    'description' => 'Campaigner SMTP User',
    'namespace' => 'campaigner',
    'xtype' => 'textfield',
    'value' => '',
    'area' => 'system',
), '', true, true);
$systemSettings[12] = $modx->newObject('modSystemSetting');
$systemSettings[12]->fromArray(array(
    'key' => 'campaigner.batchsize',
    'name' => 'Campaigner Batchsize',
    'description' => 'Campaigner Batchsize',
    'namespace' => 'campaigner',
    'xtype' => 'textfield',
    'value' => '100',
    'area' => 'system',
), '', true, true);
$systemSettings[13] = $modx->newObject('modSystemSetting');
$systemSettings[13]->fromArray(array(
    'key' => 'campaigner.default_groups',
    'name' => 'Campaigner Group (Default)',
    'description' => 'Campaigner Group (Default)',
    'namespace' => 'campaigner',
    'xtype' => 'textfield',
    'value' => '3',
    'area' => 'system',
), '', true, true);
$systemSettings[14] = $modx->newObject('modSystemSetting');
$systemSettings[14]->fromArray(array(
    'key' => 'campaigner.default_from',
    'name' => 'Campaigner From (Default)',
    'description' => 'Campaigner From (Default)',
    'namespace' => 'campaigner',
    'xtype' => 'textfield',
    'value' => 'you@domain.com',
    'area' => 'system',
), '', true, true);
$systemSettings[15] = $modx->newObject('modSystemSetting');
$systemSettings[15]->fromArray(array(
    'key' => 'campaigner.default_name',
    'name' => 'Campaigner Name (Default)',
    'description' => 'Campaigner Name (Default)',
    'namespace' => 'campaigner',
    'xtype' => 'textfield',
    'value' => 'You',
    'area' => 'system',
), '', true, true);
$systemSettings[16] = $modx->newObject('modSystemSetting');
$systemSettings[16]->fromArray(array(
    'key' => 'campaigner.return_path',
    'name' => 'Campaigner Reply-To',
    'description' => 'Campaigner Reply-To',
    'namespace' => 'campaigner',
    'xtype' => 'textfield',
    'value' => 'rt@domain.com',
    'area' => 'system',
), '', true, true);
$systemSettings[17] = $modx->newObject('modSystemSetting');
$systemSettings[17]->fromArray(array(
    'key' => 'campaigner.autonewsletter_folder',
    'name' => 'Campaigner Autonewsletter Folder',
    'description' => 'Campaigner Autonewsletter Folder',
    'namespace' => 'campaigner',
    'xtype' => 'textfield',
    'value' => '',
    'area' => 'site',
), '', true, true);
$systemSettings[18] = $modx->newObject('modSystemSetting');
$systemSettings[18]->fromArray(array(
    'key' => 'campaigner.confirm_mail',
    'name' => 'Campaigner Confirm Mail Resource',
    'description' => 'Campaigner Confirm Mail Resource',
    'namespace' => 'campaigner',
    'xtype' => 'textfield',
    'value' => '',
    'area' => 'site',
), '', true, true);
$systemSettings[19] = $modx->newObject('modSystemSetting');
$systemSettings[19]->fromArray(array(
    'key' => 'campaigner.newsletter_folder',
    'name' => 'Campaigner Newsletter Folder',
    'description' => 'Campaigner Autonewsletter Folder',
    'namespace' => 'campaigner',
    'xtype' => 'textfield',
    'value' => '',
    'area' => 'site',
), '', true, true);
$systemSettings[20] = $modx->newObject('modSystemSetting');
$systemSettings[20]->fromArray(array(
    'key' => 'campaigner.newsletter_subfolders',
    'name' => 'Campaigner Newsletter Sub-Folder',
    'description' => 'Campaigner Newsletter Sub-Folder',
    'namespace' => 'campaigner',
    'xtype' => 'combo-boolean',
    'value' => true,
    'area' => 'site',
), '', true, true);
$systemSettings[21] = $modx->newObject('modSystemSetting');
$systemSettings[21]->fromArray(array(
    'key' => 'campaigner.unsubscribe_page',
    'name' => 'Campaigner Unsubscribe Page',
    'description' => 'Campaigner Unsubscribe Page',
    'namespace' => 'campaigner',
    'xtype' => 'textfield',
    'value' => '',
    'area' => 'site',
), '', true, true);
return $systemSettings;
