<?php
class AutorespondersUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'Autoresponders';
    public $languageTopics = array('campaigner:default');
    public $objectType = 'campaigner.fields';

    public function beforeSet() {
        $required = $this->getProperty('required');
        if ($required == 'on') { $required = true; }
        else  { $required = false; }
        $this->setProperty('required',$required);

        $active = $this->getProperty('active');
        if ($active == 'on') { $active = true; }
        else  { $active = false; }
        $this->setProperty('active',$active);
        return parent::beforeSet();
    }
}
return 'AutorespondersUpdateProcessor';