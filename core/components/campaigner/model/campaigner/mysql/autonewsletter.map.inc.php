<?php
$xpdo_meta_map['Autonewsletter']= array (
  'package' => 'campaigner',
  'table' => 'autonewsletter',
  'fields' => 
  array (
    'docid' => NULL,
    'state' => NULL,
    'start' => NULL,
    'last' => NULL,
    'frequency' => NULL,
    'time' => NULL,
    'total' => NULL,
    'sender' => NULL,
    'sender_email' => NULL,
    'description' => NULL,
  ),
  'fieldMeta' => 
  array (
    'docid' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'state' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'start' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'last' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
    ),
    'frequency' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'time' => 
    array (
      'dbtype' => 'time',
      'phptype' => 'string',
      'null' => false,
    ),
    'total' => 
    array (
      'dbtype' => 'mediumint',
      'precision' => '8',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'sender' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'sender_email' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'description' =>
    array(
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
    'AutonewsletterGroup' => 
    array (
      'class' => 'AutonewsletterGroup',
      'local' => 'id',
      'foreign' => 'autonewsletter',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'modDocument' => 
    array (
      'class' => 'modDocument',
      'local' => 'docid',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
