<?php

// Validate properties
if (empty($_POST['name'])) $modx->error->addField('name',$modx->lexicon('campaigner.group.error.noname'));

// Default color is white
if (empty($_POST['color'])) {
  $_POST['color'] = '#000000';
  // $modx->error->addField('color',$modx->lexicon('campaigner.group.error.nocolor'));
}

if(isset($_POST['color']) && !preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $_POST['color'])) {
  $modx->error->addField('color',$modx->lexicon('campaigner.groups.error.invalidcolor'));
}

if ($modx->error->hasError()) {
    return $modx->error->failure();
}

// some default values
$_POST['public'] = empty($_POST['public']) ? 0 : 1;
$_POST['subscribers'] = 0;

// get the object
if(!empty($_POST['id'])) {
    $group = $modx->getObject('Group', array('id' => $_POST['id']));
    if(!$group) return $modx->error->failure($modx->lexicon('campaigner.group.notfound'));
} else {
    $group = $modx->newObject('Group');
}
$group->fromArray($_POST);

// save it
if ($group->save() == false) {
    return $modx->error->failure($modx->lexicon('campaigner.err_save'));
}

return $modx->error->success('',$group);
