<?php
/**
 * Plugin: CampaignerAutofill
 *
 * Recreates the contents of MIGx TV for a newsletter
 * This plugin is also used as a snippet to run when a new auto-newsletter is created
 *
 * @param object $resource MODx resource
 */

if($scriptProperties['runSnippet'])
    $resource = $scriptProperties['resource'];

$modx->log(MODX_LOG_LEVEL_ERROR, 'SNIPPET RUN: ' . $scriptProperties['runSnippet']);

//check if it is a autonewsletter
$template = $resource->get('template');
if($template != 2)
	return;

//check whether to update or not
$updateTV = $resource->getTVValue('tvAutogenerate');
if($updateTV != 1)
	return;

//Top News
$out = '[';
$options = array(
	'parents' => 46,
	'context' => 'web',
	'limit' => 3,
	'tpl' => '@INLINE {\"MIGX_id\":\"[[+idx]]\",\"resource\":\"[[+pagetitle]]:[[+id]]\"}',
	'includeContent' => 1,
    'includeTVs' => 1,
    'showHidden' => 1,
    'where' => '{"template:=":11, "OR:template:=":34}',
    'sortby' => 'publishedon',
    'sortdir' => 'DESC',
    'hideContainers' => 1,
    'outputSeparator' => ','
);
$topNews = $modx->runsnippet('getResources', $options);
$out .= '{"MIGX_id":"1","section":"Top News","resource_ids":"['.$topNews.']"}';


//Top Thema 
$options = array(
	'parents' => 3,
	'context' => 'web',
	'limit' => 3,
	'tpl' => '@INLINE {\"MIGX_id\":\"[[+idx]]\",\"resource\":\"[[+pagetitle]]:[[+id]]\"}',
	'includeContent' => 1,
    'includeTVs' => 1,
    'showHidden' => 1,
    'where' => '{"template:=":11, "OR:template:=":34}',
    'sortby' => 'publishedon',
    'sortdir' => 'DESC',
    'hideContainers' => 1,
    'outputSeparator' => ','
);
$topThema = $modx->runsnippet('getResources', $options);
$out .= ',{"MIGX_id":"2","section":"Top Thema","resource_ids":"['.$topThema.']"}';

// Prozess des Monats
$options = array(
	'parents' => 76,
	'context' => 'web',
	'limit' => 3,
	'tpl' => '@INLINE {\"MIGX_id\":\"[[+idx]]\",\"resource\":\"[[+pagetitle]]:[[+id]]\"}',
	'includeContent' => 1,
    'includeTVs' => 1,
    'showHidden' => 1,
    'where' => '{"template:=":11, "OR:template:=":34}',
    'sortby' => 'publishedon',
    'sortdir' => 'DESC',
    'hideContainers' => 1,
    'outputSeparator' => ','
);
$prozess = $modx->runsnippet('getResources', $options);
$out .= ',{"MIGX_id":"3","section":"Prozess des Monats","resource_ids":"['.$prozess.']"}';

// Publikation des Monats
$options = array(
	'parents' => 113,
	'context' => 'web',
	'limit' => 3,
	'tpl' => '@INLINE {\"MIGX_id\":\"[[+idx]]\",\"resource\":\"[[+pagetitle]]:[[+id]]\"}',
	'includeContent' => 1,
    'includeTVs' => 1,
    'showHidden' => 1,
    'where' => '{"template:=":30}',
    'sortby' => 'publishedon',
    'sortdir' => 'DESC',
    'hideContainers' => 1,
    'outputSeparator' => ','
);
$publikation = $modx->runsnippet('getResources', $options);
$out .= ',{"MIGX_id":"4","section":"Publikation des Monats","resource_ids":"['.$publikation.']"}';


// Webbtipp des Monats
$options = array(
	'parents' => 112,
	'context' => 'web',
	'limit' => 3,
	'tpl' => '@INLINE {\"MIGX_id\":\"[[+idx]]\",\"resource\":\"[[+pagetitle]]:[[+id]]\"}',
	'includeContent' => 1,
    'includeTVs' => 1,
    'showHidden' => 1,
    'where' => '{"template:=":11, "OR:template:=":34}',
    'sortby' => 'publishedon',
    'sortdir' => 'DESC',
    'hideContainers' => 1,
    'outputSeparator' => ','
);
$publikation = $modx->runsnippet('getResources', $options);
$out .= ',{"MIGX_id":"5","section":"Webtipp des Monats","resource_ids":"['.$publikation.']"}';


// Top Events des Monats
$options = array(
	'parents' => 187,
	'context' => 'web',
	'limit' => 2,
	'tpl' => '@INLINE {\"MIGX_id\":\"[[+idx]]\",\"resource\":\"[[+pagetitle]]:[[+id]]\"}',
	'includeContent' => 1,
    'includeTVs' => 1,
    'showHidden' => 1,
    'where' => '{"template:=":14}',
    'sortbyTV' => 'myevents_start',
    'sortdirTV' => 'ASC',
    'sortbyTVType' => 'datetime',
    'hideContainers' => 1,
    'outputSeparator' => ','
);
$events = $modx->runsnippet('getResources', $options);
$out .= ',{"MIGX_id":"6","section":"Top Events des Monats","resource_ids":"['.$events.']"}';


$out .= ']';

//save MigxTV
$resource->setTVValue('nested', $out);
//set autogenerate to 0
$resource->setTVValue('tvAutogenerate', 0);
//save
$resource->save();

return;