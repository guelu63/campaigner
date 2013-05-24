<?php
class SocialSharingRemoveProcessor extends modObjectRemoveProcessor {
    public $classKey = 'SocialSharing';
    public $languageTopics = array('campaigner:default');
    public $objectType = 'campaigner.socialsharing';
}
return 'SocialSharingRemoveProcessor';