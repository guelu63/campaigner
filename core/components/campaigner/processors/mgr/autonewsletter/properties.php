<?php
// var_dump($_POST);
// return $modx->error->success('',$newsletter);

// validate properties
if (!isset($_POST['state']))
	$modx->error->addField('state',$modx->lexicon('campaigner.newsletter.error.nostate'));

if (empty($_POST['id']))
	$modx->error->addField('sender',$modx->lexicon('campaigner.newsletter.error.notfound'));

if ($modx->error->hasError())
    return $modx->error->failure();

// get the object
$_POST['last']  = $_POST['last'] ? strtotime($_POST['last']) : null;
$_POST['start'] = $_POST['start'] ? strtotime($_POST['start']) : null;
$_POST['frequency'] = $_POST['frequency'] * $_POST['interval'] * 24*60*60;

$newsletter = $modx->getObject('Autonewsletter', array('id' => $_POST['id']));
if(!$newsletter)
	return $modx->error->failure($modx->lexicon('campaigner.newsletter.error.notfound'));

$newsletter->fromArray($_POST);

// save it
if (!$newsletter->save())
    return $modx->error->failure($modx->lexicon('campaigner.err_save'));

return $modx->error->success('',$newsletter);