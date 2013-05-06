<?php
/**
 * CampaignerTracking plugin for campaigner extra
 *
 * Copyright 2013 by Subsolutions <http://www.subsolutions.at>
 * Created on 04-18-2013
 *
 * campaigner is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * campaigner is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * campaigner; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package campaigner
 */

/**
 * Description
 * -----------
 * Creates campaignes when events are hit
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package campaigner
 **/
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