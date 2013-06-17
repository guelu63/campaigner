<?php
/**
 * Processor to clean up newsletters
 * 
 * This processor was built to clean up the messi newsletter grid
 * # 1. Collecting parametes are:
 * * Older than 1 (or 2) month
 * ** Sent to development group
 * ** Zombie (state = 0 + send_date = null)
 * 
 * # 2. Archiving old mediainfos
 * * Searches for newsletters in a specified range (e.g.: 2011-07-01, 2012-06-30)
 * * Creates (if not already) a container resource for a season
 * * Moves the found newsletters into the season container
 * 
 * @todo Make due_date available through system setting
 * @todo Make month_ago available through system setting
 * @todo Give some feedback on found items, removed items, restored items (#MODExt)
 */
$cleaner = $modx->getOption('cleaner',$_REQUEST,null);

$trashcollector = FALSE;
$archivecollector = FALSE;
$dehtmlifier = FALSE;

if($cleaner == 1) {
	$trashcollector = TRUE;
	$archivecollector = TRUE;
	$dehtmlifier = TRUE;
}

switch($cleaner) {
	case 'trash':
		$trashcollector = TRUE;
		break;
	case 'archiver':
		$archivecollector = TRUE;
		break;
	case 'dehtml':
		$dehtmlifier = TRUE;
		break;
}

$summary = array();

if($trashcollector) {
	// echo 'TRASHCOLLECTOR';
	$c = $modx->newQuery('Newsletter');
	$c->leftJoin('modDocument', 'Document', '`Document`.`id` = `Newsletter`.`docid`');
	$c->groupby('`Newsletter`.`id`');

	$c->leftJoin('NewsletterGroup', 'NewsletterGroup', '`NewsletterGroup`.`newsletter` = `Newsletter`.`id`');
	$c->leftJoin('Group', 'Group', '`Group`.`id` = `NewsletterGroup`.`group`');

	$c->select(array(
	    'newsletter' => '`Newsletter`.*',
	    'subject' => '`Document`.`pagetitle`',
	    'date' => '`Document`.`publishedon`',
	    'groups' => 'GROUP_CONCAT(Group.id)'
	));
	$month = 60 * 60 * 24 * 31 * 2;
	$month_ago = time() - $month;

	$c->where(array(
		'`Document`.`publishedon`:<' => $month_ago,
		'sent_date:<' => $month_ago
		), xPDOQuery::SQL_OR
	);
	$c->where(
		array(
			'state' => 0,
			'`Document`.`parent`:NOT IN' => array(18553)
			)
		);
	// $c->prepare();
	// echo $c->toSQL();
	// die();
	$newsletters = $modx->getCollection('Newsletter',$c);

	// Trash Collector
	$trash = array();
	$i = 0;
	foreach($newsletters as $letter) {
		// if($i > 2) break;
		$i++;
		$groups = explode(',', $letter->get('groups'));
		// Remove all developer newsletters
		if(in_array(2, $groups)) {
			$id = $letter->get('id');
			// echo $id . '<br/>';
			$res = $modx->getObject('modResource', $letter->get('docid'));
			if($res) {
				$res->set('deleted',1);
				if($res->save())
					$trash['dev'][$id]['res'] = $res->get('id');
			}
			$nl = $modx->getObject('Newsletter', $letter->get('id'));
			// echo $nl->get('id') . ' - ' . $nl->get('docid') . '<br/>';
			// if($nl->remove())
				$trash['dev'][$id]['nl'] = $id;
			continue;
		}
		
		// Zombie newsletter
		if($letter->get('state') == 0 && $letter->get('sent_date') == NULL) {
			$id = $letter->get('id');
			// echo $id . '<br/>';
			$res = $modx->getObject('modResource', $letter->get('docid'));
			if($res) {
				$res->set('deleted',1);
				if($res->save())
					$trash['zombie'][$id]['res'] = $res->get('id');
			}
			$nl = $modx->getObject('Newsletter', $letter->get('id'));
			// echo $nl->get('id') . ' - ' . $nl->get('docid') . '<br/>';
			// if($nl->remove())
				$trash['zombie'][$id]['nl'] = $id;
			continue;
		}
	}
	$summary['trash'] = $trash;
}

