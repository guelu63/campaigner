<?php
/**
 * Processor: Get articles of the specified newsletter
 *
 * Processor to parse and analyze MIGx values and recreate them
 * in a ExtJS grid view
 * 
 * @todo Move (drag and drop) functionality
 * @todo Remove element
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
		preg_match('/.*\(?:([^\)]+)\)?$/', $article['resource'], $match);
		$sections[$match[1]] = $section['section'];
		$ids[] = $match[1];
		// $sections[$article['_this.value']] = $section['section'];
		// $ids[] = $article['_this.value'];
	}
}

$c = $modx->newQuery('modResource');
$c->limit($limit,$start);
$c->where(array('id:IN' => $ids));

// Get the custom sort order
$sql .= "CASE id\n";
foreach($ids as $k => $v){
    $sql .= 'WHEN ' . $v . ' THEN ' . $k . "\n";
}
$sql .= 'END ';
$c->sortby($sql);

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