<?php
class ScheduleGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'Autonewsletter';
    public $languageTopics = array('campaigner:default');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'campaigner.fields';

    // public function prepareRow(xPDOObject $object) {
    //     $ta = $object->toArray('', false, true);
    //     $arr = array();
    //     // var_dump($ta);
    //     for ($i=0; $i < 10; $i++) { 
    //     	$arr[]['start'] = $ta['start'] + (86400 * $i);
    //     }
    //     // $arr[] = $ta
    //     return $arr;
    //     return $ta;
    // }
    
    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $nl_id = (int)$this->getProperty('id',0);
        $c->where(array('id' => $nl_id));
        return $c;
    }

    public function afterIteration(array $list) {
    	$data = $list;
    	$list = array();
    	foreach ($data as $key => $value) {
    		while ($i <= 10) {
    			$j++;
    			if($value['start'] + ($value['frequency'] * $j) < time())
    				continue;
    			$list[] = array('start' => date('D, d.m.Y', $value['start'] + ($value['frequency'] * $j)));
    			$i++;
    		}
    	}
    	// var_dump($list);
    	return $list;
    }
}
return 'ScheduleGetListProcessor';