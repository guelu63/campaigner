<?php
/**
 * Processor: Check email for duplicate
 * @param array $_POST The data
 * @return mixed Object containing true or false
 */
// return $modx->error->success();
if($modx->campaigner->emailTaken($_POST['email'])) {
	$modx->error->addField('email',$modx->lexicon('campaigner.subscribe.error.emailtaken'));
	return $modx->error->failure();
}
return $modx->error->success();