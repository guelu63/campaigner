<?php
/**
 * Processor: Save a segment and assign subscribers
 */

$group_params = array_intersect_key($scriptProperties, array_flip(array('id', 'name', 'color', 'public', 'priority')));
$subscriber_params = array_intersect_key($scriptProperties, array_flip(array('subscribers')));

// Validate properties
if (empty($group_params['name']))
	$modx->error->addField('name',$modx->lexicon('campaigner.groups.error.noname'));
if (empty($group_params['color']))
	$modx->error->addField('color',$modx->lexicon('campaigner.groups.error.nocolor'));

if(!preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $group_params['color']))
	$modx->error->addField('color',$modx->lexicon('campaigner.groups.error.invalidcolor'));

if ($modx->error->hasError())
    return $modx->error->failure();

// Some default values
$group_params['public'] = empty($group_params['public']) ? 0 : 1;
$group_params['subscribers'] = 0;

// Get the object
if(!empty($group_params['id'])) {
    $group = $modx->getObject('Group', array('id' => $group_params['id']));
    if(!$group)
    	return $modx->error->failure($modx->lexicon('campaigner.group.notfound'));
} else {
    $group = $modx->newObject('Group');
}
$group->fromArray($group_params);

// Save it
if ($group->save() == false)
    return $modx->error->failure($modx->lexicon('campaigner.err_save'));


// Assign the subscribers to the segment
if(empty($subscriber_params['subscribers']))
	return $modx->error->failure();

$subscribers = explode(',', $subscriber_params['subscribers']);
if(count($subscribers) <= 0)
	return $modx->error->success();

$grp_subs = $modx->getCollection('GroupSubscriber', array('group' => $group->get('id')));
foreach($grp_subs as $grp_sub) {
	$existing[] = $grp_sub->get('subscriber');
}
if(is_array($existing))
	$modx->removeCollection('GroupSubscriber', array('subscriber:IN' => array_diff($existing, $subscribers)));

$counter = 0;
foreach($subscribers as $subscriber) {
	$sub = $modx->getObject('GroupSubscriber', array('subscriber' => $subscriber, 'group' => $group->get('id')));
	if($sub)
		continue;
	$sub = $modx->newObject('GroupSubscriber');
	$data = array(
		'subscriber'	=> $subscriber,
		'group'			=> $group->get('id')
		);
	$sub->fromArray($data);
	if($sub->save())
		$counter++;
}

$group->set('subscribers', $counter);
$group->save();

return $modx->error->success();