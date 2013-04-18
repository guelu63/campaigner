<?php
require_once dirname(dirname(__FILE__)) . '/model/campaigner/campaigner.class.php';
$campaigner = new Campaigner($modx);

return $campaigner->initialize('mgr');