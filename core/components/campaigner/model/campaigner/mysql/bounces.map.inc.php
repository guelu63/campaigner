<?php
$xpdo_meta_map['Bounces']= array (
  'package' => 'campaigner',
  'table' => 'bounces',
  'fields' => 
  array (
    'subscriber' => NULL,
    'newsletter' => NULL,
    'reason' => NULL,
    'type' => NULL,
    'code' => NULL,
    'recieved' => NULL,
  ),
  'fieldMeta' => 
  array (
    'subscriber' => 
    array (
      'dbtype' => 'mediumint',
      'precision' => '8',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'newsletter' => 
    array (
      'dbtype' => 'smallint',
      'precision' => '5',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'reason' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'type' => 
    array (
      'dbtype' => 'enum',
      'precision' => '"h,s"',
      'phptype' => 'string',
      'default' => 'h',
      'null' => false,
    ),
    'code' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '5',
      'phptype' => 'string',
      'null' => false,
    ),
    'recieved' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
    )
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
  'aggregates' => 
  array (
    'Newsletter' => 
    array (
      'class' => 'Newsletter',
      'local' => 'newsletter',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Subscriber' => 
    array (
      'class' => 'Subscriber',
      'local' => 'subscriber',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
