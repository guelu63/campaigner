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
 * @var $chunk string Form chunk
 * @var $required string Required form fields, e.g.: `address:bool,email:email,firstname,lastname,groups:int`
 *
 * @package campaigner
 **/
 // error_reporting(-1);

// Properties
$chunk  = $modx->getOption('chunk', $scriptProperties, 'CampaignerForm');
$success_chunk = $modx->getOption('success_chunk', $scriptProperties, 'CampaignerSuccess');
$preset_groups = $modx->getOption('groups', $scriptProperties, $modx->getOption('campaigner.default_groups'));

// Store this resource
$resource = $modx->resource;

// Successful subscription
if($_GET['success'])
  return $modx->getChunk($success_chunk);

$campaigner = $modx->getService('campaigner', 'Campaigner', $modx->getOption('core_path'). 'components/campaigner/model/campaigner/');
if (!($campaigner instanceof Campaigner)) return 'nono';

// Form - Parameter
$post = $modx->request->getParameters(array(), 'POST');

// Include a checkbox for TAC
if($modx->getOption('campaigner.tac'))
  $params['rights_check'] = $modx->getChunk('CampaignerRights');

// Add groups checkboxes - [[+groups_check]]
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

// No data submitted
if(!isset($post['subscribe']))
  return $modx->getChunk($chunk, $params);

// Set groups - either set via property, system setting or form input
if($post && (!$post['groups'] || !isset($post['groups'])))
  $post['groups'] = explode(',', $preset_groups);
if($post && !$post['groups'] && !is_array($post['groups']))
  $post['groups'] = $post['groups'] ? $post['groups'] : false;


// Prepare required fields
$req_field = explode(',', $modx->getOption('required', $scriptProperties));
foreach($req_field as $f) {
  $p = array();
  $p = explode(':', $f);
  $req_def[$p[0]]['field'] = $p[0];
  $req_def[$p[0]]['valid'] = $p[1];
  // var_dump($p);
  // $required[$p[0]]['field'] = $p[0];
  // if(!empty($p[1]))
  //   $required[$p[0]]['valid'] = $p[1];
}

$params = array_merge($post, $params);

// var_dump($req_def);
// var_dump($params);
// Check for required fields
// Check if required fields are set
// Check if required fields are not empty
// Check if required fields have valid values
if($params && is_array($params)) {
  // Iterate through required fields (snippet property)
  foreach($req_def as $key => $value) {
    $valid = '';
    // Not in params => set error
    if(!in_array($key, array_keys($params))) {
      $params['error']['required'][$key]['field'] = $key;
      $params['error']['required'][$key]['message'] = '(' . strtoupper($key) . ' erwartet)';
    }
    // In params => check
    if(in_array($key, array_keys($params))) {
      // No validity check added
      if(empty($value['valid'])) {
        // Check for set, not empty checks
        if(is_array($params[$key])) {
          $arr_err = true;
          foreach($params[$key] as $val) {
            if(!isset($val) || empty($val) || $val == '')
              $arr_err = false;
          }
          if($arr_err === false) {
            $params['error']['required'][$key]['field'] = $key;
            $params['error']['required'][$key]['message'] = '(' . strtoupper($key) . ' erwartet)';
          }
        }
        if(!isset($params[$key]) || empty($params[$key]) || $params[$key] == '') {
          $params['error']['required'][$key]['field'] = $key;
          $params['error']['required'][$key]['message'] = '(' . strtoupper($key) . ' erwartet)';
        }
      // Validity check found
      } else {
        // Check for valid email address
        if(!filter_var($params[$key], ($value['valid'] == 'email' ? FILTER_VALIDATE_EMAIL : ''))) {
          $params['error']['required'][$key]['field'] = $key;
          $params['error']['required'][$key]['message'] = '(' . strtoupper($value['valid']) . ' erwartet)';
        }
      }
    }
  }
}

// var_dump($params['error']['required']);
$submit = false;
if(count(array_filter($post)) > 0)
  $submit = true;

if($submit && !isset($post['subscribed']))
  $errors['error']['top'] = 'Formular falsch!';

if($submit && $modx->getOption('campaigner.tac') && !isset($post['rights']))
  $errors['error']['field.tac'] = 'Sie müssen den Nutzungsbestimmungen zustimmen';

if($submit && !$post['groups'])
  $errors['error']['field.groups'] = 'Wählen Sie mindestens eine Gruppe!';

// $post['groups'] = $modx->getOption('campaigner.default_groups');

// if($submit && count($params['error']) <= 0) {
//   if(!$campaigner->subscribe($post))
//     return json_decode($campaigner->errormsg);
// }

// var_dump($params);

// FUTURE DEVELOPMENT - AJAX HANDLING
// Registering the JS which handles the AJAX request
// $url = $modx->makeUrl(401);
// $modx->regClientHTMLBlock('<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>');
// $modx->regClientScript($campaigner->config['jsUrl'] . 'web/request.js');
// $js .= 'CAMPAIGNER.url = "' . $url . '"' . "\n";
// $js .= 'CAMPAIGNER.ajax = 1' . "\n";
// $js .= 'CAMPAIGNER.REQUEST.subscribe();' . "\n";
// $modx->regClientHTMLBlock('<script type="text/javascript">'.$js.'</script>');


// Error handling
if($submit && count($params['error']) > 0) {
  // var_dump($params['error']);
  foreach($params['error'] as $field) {
    $errors['error'][$field['field']] = implode('<br/>', $field['message']);
  }
  foreach($params['error']['required'] as $field) {
    $errors['error']['field.'.$field['field']] = 'Korrektur notwendig';
  }
}

if($errors && is_array($errors))
  $params = array_merge($params, $errors);


// Found errors - return form with filled error messages
if(!$submit || count($params['error']) > 0)
  return $modx->getChunk($chunk, $params);


// No errors - try to subscribe the user
if($submit)
  $success = $campaigner->subscribe($post);


// Check for errors in subscription method
if(!$success)
  return $modx->getChunk($chunk, array_merge($params, array('error' => implode('<br/>', $campaigner->errormsg))));


// Successful subscription phase 1
if(!$campaigner->errormsg && $success) {
  unset($params);
  $params['msg']['success'] = $modx->lexicon('campaigner.subscribe.success');
  $params['msg']['email_sent'] = $modx->lexicon('campaigner.subscribe.email_sent');
}


// echo $modx->makeUrl($modx->resource->get('id'), $modx->context->key, array('success' => true));
// return;
// Return successfully submitted form
$modx->sendRedirect($modx->makeUrl($resource->get('id'), $resource->get('context_key'), array('success' => true)));


// return $modx->getChunk($success_chunk);