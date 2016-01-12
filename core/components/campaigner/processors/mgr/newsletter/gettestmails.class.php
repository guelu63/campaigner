<?php

class TestmailsGetNodesProcessor extends modObjectProcessor {
	public $classKey = 'Newsletter';
	public $languageTopics = array('campaigner:default');
	public $defaultSortField = 'name';
	public $defaultSortDirection = 'ASC';
	public $objectType = 'newsletter';

	public function process()
	{
		$subscriber = $this->modx->getObject('Subscriber', array('email' => 'andreas.bilz@gmail.com'));
		$subtags = $this->modx->campaigner->getSubscriberTags($subscriber);
		$arr = array();
		$i = 0;
    $values = explode(',', $this->modx->getOption('campaigner.test_mail'));
    return $this->toJSON($values);
		// foreach($values as $key => $value) {
		// 	$arr[] = array(
		// 		'text' => '[[+'.$key.']]',
		// 		'name' => $key,
		// 		'description' => $value,
		// 		'parentid' => 0,
		// 		'id' => 'n_ug_' . $i,
		// 		'leaf' => true,
		// 		'type' => 'file',
		// 		'url' => '[[+'.$key.']]',
		// 		'cls' => ''
		// 		);
		// 	$i++;
		// }
		// return $this->toJSON($sub_arr);
	}
}
return 'TestmailsGetNodesProcessor';
