<?php
class SocialSharingGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'SocialSharing';
    public $languageTopics = array('campaigner:default');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'campaigner.socialsharing';
}
return 'SocialSharingGetListProcessor';