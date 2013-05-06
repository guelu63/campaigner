<?php
$xpdo_meta_map['Group']= array (
  'package' => 'campaigner',
  'table' => 'group',
  'fields' => 
  array (
    'name' => NULL,
    'public' => NULL,
    'subscribers' => NULL,
    'color' => NULL,
    'priority' => NULL,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
    ),
    'public' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'subscribers' => 
    array (
      'dbtype' => 'mediumint',
      'precision' => '8',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'color' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '7',
      'phptype' => 'string',
      'null' => false,
    ),
    'priority' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
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
      'foreign' => 'group',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'NewsletterGroup' => 
    array (
      'class' => 'NewsletterGroup',
      'local' => 'id',
      'foreign' => 'group',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
