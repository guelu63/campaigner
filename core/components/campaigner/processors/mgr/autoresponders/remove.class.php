<?php
class AutorespondersRemoveProcessor extends modObjectRemoveProcessor {
    public $classKey = 'Autoresponder';
    public $languageTopics = array('campaigner:default');
    public $objectType = 'campaigner.fields';
}
return 'AutorespondersRemoveProcessor';