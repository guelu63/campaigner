<?php
class SocialSharingUpdateProcessor extends modObjectUpdateProcessor {
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
}
return 'SocialSharingUpdateProcessor';