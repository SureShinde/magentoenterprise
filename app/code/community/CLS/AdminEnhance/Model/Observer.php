<?php
/**
 * Nucleus
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Nucleus License
 * that is bundled with this package in the file LICENSE_NUCLEUS.txt.
 * It is also available through the World Wide Web at this URL:
 * http://www.nucleuscommerce.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the World Wide Web, please send an email
 * to support@nucleuscommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Nucleus
 * to newer versions in the future.
 *
 * @category   CLS
 * @package    AdminEnhance
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

class CLS_AdminEnhance_Model_Observer
{
    /**
     * When an admin user logs in, set their list of "collapsed" banner locations
     *
     * @param Varien_Event_Observer $observer
     */
    public function initAdminUserCollapsedBanners(Varien_Event_Observer $observer)
    {
        if ($user = Mage::getSingleton('admin/session')->getUser()) {
            // Get all records of locations that are currently collapsed for this user
            $collapsedBannerLocations = Mage::getModel('cls_adminenhance/location_collapsed')->getCollection()
                ->addFieldToFilter('user_id', $user->getId())
                ->addFieldToFilter('collapsed', 1);

            if ($collapsedBannerLocations->count() > 0) {
                // Form array indexed by ID and set on the session
                $locationIds = array();
                foreach ($collapsedBannerLocations as $collapsedBannerLocation) {
                    $locationIds[$collapsedBannerLocation->getLocationId()] = true;
                }
                Mage::helper('cls_adminenhance')->setSessionCollapsedLocations($locationIds);
            }
        }
    }

    /**
     * Add block with list of all existing videos to the CLS system info block
     *
     * @param Varien_Event_Observer $observer
     */
    public function addVideosToSystemInfoBlock(Varien_Event_Observer $observer)
    {
        $parentBlock = $observer->getBlock();
        $block = Mage::app()->getLayout()->createBlock('cls_adminenhance/adminhtml_support_videos')
            ->setIncludeWrapper(true);

        $parentBlock->setChild('cls_system_info_videos', $block);
    }
}