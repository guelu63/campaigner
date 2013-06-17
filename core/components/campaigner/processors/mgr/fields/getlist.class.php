<?php
class FieldsGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'Fields';
    public $languageTopics = array('campaigner:default');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'campaigner.fields';
}
return 'FieldsGetListProcessor';