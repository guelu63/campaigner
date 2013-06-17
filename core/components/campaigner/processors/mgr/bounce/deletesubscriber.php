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
		
	/* query for bouncing messages */
	$c = $modx->newQuery('Bounces');
	
	$count = $modx->getCount('Bounces',$c);
	$c->where('`Bounces`.`subscriber`="'.$subscriber->get('id').'"');
	
	//$c->prepare(); var_dump($c->toSQL()); die;
	
	$bounce = $modx->getCollection('Bounces',$c);
	
	//Delete Bounces
	foreach ($bounce as $item) {
	    $item->remove();
	}
	
	/** QUEUE ELEMENTE UND RESEND ELEMENTE von diesem Subscriber auch loeschen **/
	//Alle ResendCheck Elemente von diesem Subscriber holen
	$c = $modx->newQuery('ResendCheck');
	$count = $modx->getCount('ResendCheck',$c);
	$c->leftJoin('Queue', 'Queue', '`Queue`.`id` = `ResendCheck`.`queue_id`');
	$c->where('`Queue`.`subscriber`="'.$subscriber->get('id').'"');
	$resends = $modx->getCollection('ResendCheck',$c);
	
	//Alle ResendCheck Elemente durchgehen
	foreach($resends as $resendElement) {
		//Dazugehoeriges Queue Element holen
		$queueElement = $modx->getObject('Queue', $resendElement->get('queue_id'));
	
		if ($queueElement == null) {
			return $modx->error->failure($modx->lexicon('campaigner.queue.notfound'));
		}
		
		//Queue Element loeschen
		$queueElement->remove();
		
		//ResendCheck Element loeschen
		$resendElement->remove();
	}
	
	//Delete Subscriber
	$subscriber->remove();
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
		
		//return $modx->error->failure("Subscriber: ".$subscriber->get('email'));
		
		/* query for bouncing messages */
		$c = $modx->newQuery('Bounces');
		
		$count = $modx->getCount('Bounces',$c);
		$c->where('`Bounces`.`subscriber`="'.$subscriber->get('id').'"');
		
		//$c->prepare(); var_dump($c->toSQL()); die;
		
		$bounce = $modx->getCollection('Bounces',$c);
		
		//Delete Bounces
		foreach ($bounce as $item) {
		    $item->remove();
		}
		
		/** QUEUE ELEMENTE UND RESEND ELEMENTE von diesem Subscriber auch loeschen **/
		//Alle ResendCheck Elemente von diesem Subscriber holen
		$c = $modx->newQuery('ResendCheck');
		$count = $modx->getCount('ResendCheck',$c);
		$c->leftJoin('Queue', 'Queue', '`Queue`.`id` = `ResendCheck`.`queue_id`');
		$c->where('`Queue`.`subscriber`="'.$subscriber->get('id').'"');
		$resends = $modx->getCollection('ResendCheck',$c);
		
		//Alle ResendCheck Elemente durchgehen
		foreach($resends as $resendElement) {
			//Dazugehoeriges Queue Element holen
			$queueElement = $modx->getObject('Queue', $resendElement->get('queue_id'));
		
			if ($queueElement == null) {
				return $modx->error->failure($modx->lexicon('campaigner.queue.notfound'));
			}
			
			//Queue Element loeschen
			$queueElement->remove();
			
			//ResendCheck Element loeschen
			$resendElement->remove();
		}
		
		//Delete Subscriber
		$subscriber->remove();
	}
}

return $modx->error->success('campaigner.subscriber.removed');