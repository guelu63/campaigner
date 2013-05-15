<?php
/**
 * CampaignerUnsubscribe snippet for campaigner extra
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
 * Unsubscribe snippet
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

// Get the chunk to render unsubscribe-form
$chunk  = $modx->getOption('chunk', $scriptProperties, 'CampaignerFormUnsubscribe');
$params = array();

// MODx Request GET/POST Parameters
$get = array(); $post = array();
$get = $modx->request->getParameters();
$post = $modx->request->getParameters(array(), 'POST');
var_dump($get);
// No subscriber nor key given => just return;
if((empty($get['subscriber']) && empty($_POST['subscriber'])) || (empty($get['key']) && empty($_POST['key']))) return;

// $params['subscriber'] = $get['subscriber'] ? $get['subscriber'] : $post['subscriber'];
// $params['key'] = $get['key'] ? $get['key'] : $post['key'];
$params = array_merge($get, $post);

if($post['changed']) {
	// $tpl       = $modx->getOption('tpl', $scriptProperties, 'campaignerMessage');
	// $errorTpl  = $modx->getOption('errorTpl', $scriptProperties, $tpl);
	if(empty($params['subscriber']))
		return;

	if($campaigner->unsubscribe($params)) {
	    $params['message'] = $modx->lexicon('campaigner.unsubscribe.success');
	    $params['msg'] = $campaigner->errormsg[0];
	    $params['type']    = 'success';
	    // return $modx->getChunk($tpl, $params);
	} else {
	    $params['message'] = $campaigner->errormsg[0];
	    $params['type']    = 'error';
	    // return $modx->getChunk($errorTpl, $params);
	}
}

// Get reasons for unsubscriptions via system settings (MODx input option value like string: 'Display==value||Display2==value2')
$reasons_str = $modx->getOption('campaigner.unsubscribe_reasons');
$reasons = array();
foreach (explode("||", $reasons_str) as $cLine) {
    list ($cKey, $cValue) = explode('==', $cLine, 2);
    $reasons[$cKey] = $cValue;
}

foreach($reasons as $display => $value) {
	$args = array(
		'value' => $value,
		'display' => $display,
		);
	$params['reasons_options'] .= $modx->getChunk('CampaignerOption', $args);
}

// Get the list of groups a user is subscribed to
$groups = $campaigner->getGroups($params['subscriber']);
foreach($groups as $group) {
	$checked = true;
	$args = array(
		'name' => 'groups[]',
		'value' => $group->get('gs_group'),
		'display' => $group->get('g_name'),
		'checked' => $checked,
		'id' => 'group-' . $group->get('gs_group'),
		);
	$params['groups_check'] .= $modx->getChunk('CampaignerCheckbox', $args);
}

return $modx->getChunk($chunk, $params);