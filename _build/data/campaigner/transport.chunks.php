<?php
/**
 * chunks transport file for campaigner extra
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
/* @var xPDOObject[] $chunks */


$chunks = array();

$chunks[1] = $modx->newObject('modChunk');
$chunks[1]->fromArray(array(
    'id' => '1',
    'property_preprocess' => '',
    'name' => 'CampaignerMessage',
    'description' => 'Chunk',
    'properties' => '',
), '', true, true);
$chunks[1]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/campaignermessage.chunk.html'));

$chunks[2] = $modx->newObject('modChunk');
$chunks[2]->fromArray(array(
    'id' => '2',
    'property_preprocess' => '',
    'name' => 'CampaignerForm',
    'description' => 'Chunk',
    'properties' => '',
), '', true, true);
$chunks[2]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/campaignerform.chunk.html'));

$chunks[3] = $modx->newObject('modChunk');
$chunks[3]->fromArray(array(
    'id' => '3',
    'property_preprocess' => '',
    'name' => 'CampaignerFormSimple',
    'description' => 'Chunk',
    'properties' => '',
), '', true, true);
$chunks[3]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/campaignerformsimple.chunk.html'));

$chunks[4] = $modx->newObject('modChunk');
$chunks[4]->fromArray(array(
    'id' => '4',
    'property_preprocess' => '',
    'name' => 'CampaignerCheckbox',
    'description' => 'Chunk',
    'properties' => '',
), '', true, true);
$chunks[4]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/campaignercheckbox.chunk.html'));

$chunks[5] = $modx->newObject('modChunk');
$chunks[5]->fromArray(array(
    'id' => '5',
    'property_preprocess' => '',
    'name' => 'CampaignerOption',
    'description' => 'Chunk',
    'properties' => '',
), '', true, true);
$chunks[5]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/campaigneroption.chunk.html'));

$chunks[6] = $modx->newObject('modChunk');
$chunks[6]->fromArray(array(
    'id' => '6',
    'property_preprocess' => '',
    'name' => 'CampaignerFormUnsubscribe',
    'description' => 'Chunk',
    'properties' => '',
), '', true, true);
$chunks[6]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/campaignerformunsubscribe.chunk.html'));

return $chunks;
