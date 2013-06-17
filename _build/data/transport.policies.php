<?php
/**
 * Campaigner
 *
 * Copyright 2010-11 by Shaun McCormick <shaun@modx.com>
 *
 * This file is part of Campaigner, a simple commenting component for MODx Revolution.
 *
 * Campaigner is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Campaigner is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Campaigner; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package Campaigner
 */
/**
 * Default Campaigner Access Policies
 *
 * @package Campaigner
 * @subpackage build
 */
function bld_policyFormatData($permissions) {
    $data = array();
    foreach ($permissions as $permission) {
        $data[$permission->get('name')] = true;
    }
    return $data;
}
$policies = array();
$policies[1]= $modx->newObject('modAccessPolicy');
$policies[1]->fromArray(array (
  'id' => 1,
  'name' => 'CampaignerAdminPolicy',
  'description' => 'A policy for campaigner admins.',
  'parent' => 0,
  'class' => '',
  'lexicon' => 'campaigner:permissions',
  // 'data' => '{"quip.comment_approve":true,"quip.comment_list":true,"quip.comment_list_unapproved":true,"quip.comment_remove":true,"quip.comment_update":true,"quip.thread_list":true,"quip.thread_manage":true,"quip.thread_remove":true,"quip.thread_truncate":true,"quip.thread_view":true,"quip.thread_update":true}',
), '', true, true);

return $policies;