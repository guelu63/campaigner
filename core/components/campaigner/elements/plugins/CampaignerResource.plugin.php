<?php
/**
 * CampaignerResource plugin for campaigner extra
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
 

switch($modx->event->name) {
    default: return;

    // THE 'FOR THE REDIRECTION AFTER
    // A NEWSLETTER HAS BEEN CREATED'-QUEST PART 2
    // case 'OnManagerPageBeforeRender':
    //     if($_REQUEST['letter'] === 1) {
    //         $modx->sendRedirect($modx->getOption('manager_url') . 'index.php?a=82');
    //         return;
    //     }
    //     return;
    // break;
    // case 'OnBeforeDocFormSave':
    //     if(in_array($resource->get('parent'), array(12,13)))
    //         $_REQUEST['letter'] = 1;
    // break;

    // case 'OnManagerPageAfterRender':
    //     if($_REQUEST['letter'] && $_REQUEST['letter'] === 1) {
    //         $modx->log(MODX_LOG_LEVEL_ERROR, 'I will redirect');
    //         return $modx->sendRedirect($modx->getOption('manager_url') . 'index.php?a=82');
    //     }
    // break;

    // Create newsletter objects
    case 'OnDocFormSave':
        if(!$campaigner)
            $campaigner = $modx->getService('campaigner', 'Campaigner', $modx->getOption('core_path'). 'components/campaigner/model/campaigner/');
            if (!($campaigner instanceof Campaigner))
                return;
        if($mode !== 'new') return; // only implemented for new resources yet
        $parents = array($resource->get('parent'));

        if($modx->context->key !== $resource->get('context_key')) {
            $prevkey = $modx->context->key;
            $modx->switchContext($resource->get('context_key'));
        }
        $modx->log(modX::LOG_LEVEL_ERROR, "AUTO-FOLDER: " . $modx->getOption('campaigner.autonewsletter_folder'));
        if($modx->getOption('campaigner.autonewsletter_folder') == '' && $modx->getOption('campaigner.newsletter_folder') == '')
            return;
        
        if($modx->getOption('campaigner.newsletter_subfolders'))
            $parents = array_merge($parents, $modx->getParentIds($parents[0]));

        $modx->log(MODX_LOG_LEVEL_ERROR, "Parents: " . implode(',', $parents));
        $modx->log(MODX_LOG_LEVEL_ERROR, "Folder: ". $modx->getOption('campaigner.newsletter_folder'));
        $modx->log(MODX_LOG_LEVEL_ERROR, "Folder Auto: ". $modx->getOption('campaigner.autonewsletter_folder'));
        $modx->log(MODX_LOG_LEVEL_ERROR, 'Created On: ' . $resource->get('createdon'));

        if($modx->getOption('campaigner.autonewsletter_folder') !== '' && in_array($modx->getOption('campaigner.autonewsletter_folder'), $parents)) {
            $newsletter = $modx->newObject('Autonewsletter');
            $newsletter->fromArray(array(
                'docid'     => $resource->get('id'),
                'state'     => 0,
                'start'     => strtotime($resource->get('createdon')),
                'last'      => null,
                'frequency' => 86400,
                'time'      => '00:00:00',
                'total'     => 0,
            ));
            $newsletter->save();
        } elseif(!in_array($modx->getOption('campaigner.newsletter_folder'), $parents)) return;

        $newsletter = $modx->newObject('Newsletter');
        $newsletter->fromArray(array(
            'docid'   => $resource->get('id'),
            'total'   => 0,
            'sent'    => 0,
            'bounced' => 0
        ));
        if($newsletter->save())
            $modx->log(MODX_LOG_LEVEL_ERROR, "NL SAVED");

        $groups = explode(',', $modx->getOption('campaigner.default_groups'));
        if(count($groups) > 0) {
            foreach($groups as $gid) {
                $newsletterGroup = $modx->newObject('NewsletterGroup');
                $newsletterGroup->fromArray(array(
                    'newsletter' => $newsletter->get('id'),
                    'group'      => $gid
                ));
                $newsletterGroup->save();
            }
        }
        if($prevkey)
            $modx->switchContext($prevkey);
    break;
    
    // emptying the trash
    case 'OnBeforeEmptyTrash':
        if(!$campaigner)
            $campaigner = $modx->getService('campaigner', 'Campaigner', $modx->getOption('core_path'). 'components/campaigner/model/campaigner/');
            if (!($campaigner instanceof Campaigner))
                return;

        $newsletters = $modx->getCollection('Newsletter', array('docid:IN' => $ids));
        foreach($newsletters as $newsletter) {
            $newsletter->remove();
        }
        $newsletters = $modx->getCollection('Autonewsletter', array('docid:IN' => $ids));
        foreach($newsletters as $newsletter) {
            $newsletter->remove();
        }
    break;

    // case 'OnDocFormRender':
    //     if($_REQUEST['letter'] && (int) $_REQUEST['letter'] === 1) {
            
    // break;

    case 'OnDocFormRender':
        $campaignerTv = $resource->getTVValue('tvCampaignerData');
        $letter = $_REQUEST['letter'];
        if(!empty($campaignerTv))
            $isNl = 1;
        if((!$letter || $letter ==! 1) && !$isNl)
            return;
        
        $camp_url = $modx->getOption('manager_url') . 'index.php?a=82';
        $js =<<<JS
        <script type="text/javascript">
        Ext.onReady(function () {
            var modab = Ext.getCmp("modx-action-buttons");
            modab.add('-');
            modab.add({
                xtype: 'button'
                ,text: 'Zur Newsletter-Komponente'
                ,id: 'modx-abtn-to-campaigner'
                ,handler: function() {
                    MODx.loadPage(82)
                }
            });
            modab.doLayout();
        });
        </script>
JS;
        $modx->regClientStartupHTMLBlock($js);

        $js =<<<JS
        <script type="text/javascript">
            Ext.onReady(function () {
                var tp = Ext.getCmp('modx-leftbar-tabpanel');
                tp.activate('modx-resource-tree');

                var t = Ext.getCmp('modx-resource-tree');
                t.on('expandnode', function(n) {
                    var nl_ctx = t.getNodeById('web_0');
                    nl_ctx.expand();
                    var nl = 'web_13';
                    t.expandPath(nl_ctx.getPath());
                    var nl_par = t.getNodeById(nl);
                    t.expandPath(nl_par.getPath());
                });
            })
        </script>
JS;
        $modx->regClientStartupHTMLBlock($js);

        $parent = $modx->getObject('modResource', $modx->getOption('campaigner.newsletter_folder'));
        $props = array(
            'template'      => $modx->getOption('campaigner.default_template'),
            // 'parent-cmb'    => $parent->get('pagetitle') . ' (' . $parent->get('id') . ')',
            'published'     => 1,
            'pagetitle'     => 'Test',
            // 'parent'    => $parent->get('id'),
            // 'parentname' => $parent->get('pagetitle'),
            // 'parent_pagetitle' => $parent->get('pagetitle'),
            // 'content'   => 'test',
            );
        $resourceArray['pagetitle'] = 'test';
        $placeholders['pagetitle'] = 'test';
        $scriptProperties['pagetitle'] = 'test';
        // $resource->set('parent-cmb', $parent->get('pagetitle') . ' (' . $parent->get('id') . ')');
        if(!$modx->controller->setProperties($props))
            $modx->log(MODX_LOG_LEVEL_ERROR, 'Properties set: ' . json_encode($props));
    break;
}
return;