<?php
class SocialSharingCreateProcessor extends modObjectCreateProcessor {
    public $classKey = 'SocialSharing';
    public $languageTopics = array('campaigner:default');
    public $objectType = 'campaigner.socialsharing';

    public function beforeSet() {
        $active = $this->getProperty('active');
        if ($active == 'on') { $active = true; }
        else  { $active = false; }
        $this->setProperty('active',$active);
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
return 'SocialSharingCreateProcessor';