<?php
class GeoLocGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'SubscriberHits';
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
        $c->leftJoin('Subscriber', 'Subscriber');
        $c->where(array('loc:!=' => NULL));
        $c->select($this->modx->getSelectColumns('SubscriberHits', 'SubscriberHits'));
        $c->select(array('Subscriber.email'));
        $c->prepare();
        // echo $c->toSQL();
        return $c;
    }

    public function prepareRow(xPDOObject $object) {
        $ta = $object->toArray('', false, true);
        $ta['ip'] = long2ip($ta['ip']);
        $loc = json_decode($ta['loc']);
        $ta['loc'] = $loc->cityName . ', ' . $loc->regionName . ' (' . $loc->countryCode . ')';
        $ta['longitude'] = $loc->longitude;
        $ta['latitude'] = $loc->latitude;
        $ta['timezone'] = $loc->timeZone;
        $ta['subscriber'] = $ta['email'];
        return $ta;
    }

    // public function afterIteration(array $list) {
    // 	$data = $list;
    // 	$list = array();
    // 	foreach ($data as $key => $value) {
    // 		while ($i <= 10) {
    // 			$j++;
    // 			if($value['start'] + ($value['frequency'] * $j) < time())
    // 				continue;
    // 			$list[] = array('start' => date('D, d.m.Y', $value['start'] + ($value['frequency'] * $j)));
    // 			$i++;
    // 		}
    // 	}
    // 	// var_dump($list);
    // 	return $list;
    // }
}
return 'GeoLocGetListProcessor';