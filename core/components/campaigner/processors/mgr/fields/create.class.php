<?php
class FieldsCreateProcessor extends modObjectCreateProcessor {
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

    public function beforeSave() {
        $name = $this->getProperty('name');
 
        if (empty($name)) {
            $this->addFieldError('name',$this->modx->lexicon('doodles.doodle_err_ns_name'));
        } else if ($this->doesAlreadyExist(array('name' => $name))) {
            $this->addFieldError('name',$this->modx->lexicon('doodles.doodle_err_ae'));
        }
        return parent::beforeSave();
    }
}
return 'FieldsCreateProcessor';