<?php
/**
 * plugins transport file for campaigner extra
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
/* @var xPDOObject[] $plugins */


$plugins = array();

$plugins[1] = $modx->newObject('modPlugin');
$plugins[1]->fromArray(array(
    'id' => '1',
    'property_preprocess' => '',
    'name' => 'CampaignerTracking',
    'description' => 'Tracks clicks & opens of subscribers',
    'properties' => '',
    'disabled' => '',
), '', true, true);
$plugins[1]->setContent(file_get_contents($sources['source_core'] . '/elements/plugins/campaignertracking.plugin.php'));

$plugins[2] = $modx->newObject('modPlugin');
$plugins[2]->fromArray(array(
    'id' => '2',
    'property_preprocess' => '',
    'name' => 'CampaignerResource',
    'description' => 'Creates campaignes when events are hit',
    'properties' => '',
    'disabled' => '',
), '', true, true);
$plugins[2]->setContent(file_get_contents($sources['source_core'] . '/elements/plugins/campaignerresource.plugin.php'));

return $plugins;
