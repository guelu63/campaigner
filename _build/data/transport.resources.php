<?php
/**
 * resources transport file for campaigner extra
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
/* @var xPDOObject[] $resources */


$resources = array();

$resources[1] = $modx->newObject('modResource');
$resources[1]->fromArray(array(
    'id' => '1',
    'type' => 'document',
    'contentType' => 'text/html',
    'pagetitle' => 'Resource1',
    'longtitle' => '',
    'description' => '',
    'alias' => 'resource1',
    'link_attributes' => '',
    'published' => '',
    'isfolder' => '1',
    'introtext' => '',
    'richtext' => '1',
    'template' => 'default',
    'menuindex' => '0',
    'searchable' => '1',
    'cacheable' => '1',
    'createdby' => '1',
    'editedby' => '0',
    'deleted' => '',
    'deletedon' => '0',
    'deletedby' => '0',
    'menutitle' => '',
    'donthit' => '',
    'privateweb' => '',
    'privatemgr' => '',
    'content_dispo' => '0',
    'hidemenu' => '',
    'class_key' => 'modDocument',
    'context_key' => 'example',
    'content_type' => '1',
    'hide_children_in_tree' => '0',
    'show_in_tree' => '1',
    'properties' => '',
), '', true, true);
$resources[1]->setContent(file_get_contents($sources['data'].'resources/resource1.content.html'));

$resources[2] = $modx->newObject('modResource');
$resources[2]->fromArray(array(
    'id' => '2',
    'type' => 'document',
    'contentType' => 'text/html',
    'pagetitle' => 'Resource2',
    'longtitle' => '',
    'description' => '',
    'alias' => 'resource2',
    'link_attributes' => '',
    'published' => '1',
    'isfolder' => '',
    'introtext' => '',
    'richtext' => '',
    'template' => 'Template2',
    'menuindex' => '0',
    'searchable' => '1',
    'cacheable' => '1',
    'createdby' => '1',
    'editedby' => '0',
    'deleted' => '',
    'deletedon' => '0',
    'deletedby' => '0',
    'menutitle' => '',
    'donthit' => '',
    'privateweb' => '',
    'privatemgr' => '',
    'content_dispo' => '0',
    'hidemenu' => '',
    'class_key' => 'modDocument',
    'context_key' => 'example',
    'content_type' => '1',
    'hide_children_in_tree' => '0',
    'show_in_tree' => '1',
    'properties' => '',
), '', true, true);
$resources[2]->setContent(file_get_contents($sources['data'].'resources/resource2.content.html'));

return $resources;
