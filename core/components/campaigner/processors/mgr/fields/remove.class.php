<?php
class FieldsRemoveProcessor extends modObjectRemoveProcessor {
    public $classKey = 'Fields';
    public $languageTopics = array('campaigner:default');
    public $objectType = 'campaigner.fields';
}
return 'FieldsRemoveProcessor';