<?php
/**
 * snippets transport file for campaigner extra
 *
 * Copyright 2013 by Subsolutions <http://www.subsolutions.at>
 * Created on 04-18-2013
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
/* @var xPDOObject[] $snippets */


$snippets = array();

$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => '1',
    'property_preprocess' => '',
    'name' => 'CampaignerConfirm',
    'description' => 'Confirms a subscription',
    'properties' => '',
), '', true, true);
$snippets[1]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/campaignerconfirm.snippet.php'));

$snippets[2] = $modx->newObject('modSnippet');
$snippets[2]->fromArray(array(
    'id' => '2',
    'property_preprocess' => '',
    'name' => 'CampaignerSubscribe',
    'description' => 'Subscribe snippet',
    'properties' => '',
), '', true, true);
$snippets[2]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/campaignersubscribe.snippet.php'));

$snippets[3] = $modx->newObject('modSnippet');
$snippets[3]->fromArray(array(
    'id' => '3',
    'property_preprocess' => '',
    'name' => 'CampaignerUnsubscribe',
    'description' => 'Unsubscribe snippet',
    'properties' => '',
), '', true, true);
$snippets[3]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/campaignerunsubscribe.snippet.php'));

return $snippets;
