<?php

if(!empty($_POST['id'])) {
    $subscriber = $modx->getObject('Subscriber', array('id' => $_POST['id']));
}

if(!$subscriber) return $modx->error->failure($modx->lexicon('campaigner.subscriber.error.notfound'));

$subscriber->fromArray(array('active' => '1'));

if ($subscriber->save() == false) {
    return $modx->error->failure($modx->lexicon('campaigner.error.save'));
}

return $modx->error->success('',$subscriber);