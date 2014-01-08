<?php
class AutoresponderGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'Autoresponder';
    public $languageTopics = array('campaigner:default');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'campaigner.fields';

    public function prepareRow(xPDOObject $object) {
        $ta = $object->toArray('', false, true);
        $options = json_decode($ta['options']);
        $ta['event'] = $options->event;
        $ta['field'] = $options->field;
        $ta['time'] = $options->time;
        $ta['delay_value'] = $options->delay_value;
        $ta['weekday'] = $options->weekday;
        $ta['delay_unit'] = $options->delay_unit == 3600 ? 'Stunden' : ($options->delay_unit == 86400 ? 'Tage' : ($options->delay_unit == 604800 ? 'Wochen' : ($options->delay_unit == 2628000 ? 'Monate' : 'Jahre')));
        return $ta;
    }
}
return 'AutoresponderGetListProcessor';