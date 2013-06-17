<?php
/**
 * Get articles of the specified newsletter
 * @todo Enhance!!!
 */

$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,10);

// Get the TV raw value and convert it to a php usable array
$resource = $modx->getObject('modResource', $_REQUEST['docid']);
$tv = json_decode($resource->getTVValue($modx->getOption('campaigner.articles_tv')), true);

foreach($tv as $section) {
	$pattern = array('"[{', '}]"');
	$js_de = json_decode(str_replace($pattern, '', $section['resource_ids']), true);
	// var_dump($js_de);
	foreach($js_de as $article) {
		$sections[$article['resource']] = $section['section'];
		$ids[] = $article['resource'];
	}
}

$c = $modx->newQuery('modResource');
$c->limit($limit,$start);
$c->where(array('id:IN' => $ids));
$c->sortby('FIELD(modResource.id, '.implode(',',$ids).' )', 'DESC');
$resources = $modx->getCollection('modResource', $c);

foreach($resources as $resource) {
	$resource = $resource->toArray();
	$resource['section'] = $sections[$resource['id']];
	// $resource['section'] = 'Artikel';
	$list[] = $resource;
}
// $list[] = array('id' => 2, 'pagetitle' => 'test', 'section' => 'Hula');
$count = count($list);
return $this->outputArray($list,$count);