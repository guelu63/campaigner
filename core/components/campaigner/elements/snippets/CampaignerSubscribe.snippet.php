<?php
/**
 * CampaignerSubscribe snippet for campaigner extra
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
 * Subscribe snippet
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package campaigner
 **/
 
$campaigner = $modx->getService('campaigner', 'Campaigner', $modx->getOption('core_path'). 'components/campaigner/model/campaigner/');
if (!($campaigner instanceof Campaigner)) return 'nono';

//$groups = $modx->getOption('groups', $scriptProperties, null);

// $_POST['email'] = 'jan.heinzle@subsolutions.at';

$post = $modx->request->getParameters(array(), 'POST');

$groups = $post['groups'] ? $post['groups'] : false;
$chunk  = $modx->getOption('chunk', $scriptProperties, 'CampaignerForm');
$params = $post;

$submit = false;
if(count(array_filter($post)) > 0)
  $submit = true;

if($submit && !isset($post['subscribed']))
  $params['error'][] = 'Formular falsch!';

if($submit && $modx->getOption('campaigner.tac') && !isset($post['rights']))
  $params['error'][] = 'Sie müssen den Nutzungsbestimmungen zustimmen';

if($submit && ($modx->getOption('campaigner.default_groups') == 0) && !$groups)
  $params['error'][] = 'Wählen Sie mindestens eine Gruppe!';

$post['groups'] = $modx->getOption('campaigner.default_groups');

var_dump($params);

$available_groups = $campaigner->getGroups();
foreach($available_groups as $group) {
  $args = array(
    'name' => 'groups[]',
    'value' => $group->get('id'),
    'display' => $group->get('name'),
    'checked' => $checked,
    'id' => 'group-' . $group->get('id'),
    );
  $params['groups_check'] .= $modx->getChunk('CampaignerCheckbox', $args);
}

if($submit && count($params['error']) > 0) {
  $params['error'] = implode('<br/>', $params['error']);
  return $modx->getChunk($chunk, $params);
}

if(!$submit && count($params['error']) == 0)
  return $modx->getChunk($chunk, $params);

if($submit)
  if(!$campaigner->subscribe($post))
    $params['error'][] = $campaigner->errormsg[$success];

$params['msg'] = $modx->lexicon('campaigner.subscribe.success');
$params['error'] = implode('<br/>', $params['error']);
return $modx->getChunk($chunk, $params);