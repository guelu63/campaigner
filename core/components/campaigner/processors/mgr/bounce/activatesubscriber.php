<?php
$markedSubscribers = explode(',', $_REQUEST['markedSubscribers']);

if (count($markedSubscribers) < 1) {
	return $modx->error->failure($modx->lexicon('campaigner.subscriber.err_notfound'));
}
elseif(count($markedSubscribers) == 1) {
	//Bounce Objekt holen, weil wir hier nur die ID des Bounces haben und so irgendwie auf den Subscriber kommen muessen
	//und in diese Bounce Objekt befindet sich die Subscriber ID -> geht sicher einfacher, indem man schon aus dem JS das
	//komplette Setting aus dem Grid uebergibt (noch keine Idee wie das geht)
	$bounce = $modx->getObject('Bounces', $markedSubscribers[0]);
	
	if ($bounce == null) {
		return $modx->error->failure($modx->lexicon('campaigner.bounce.notfound'));
	}
	
	$subscriber = $modx->getObject('Subscriber', $bounce->get('subscriber'));

	if ($subscriber == null) {
		return $modx->error->failure($modx->lexicon('campaigner.subscriber.notfound'));
	}
	
	// Deactivate Subscriber
        if($subscriber->get('active') != 1) {                            
            $subscriber->set('active', 1);
            $subscriber->save();
        }
}
else {
	foreach($markedSubscribers as $nr => $bounceId) {
		//Bounce Objekt holen, weil wir hier nur die ID des Bounces haben und so irgendwie auf den Subscriber kommen muessen
		//und in diese Bounce Objekt befindet sich die Subscriber ID -> geht sicher einfacher, indem man schon aus dem JS das
		//komplette Setting aus dem Grid uebergibt (noch keine Idee wie das geht)
		$bounce = $modx->getObject('Bounces', $bounceId);
	
		if ($bounce == null) {
			return $modx->error->failure($modx->lexicon('campaigner.bounce.notfound'));
		}
		
		$subscriber = $modx->getObject('Subscriber', $bounce->get('subscriber'));

		if ($subscriber == null) {
			return $modx->error->failure($modx->lexicon('campaigner.subscriber.notfound'));
		}
		
		// Deactivate Subscriber
                if($subscriber->get('active') != 1) {                            
                    $subscriber->set('active', 1);
                    $subscriber->save();
                }
	}
}

return $modx->error->success('campaigner.subscriber.removed');