<?php
$xpdo_meta_map['Fields']= array (
  'package' => 'campaigner',
  'table' => 'fields',
  'fields' => 
  array (
    'name' => NULL,
    'label' => NULL,
    'type' => NULL,
    'required' => NULL,
    'values' => NULL,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => false,
    ),
    'label' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => true,
    ),
    'type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => true,
    ),
    'required' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
    ),
    'values' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'columns' => 
      array (
        'id' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'SubscriberFields' => 
    array (
      'class' => 'SubscriberFields',
      'local' => 'id',
      'foreign' => 'field',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);