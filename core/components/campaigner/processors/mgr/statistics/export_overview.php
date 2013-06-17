<?php
/**
 * Export statistical overview data
 */
if(!$_GET['export'] && !$_GET['export'] == 1)
    return $modx->error->success('');

$_overview[] = 'Campaigner Report';
$_overview[] = 'Overall Stats';
$delimiter = ',';
$nl = PHP_EOL;
// $nl = '<br/>';

// Let's get some overview information
$c = $modx->newQuery('Newsletter');
$c->where(array('Newsletter.id' => $_GET['statistics_id']));

$c->leftJoin('modDocument', 'Document', '`Document`.`id` = `Newsletter`.`docid`');
$c->leftJoin('SubscriberHits', 'SubscriberHits', '`Newsletter`.`id` = `SubscriberHits`.`newsletter`');
$c->leftJoin('Unsubscriber', 'Unsubscriber', '`Newsletter`.`id` = `Unsubscriber`.`newsletter`');
$c->select(array(
    'newsletter'	=> $modx->getSelectColumns('Newsletter', 'Newsletter', '', array('id', 'docid', 'state', 'sent_date', 'total', 'sent', 'bounced')),
    'pagetitle'		=> 'Document.pagetitle',
    'hits'			=> 'SUM(CASE WHEN SubscriberHits.hit_type != \'image\' THEN view_total ELSE 0 END)',
    'facebook'      => 'SUM(CASE WHEN SubscriberHits.hit_type != \'facebook\' THEN 1 ELSE 0 END)',
    'opened'		=> 'SUM(CASE WHEN SubscriberHits.hit_type = \'image\' THEN view_total ELSE 0 END)',
    'subscriber'	=> 'COUNT(DISTINCT(SubscriberHits.subscriber))',
    'unsubscriber'  => 'COUNT(DISTINCT(Unsubscriber.subscriber))',
    )
);
$c->groupby('SubscriberHits.newsletter');
$c->prepare();
// echo $c->toSQL() . '<br/>';
$item = $modx->getObject('Newsletter',$c);

if(!$item)
	return $modx->error->failure('Sorry, no item found');

// Export specific statistic data
$overview_fields = array('pagetitle', 'sent_date', 'total', 'sent', 'hits', 'facebook', 'opened', 'subscriber', 'unsubscriber');
$item = $item->toArray();
foreach($item as $key => $value) {
	// $row_values = array();
	if($key === 'sent_date')
		$value = strftime('%Y-%m-%d %H:%M', $value);
    if(in_array($key, $overview_fields))
    	$row_values[array_search($key,$overview_fields)] = $key . $delimiter . $value;
}
// Sort by index
ksort($row_values, SORT_NUMERIC);

// Get clicks of specified newsletter
$c = $modx->newQuery('SubscriberHits');
$c->leftJoin('NewsletterLink', 'NewsletterLink', 'NewsletterLink.id = SubscriberHits.link');
$c->where(array('newsletter' => $_GET['statistics_id']));
$c->select(array(
	'id'		=> $modx->getSelectColumns('SubscriberHits', 'SubscriberHits', '', array('id')),
	'link'		=> $modx->getSelectColumns('NewsletterLink', 'NewsletterLink', '', array('url')),
	'hits'		=> 'SUM(CASE WHEN SubscriberHits.hit_type != \'image\' THEN view_total ELSE 0 END)',
	'unique_hits'	=> 'SUM(CASE WHEN SubscriberHits.hit_type != \'image\' THEN 1 ELSE 0 END)',
	));
// Group by link aka url
$c->groupBy('SubscriberHits.link');
$clicks = $modx->getCollection('SubscriberHits', $c);

// Which fields to include in output
$click_fields = array('link', 'hits', 'unique_hits');
$list = array();
$_clicks[] = 'Clicks by URL';
$_clicks[] = implode($delimiter, $click_fields);
foreach ($clicks as $click) {
    $click = $click->toArray();
    foreach($click as $key => $value) {
    	if(in_array($key, $click_fields))
    		$_row[array_search($key,$click_fields)] = $value;
    }
    // Sort by index
    ksort($_row, SORT_NUMERIC);
    $_clicks[] = implode($delimiter, $_row);
}

// Put it together
$output = implode($nl, $_overview) . $nl . implode($nl, $row_values) . $nl . $nl . implode($nl, $_clicks);

// Output the CSV
// File name: campaigner-title-date-report-data.csv
header('Content-Disposition: attachment; filename="campaigner-' . $item['pagetitle'] . '-' . strftime('%Y-%m-%d') . '-report-data.csv"'); 
return $output;