/**
 * Archive-Collector
 * Collects resources based on the publish date and if the date is within a certain range
 * and stores old (prev season) resources into a container.
 */
if($archivecollector) {
	// echo 'ARCHIVECOLLECTOR';
	$s_date = '%d-06-30';
	$e_date = '%d-07-01';
	
	for($i=(date('Y'));$i>=2008;$i--) {
		$start = sprintf($s_date, $i-1);
		$end = sprintf($s_date, $i);
		
		$c = $modx->newQuery('modResource');
		$c->where(array('parent' => 18787));
		$c->where(array('publishedon:<' => strtotime($end), 'publishedon:>' => strtotime($start)));
		// $c->prepare();
		// echo $c->toSQL();
		$res = $modx->getCollection('modResource', $c);
		
		// Have we found any resources? If not, go to the next year
		if(count($res) < 1) continue;
		
		// Create a season container if we have found resources
		$cont = $modx->getObject('modResource', array('pagetitle' => ($i-1).'/'.$i, 'parent' => 18787));
		if(!$cont)
			$cont = $modx->newObject('modResource');
		$data = array(
			'pagetitle'	=> ($i-1).'/'.$i,
			'parent'	=> 18787,
			'published'	=> 1,
			'isfolder'	=> 1,
			);
		$cont->fromArray($data);
		$cont->save();
		$parent = $cont->get('id');

		foreach($res as $item) {
			$item->set('parent', $parent);
			if($item->save())
				$archive[] = $item->get('id');
			echo 'CONTAINER ' . ($i-1).$i . ' (' . $parent . '): ' . $item->get('pagetitle') . ' on ' . date('d.m.Y', strtotime($item->get('publishedon'))) . '<br/>';
		}
	}
	$summary['archive'] = $archive;
}

/**
 * Dehtmlifier
 * Parses the resource content and restores the pure content which before was a HTML generated output
 */
if($dehtmlifier) {
	// echo 'DEHTMLIFIER';
	$resources = $modx->getChildIds(18787, 10, array('isfolder' => 0, 'context' => 'web'));
	$i = 0;
	foreach($resources as $id) {
		// if($i > 50) break;
		$item = $modx->getObject('modResource', $id);
		
		// $pattern = array('/<td width="550" valign="top" style="font-family:arial,sans-serif;width: 412.5pt; padding: 11.25pt;">/', '/<\/td>/');
		// // $pattern = '</td>';
		// $replace = array('','');
		// echo preg_replace($pattern, $replace, $item->get('content'));
		// die();

		$head = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html lang="de" xmlns:v="urn:schemas-microsoft-com:vml"><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head><body>';
		$foot = '</body></html>';
		$content = mb_convert_encoding($item->get('content'), "HTML-ENTITIES", "UTF-8");
		$search = '//td[@width=\'550\']';

		$dom = new DOMDocument;
		$dom->loadHTML($content);

		$xpath = new DOMXpath($dom); 
		$res = $xpath->query("$search");

		// Nothing found - looks like the content is ok!
		if(!$res->length > 0) continue;
		
		// echo 'RESOURCE ' . $item->get('id');
		$nice = '';

	    foreach ($res as $child) {
	        $nice .= $child->ownerDocument->saveXML( $child ); 
	    }

	    /**
	     * @todo add nl2br when having plain text
	     */
		$data = array(
			'content'	=> mb_convert_encoding($nice, "HTML-ENTITIES", "UTF-8"),
			'template'	=> 22,
			);
		$item->fromArray($data);
		if($item->save())
			$dehtml[] = $item->get('id');
		$i++;
	}
	$summary['dehtml'] = $dehtml;
}
$summary = array(
	'total' => count($summary['trash']) + count($summary['archive']) + count($summary['dehtml']),
	'data' => array('1' => 'test'),
	'success' => 1,
	'message' => 'hello, here'
	);
// return array('result' => $summary);
// return json_encode(array('result' => $summary));