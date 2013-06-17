<?php

// validate properties
if (empty($_POST['email'])) $modx->error->addField('email',$modx->lexicon('campaigner.subscriber.error.noemail'));

if(!preg_match("/^(.+)@([^@]+)$/", $_POST['email'])) {
    $modx->error->addField('email',$modx->lexicon('campaigner.subscriber.error.noemail'));
}

if ($modx->error->hasError()) {
    return $modx->error->failure();
}

$groups = $_POST['groups'];
unset($_POST['groups']);

// get the object
if(!empty($_POST['subscriber'])) {
    $subscriber = $modx->getObject('Subscriber', array('id' => $_POST['subscriber']));
    if(!$subscriber) {
        return $modx->error->failure($modx->lexicon('campaigner.subscriber.error.notfound'));
    }
    else {
        //Wenn schon die Email Adresse drinnen ist dann bringt das wieder aufnehmen nix -> Error
        /*if($subscriber->get('email') == $_POST['email']) {
            return $modx->error->failure($modx->lexicon('campaigner.subscriber.error.sameemail'));    
        }*/
        $subscriber->set('email', $_POST['email']);
        $subscriber->set('active', 1);

        /* query for bouncing messages */
        $c = $modx->newQuery('Bounces');
        
        $count = $modx->getCount('Bounces',$c);
        $c->where('`Bounces`.`subscriber`="'.$_POST['subscriber'].'"');
        
        //$c->prepare(); var_dump($c->toSQL()); die;
        
        $bounce = $modx->getCollection('Bounces',$c);
        
        //Delete Bounces
        foreach ($bounce as $item) {
            $item->remove();
        }
    }
} else {
    return $modx->error->failure($modx->lexicon('campaigner.subscriber.error.noid'));
}

// save it
if ($subscriber->save() == false) {
    return $modx->error->failure($modx->lexicon('campaigner.err_save'));
}

return $modx->error->success('',$subscriber);