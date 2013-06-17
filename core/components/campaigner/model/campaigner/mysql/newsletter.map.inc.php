<?php
$xpdo_meta_map['Newsletter']= array (
  'package' => 'campaigner',
  'table' => 'newsletter',
  'fields' => 
  array (
    'docid' => NULL,
    'state' => NULL,
    'sent_date' => NULL,
    'total' => NULL,
    'sent' => NULL,
    'bounced' => NULL,
    'sender' => NULL,
    'sender_email' => NULL,
    'auto' => NULL,
    'priority' => NULL,
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
      'null' => true,
    ),
    'sent_date' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
    ),
    'total' => 
    array (
      'dbtype' => 'mediumint',
      'precision' => '8',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
    ),
    'sent' => 
    array (
      'dbtype' => 'mediumint',
      'precision' => '8',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
    ),
    'bounced' => 
    array (
      'dbtype' => 'mediumint',
      'precision' => '8',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
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
    'auto' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
    ),
    'priority' =>
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
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
    'Queue' => 
    array (
      'class' => 'Queue',
      'local' => 'id',
      'foreign' => 'newsletter',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Bounce' => 
    array (
      'class' => 'Bounce',
      'local' => 'id',
      'foreign' => 'newsletter',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'NewsletterGroup' => 
    array (
      'class' => 'NewsletterGroup',
      'local' => 'id',
      'foreign' => 'newsletter',
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
