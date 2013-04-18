<?php
$markedJobs = explode(',', $_REQUEST['markedJobs']);

//return $modx->error->failure(var_export($markedJobs, true));

if (count($markedJobs) < 1) {
	return $modx->error->failure($modx->lexicon('campaigner.subscriber.err_notfound'));
}
elseif(count($markedJobs) == 1) {        
        //ResendCheck Objekt holen
        $resendCheckItem = $modx->getObject('ResendCheck', $markedJobs[0]);
        
        if ($resendCheckItem == null) {
                return $modx->error->failure($modx->lexicon('campaigner.resendCheck.notfound'));
        }
        
	//Queue Objekt holen
        $queueItem = $modx->getObject('Queue', $resendCheckItem->get('queue_id'));
        
        if ($queueItem == null) {
                return $modx->error->failure($modx->lexicon('campaigner.queue.notfound'));
        }
        
        //Queue Objekt entfernen
        $queueItem->remove();
        
        //ResendCheck Objekt entfernen
        $resendCheckItem->remove();
}
else {
	foreach($markedJobs as $nr => $resendCheckId) {
            //ResendCheck Objekt holen
            $resendCheckItem = $modx->getObject('ResendCheck', $resendCheckId);
            
            if ($resendCheckItem == null) {
                    return $modx->error->failure($modx->lexicon('campaigner.resendCheck.notfound'));
            }
            
            //Queue Objekt holen
            $queueItem = $modx->getObject('Queue', $resendCheckItem->get('queue_id'));
            
            if ($queueItem == null) {
                    return $modx->error->failure($modx->lexicon('campaigner.queue.notfound'));
            }
            
            //Queue Objekt entfernen
            $queueItem->remove();
            
            //ResendCheck Objekt entfernen
            $resendCheckItem->remove();
	}
}

return $modx->error->success('campaigner.resend.removed');