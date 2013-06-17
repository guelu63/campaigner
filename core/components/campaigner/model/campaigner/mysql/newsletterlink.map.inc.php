<?php
$xpdo_meta_map['NewsletterLink']= array (
  'package' => 'campaigner',
  'table' => 'newsletterlink',
  'fields' => 
  array (
    'newsletter' => NULL,
    'url' => NULL,
    'type' => NULL,
  ),
  'fieldMeta' => 
  array (
    'newsletter' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'url' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
    'type' => 
    array (
      'dbtype' => 'set',
      'precision' => '\'link\',\'image\'',
      'phptype' => 'string',
      'null' => true,
      'default' => 'link',
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