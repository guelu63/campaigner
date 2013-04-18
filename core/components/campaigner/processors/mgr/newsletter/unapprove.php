<?php

if(!empty($_POST['id'])) {
    $newsletter = $modx->getObject('Newsletter', array('id' => $_POST['id']));
}

if(!$newsletter) return $modx->error->failure($modx->lexicon('campaigner.newsletter.error.notfound'));

$newsletter->fromArray(array('state' => '0'));

if ($newsletter->save() == false) {
    return $modx->error->failure($modx->lexicon('campaigner.error.save'));
}

return $modx->error->success('',$newsletter);