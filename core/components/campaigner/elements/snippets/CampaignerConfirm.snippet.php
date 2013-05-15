<?php
/**
 * CampaignerConfirm snippet for campaigner extra
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
 * Confirms subscriptions
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package campaigner
 **/

$campaigner = $modx->getService('campaigner', 'Campaigner', $modx->getOption('core_path'). 'components/campaigner/model/campaigner/');
if (!($campaigner instanceof Campaigner)) return;

$tpl       = $modx->getOption('tpl', $scriptProperties, 'campaignerMessage');
$errorTpl  = $modx->getOption('errorTpl', $scriptProperties, $tpl);

$get = $modx->request->getParameters();

if(empty($get['subscriber'])) return;

if($campaigner->confirm($get['subscriber'], $get['key'])) {
    $params['message'] = $modx->lexicon('campaigner.confirm.success');
    $params['type']    = 'success';
    return $modx->getChunk($tpl, $params);
} else {
    $params['message'] = $campaigner->errormsg[0];
    $params['type']    = 'error';
    return $modx->getChunk($errorTpl, $params);
}