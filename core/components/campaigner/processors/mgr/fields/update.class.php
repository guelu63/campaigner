<?php
class FieldsUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'Fields';
    public $languageTopics = array('campaigner:default');
    public $objectType = 'campaigner.fields';

    public function beforeSet() {
        $active = $this->getProperty('required');
        if ($active == 'on') { $active = true; }
        else  { $active = false; }
        $this->setProperty('required',$active);
        return parent::beforeSet();
    }
}
return 'FieldsUpdateProcessor';