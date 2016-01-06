<?php
/**
 * templateVars transport file for campaigner extra
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
/* @var xPDOObject[] $templateVars */


$templateVars = array();

$templateVars[] = $modx->newObject('modTemplateVar');
$templateVars[]->fromArray(array(
    // 'id' => '1',
    'property_preprocess' => '',
    'type' => '',
    'name' => 'tvAttach',
    'caption' => 'Attachments',
    'description' => '',
    'elements' => '',
    'rank' => '0',
    'display' => '',
    'default_text' => '',
    'properties' => '',
    'input_properties' => array(),
    'output_properties' => array(),
), '', true, true);

$templateVars[] = $modx->newObject('modTemplateVar');
$templateVars[]->fromArray(array(
    // 'id' => '2',
    'property_preprocess' => '',
    'type' => '',
    'name' => 'tvCampaignerData',
    'caption' => 'Campaigner Data',
    'description' => '',
    'elements' => '',
    'rank' => '0',
    'display' => '',
    'default_text' => '',
    'properties' => '',
    'input_properties' => array(),
    'output_properties' => array(),
), '', true, true);

$templateVars[] = $modx->newObject('modTemplateVar');
$templateVars[]->fromArray(array(
    // 'id' => '2',
    'property_preprocess' => '',
    'type' => '',
    'name' => 'tvCampaignerSent',
    'caption' => 'Campaigner Sent',
    'description' => '',
    'elements' => '',
    'rank' => '0',
    'display' => '',
    'default_text' => '',
    'properties' => '',
    'input_properties' => array(),
    'output_properties' => array(),
), '', true, true);

return $templateVars;
