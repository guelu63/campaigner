<?php
$xpdo_meta_map['ResendCheck']= array (
  'package' => 'campaigner',
  'table' => 'resend_check',
  'fields' => 
  array (
    'queue_id' => NULL,
    'state' => NULL,
  ),
  'fieldMeta' => 
  array (
    'queue_id' => 
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
      'default' => '-1',
      'phptype' => 'integer',
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
  )
);
