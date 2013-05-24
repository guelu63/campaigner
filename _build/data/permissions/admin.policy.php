<?php

$perms = array(
	'campaigner.newsletter_remove',
	'campaigner.newsletter_clearing',
	'campaigner.newsletter_kick',
	'campaigner.newsletter_edit',
	'campaigner.autonewsletter_approve',
	'campaigner.autonewsletter_kick',
	'campaigner.autonewsletter_editgroups',
	'campaigner.autonewsletter_editprops',
	'campaigner.autonewsletter_edit',
	'campaigner.groups_create',
	'campaigner.groups_edit',
	'campaigner.groups_remove',
	'campaigner.subscriber_create',
	'campaigner.subscriber_edit',
	'campaigner.subscriber_remove',
	'campaigner.subscriber_togglestatus',
	'campaigner.subscriber_import',
	'campaigner.subscriber_export',
	'campaigner.subscriber_showstats',
	'campaigner.bounce_fetch',
	'campaigner.bounce_hard_remove',
	'campaigner.bounce_hard_togglestatus',
	'campaigner.bounce_soft_remove',
	'campaigner.bounce_soft_togglestatus',
	'campaigner.bounce_resend_remove',
	'campaigner.queue_process',
	'campaigner.queue_cleartests',
	'campaigner.queue_remove',
	'campaigner.queue_send',
	'campaigner.statistics_showdetails',
	'campaigner.statistics_export',
	'campaigner.statistics_opens_export',
	'campaigner.statistics_clicks_export',
	'campaigner.statistics_unsubscriptions_export',
	'campaigner.sharing_create',
	'campaigner.sharing_edit',
	'campaigner.sharing_remove',
	'campaigner.sharing_togglestatus',
	'campaigner.sharing_dragsort',
	);

$permissions = array();
foreach($perms as $perm) {
	$permissions[] = $modx->newObject('modAccessPermission',array(
	    'name' => $perm,
	    'description' => 'campaigner.perm.' . str_replace('campaigner.', '', $perm),
	    'value' => true,
	));
}

// $permissions[] = $modx->newObject('modAccessPermission',array(
//     'name' => 'campaigner.remove_subscriber',
//     'description' => 'campaigner.perm.remove_subscriber',
//     'value' => true,
// ));

return $permissions;