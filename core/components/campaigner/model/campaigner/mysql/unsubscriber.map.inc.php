<?php
$xpdo_meta_map['Unsubscriber']= array (
  'package' => 'campaigner',
  'table' => 'unsubscriber',
  'fields' => 
  array (
    'subscriber' => NULL,
    'newsletter' => NULL,
    'reason' => NULL,
    'date' => NULL,
    'via' => NULL,
  ),
  'fieldMeta' => 
  array (
    'subscriber' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'attributes' => 'unsigned',
      'phptype' => 'string',
      'null' => false,
    ),
    'newsletter' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'reason' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => true,
    ),
    'date' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'via' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
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
);
