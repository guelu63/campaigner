<?php
// validate properties
if (empty($_POST['email'])) $modx->error->addField('email',$modx->lexicon('campaigner.subscriber.error.noemail'));

if(!preg_match("/^(.+)@([^@]+)$/", $_POST['email'])) {
    $modx->error->addField('email',$modx->lexicon('campaigner.subscriber.error.noemail'));
}

if ($modx->error->hasError()) {
    return $modx->error->failure();
}

$groups = $_POST['groups']; unset($_POST['groups']);

// get the object
if(!empty($_POST['id'])) {
    $subscriber = $modx->getObject('Subscriber', array('id' => $_POST['id']));
    if(!$subscriber) return $modx->error->failure($modx->lexicon('campaigner.$subscriber.error.notfound'));
} else {
    $subscriber = $modx->newObject('Subscriber');
    $new = true;
}
$_POST['active'] = $_POST['active'] ? 1 : 0;
$_POST['text']   = $_POST['text'] ? 1 : 0;
$_POST['key'] = md5(time() . substr($_SERVER['REQUEST_URI'], rand(1, 20)) . $_SERVER['REMOTE_ADDR']);

$subscriber->fromArray($_POST);

// save it
if ($subscriber->save() == false) {
    return $modx->error->failure($modx->lexicon('campaigner.err_save'));
}

$subfield = $modx->newObject('SubscriberFields');
$subfield->set('field', 2);
$subfield->set('subscriber', $subscriber->get('id'));
$subfield->set('value', $_POST['custom']['test']);
$subfields[] = $subfield;
// $subfield->save();
$subscriber->addMany($subfields, 'SubscriberFields');

// lets get to the subsciber groups
if($new) {
    foreach($groups as $groupid) {
	$group = $modx->newObject('GroupSubscriber');
	$group->fromArray(array('group' => $groupid, 'subscriber' => $subscriber->get('id')));
	$group->save();
    }
} else {
    $subs = $modx->getCollection('GroupSubscriber', array('subscriber' => $subscriber->get('id')));
    $done = array();
    foreach($subs as $sub) {
	if(!in_array($sub->group, $groups)) {
	    $delete = $modx->getObject('GroupSubscriber', array('group' => $sub->group, 'subscriber' => $subscriber->get('id')));
	    $delete->remove();
	} else {
	    $done[] = $sub->group;
	}
    }
    
    foreach($groups as $groupid) {
	if(!in_array($groupid, $done)) {
	    $group = $modx->newObject('GroupSubscriber');
	    $group->fromArray(array('group' => $groupid, 'subscriber' => $subscriber->get('id')));
	    $group->save();
	}
    }
}
return $modx->error->success('',$subscriber);