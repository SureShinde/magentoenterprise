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

class CLS_AdminEnhance_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getVideoWidth()
    {
        return 500;
    }

    public function getVideoHeight()
    {
        return 281;
    }

    /**
     * Get list of the user's collapsed banner locations from session
     *
     * @return array
     */
    public function getSessionCollapsedLocations()
    {
        $sessionCollapsedLocations = Mage::getSingleton('admin/session')->getCollapsedBannerLocations();
        if (!$sessionCollapsedLocations) {
            $sessionCollapsedLocations = array();
        }
        return $sessionCollapsedLocations;
    }

    /**
     * Update the array of user's collapsed banner locations on the session
     *
     * @param array $sessionCollapsedLocations
     */
    public function setSessionCollapsedLocations($sessionCollapsedLocations)
    {
        Mage::getSingleton('admin/session')->setCollapsedBannerLocations($sessionCollapsedLocations);
    }

    /**
     * Add banner location to be considered collapsed to the admin user session
     *
     * @param int $locationId
     */
    public function addSessionCollapsedLocation($locationId)
    {
        $sessionCollapsedLocations = $this->getSessionCollapsedLocations();
        $sessionCollapsedLocations[$locationId] = true;
        $this->setSessionCollapsedLocations($sessionCollapsedLocations);
    }

    /**
     * Remove banner location from the list of collapsed ones on the admin user session
     *
     * @param int $locationId
     */
    public function removeSessionCollapsedLocation($locationId)
    {
        $sessionCollapsedLocations = $this->getSessionCollapsedLocations();
        unset($sessionCollapsedLocations[$locationId]);
        $this->setSessionCollapsedLocations($sessionCollapsedLocations);
    }

    /**
     * Record user's expand/collapse of a banner location
     *
     * @param int $locationId
     * @param bool $collapse
     */
    public function toggleUserBannerLocation($locationId, $collapse)
    {
        if (!($user = Mage::getSingleton('admin/session')->getUser())) {
            return;
        }

        // Get any saved "collapsed" record for this user and banner location
        $collapsedRecords = Mage::getModel('cls_adminenhance/location_collapsed')->getCollection()
            ->addFieldToFilter('user_id', $user->getId())
            ->addFieldToFilter('location_id', $locationId);

        $collapsedRecord = null;
        if ($collapsedRecords->count()) {
            $collapsedRecord = $collapsedRecords->getFirstItem();
        } elseif ($collapse) {
            // If none existed before, we only need to bother to create a new one if we're collapsing (not expanding)
            $collapsedRecord = Mage::getModel('cls_adminenhance/location_collapsed')
                ->setUserId($user->getId())
                ->setLocationId($locationId);
        }

        if (!is_null($collapsedRecord)) {
            $collapsedRecord->setCollapsed(($collapse) ? 1 : 0)
                ->save();
        }

        // Update information on the session as well
        if ($collapse) {
            $this->addSessionCollapsedLocation($locationId);
        } else {
            $this->removeSessionCollapsedLocation($locationId);
        }
    }
}
