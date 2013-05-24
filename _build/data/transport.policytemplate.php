<?php

$templates = array();

/* administrator template/policy */
$templates['1']= $modx->newObject('modAccessPolicyTemplate');
$templates['1']->fromArray(array(
    'id' => 1,
    'name' => 'CampaignerPolicyTemplate',
    'description' => 'A policy for campaigner component.',
    'lexicon' => 'campaigner:permissions',
    'template_group' => 1,
));
$permissions = include dirname(__FILE__).'/permissions/admin.policy.php';
if (is_array($permissions)) {
    $templates['1']->addMany($permissions);
} else { $modx->log(modX::LOG_LEVEL_ERROR,'Could not load Campaigner Policy Template.'); }

return $templates;