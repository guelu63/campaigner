<?php
/**
 * Resolver for campaigner extra
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
 * @package campaigner
 * @subpackage build
 */

/* @var $object xPDOObject */
/* @var $modx modX */

/* @var array $options */

if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('campaigner.core_path',null,$modx->getOption('core_path').'components/campaigner/').'model/';
            $modx->addPackage('campaigner',$modelPath, 'camp_');

            $manager = $modx->getManager();

            $manager->createObjectContainer('Autonewsletter');
            $manager->createObjectContainer('Newsletter');
            $manager->createObjectContainer('Subscriber');
            $manager->createObjectContainer('Group');
            $manager->createObjectContainer('GroupSubscriber');
            $manager->createObjectContainer('NewsletterGroup');
            $manager->createObjectContainer('AutonewsletterGroup');
            $manager->createObjectContainer('Queue');
            $manager->createObjectContainer('Bounce');
            $manager->createObjectContainer('ResendCheck');
            $manager->createObjectContainer('Bounces');
            $manager->createObjectContainer('SubscriberHits');
            $manager->createObjectContainer('NewsletterLink');
            $manager->createObjectContainer('SocialSharing');
            $manager->createObjectContainer('Fields');
            $manager->createObjectContainer('SubscriberFields');
            $manager->createObjectContainer('Unsubscriber');
            
            break;
        case xPDOTransport::ACTION_UPGRADE:
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            // $modx =& $object->xpdo;
            // $modelPath = $modx->getOption('campaigner.core_path',null,$modx->getOption('core_path').'components/campaigner/').'model/';
            // $modx->addPackage('campaigner',$modelPath, 'camp_');

            // $manager = $modx->getManager();

            // $manager->removeObjectContainer('Autonewsletter');
            // $manager->removeObjectContainer('Newsletter');
            // $manager->removeObjectContainer('Subscriber');
            // $manager->removeObjectContainer('Group');
            // $manager->removeObjectContainer('GroupSubscriber');
            // $manager->removeObjectContainer('NewsletterGroup');
            // $manager->removeObjectContainer('AutonewsletterGroup');
            // $manager->removeObjectContainer('Queue');
            // $manager->removeObjectContainer('Bounce');
            // $manager->removeObjectContainer('ResendCheck');
            // $manager->removeObjectContainer('Bounces');
            // $manager->removeObjectContainer('SubscriberHits');
            // $manager->removeObjectContainer('NewsletterLink');
            // $manager->removeObjectContainer('SocialSharing');
            // $manager->removeObjectContainer('Fields');
            // $manager->removeObjectContainer('SubscriberFields');
            // $manager->removeObjectContainer('Unsubscriber');

            break;
    }
}
return true;