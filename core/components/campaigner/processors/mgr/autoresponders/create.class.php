<?php
class AutoresponderCreateProcessor extends modObjectCreateProcessor {
    public $classKey = 'Autoresponder';
    public $languageTopics = array('campaigner:default');
    public $objectType = 'campaigner.fields';

    public function beforeSet() {
        // $required = $this->getProperty('required');
        // if ($required == 'on') { $required = true; }
        // else  { $required = false; }
        // $this->setProperty('required',$required);
        $options = $this->getProperty('options');
        $parse = array();
        if (!preg_match ('#^(?<hours>[\d]{2}):(?<mins>[\d]{2})$#',$options['time'],$parse)) {
            // Throw error, exception, etc
            throw new RuntimeException ("Hour Format not valid");
        }
        $options['delay_sec'] = (int) $options['delay_unit'] * $options['delay_value'];
        $options['time_sec'] = (int) $parse['hours'] * 3600 + (int) $parse['mins'];
        $this->setProperty('options', json_encode($options));
        // $active = $this->getProperty('active');
        // if ($active == 'on') { $active = true; }
        // else  { $active = false; }
        // $this->setProperty('active',$active);
        return parent::beforeSet();
    }

    // public function beforeSave() {
        // $name = $this->getProperty('name');
 
        // if (empty($name)) {
        //     $this->addFieldError('name',$this->modx->lexicon('doodles.doodle_err_ns_name'));
        // } else if ($this->doesAlreadyExist(array('name' => $name))) {
        //     $this->addFieldError('name',$this->modx->lexicon('doodles.doodle_err_ae'));
        // }
        // return parent::beforeSave();
    // }
}
return 'AutoresponderCreateProcessor';