<?php
$eventName = $modx->event->name;
switch($eventName) {
    case 'OnLoadWebDocument':
    /* tracking */
        if (is_object($modx->resource) && count($_GET) >= 1 ) {
            // get the current page ID and compare to the system settings ID for 
            
            if (!isset($modx->groupEletters)) {
                $modx->addPackage('campaigner', $modx->getOption('core_path').'components/campaigner/model/');
                $modx->groupEletters = $modx->getService('campaigner', 'Campaigner', $modx->getOption('core_path').'components/campaigner/model/campaigner/');
            }
            $campaigner =& $modx->campaigner;
            
            $page_id = $modx->resource->get('id');
            $tracking_id = $modx->getOption('campaigner.tracking_page');
            
            if ( $page_id == $tracking_id ) {
                // process the url and redirect if needed:
                // now load the tracking stuff:
                // $etracker = $groupEletters->loadTracker();
                // $etracker->debug = (boolean)$modx->getOption('groupeletters.debug',NULL, 0);
                $campaigner->logAction('click');
            }
        }
    break;
}