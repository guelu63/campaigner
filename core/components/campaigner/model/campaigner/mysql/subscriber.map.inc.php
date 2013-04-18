<?php
$xpdo_meta_map['Subscriber']= array (
  'package' => 'campaigner',
  'table' => 'subscriber',
  'fields' => 
  array (
    'active' => NULL,
    'email' => NULL,
    'firstname' => NULL,
    'lastname' => NULL,
    'title' => NULL,
    'company' => NULL,
    'text' => NULL,
    'key' => NULL,
    'since' => NULL,
  ),
  'fieldMeta' => 
  array (
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'email' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'firstname' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'lastname' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'title' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => true,
    ),
    'company' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'text' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
    ),
    'key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => true,
    ),
    'since' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
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
    'GroupSubscriber' => 
    array (
      'class' => 'GroupSubscriber',
      'local' => 'id',
      'foreign' => 'subscriber',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Queue' => 
    array (
      'class' => 'Queue',
      'local' => 'id',
      'foreign' => 'subscriber',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Bounce' => 
    array (
      'class' => 'Bounce',
      'local' => 'id',
      'foreign' => 'subscriber',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
