<?php

// validate properties
if (!isset($_POST['state'])) $modx->error->addField('state',$modx->lexicon('campaigner.newsletter.error.nostate'));
if (empty($_POST['id'])) $modx->error->addField('sender',$modx->lexicon('campaigner.newsletter.error.notfound'));

if ($modx->error->hasError()) {
    return $modx->error->failure();
}

// get the object
$newsletter = $modx->getObject('Newsletter', array('id' => $_POST['id']));
if(!$newsletter) return $modx->error->failure($modx->lexicon('campaigner.newsletter.error.notfound'));
$newsletter->fromArray($_POST);

// save it
if ($newsletter->save() == false) {
    return $modx->error->failure($modx->lexicon('campaigner.err_save'));
}
return $modx->error->success('',$newsletter);