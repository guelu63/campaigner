<?php
class ARFieldsGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'Fields';
    public $languageTopics = array('campaigner:default');
    public $defaultSortField = 'menuindex';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'campaigner.fields';

    public function prepareQueryBeforeCount(xPDOQuery $c) {
    	switch($this->getProperty('type')) {
    		case 'birthday':
    		case 'annual':
    			$type = 'xdatetime';
    		break;
    	}
    	$c->where(array('type' => $type));
    	return $c;
    }

    public function prepareRow(xPDOObject $object) {
        $ta = $object->toArray('', false, true);
        $arr = array();
        $arr['value'] = $ta['name'];
        $arr['display'] = $ta['label'];
        return $arr;
    }
}
return 'ARFieldsGetListProcessor';