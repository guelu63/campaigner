<?php
/**
 * Processor: Get placeholders and settings for campaigner
 *
 * Builds a ExtJS tree with different values
 */

// class modElementGetNodesProcessor extends modProcessor {
//     public $typeMap = array(
//         'template' => 'modTemplate',
//         'tv' => 'modTemplateVar',
//         'chunk' => 'modChunk',
//         'snippet' => 'modSnippet',
//         'plugin' => 'modPlugin',
//         'category' => 'modCategory',
//     );
//     public $actionMap = array();
    
//     public function checkPermissions() {
//         return $this->modx->hasPermission('element_tree');
//     }
//     public function getLanguageTopics() {
//         return array('category','element');
//     }

//     public function initialize() {
//         $this->setDefaultProperties(array(
//             'stringLiterals' => false,
//             'id' => 0,
//         ));
//         return true;
//     }

//     public function process() {

// $newList[] = array(
// 'text' => $item->get('name'), 
// 'name' => $item->get('name'), 
// 'description' => $item->get('description'), 
// 'parentid' => $item->get('id'), 
// 'id' => 'n_ug_0' . $item->get('id'), 
// 'leaf' => $leaf, 
// 'type' => $type, 
// 'cls' => ''

class PlaceholderGetNodesProcessor extends modObjectProcessor {
	public $classKey = 'Newsletter';
	public $languageTopics = array('campaigner:default');
	public $defaultSortField = 'name';
	public $defaultSortDirection = 'ASC';
	public $objectType = 'newsletter';

	public function process()
	{
		$subscriber = $this->modx->getObject('Subscriber', array('email' => 'andreas.bilz@gmail.com'));
		$subtags = $this->modx->campaigner->getSubscriberTags($subscriber);
		$sub_arr = array();
		$i = 0;
		foreach($subtags as $key => $value) {
			$sub_arr[] = array(
				'text' => '[[+'.$key.']]',
				'name' => $key,
				'description' => $value,
				'parentid' => 0,
				'id' => 'n_ug_' . $i,
				'leaf' => true,
				'type' => 'file',
				'url' => '[[+'.$key.']]',
				'cls' => ''
				);
			$i++;
		}
		return $this->toJSON($sub_arr);
	}
}
return 'PlaceholderGetNodesProcessor';