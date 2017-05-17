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

class CLS_AdminEnhance_Model_Banner extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('cls_adminenhance/banner');
    }

    /**
     * Get the related videos
     *
     * @return CLS_AdminEnhance_Model_Resource_Video_Collection
     */
    public function getVideos()
    {
        if (!$this->hasData('videos')) {
            $videos = Mage::getModel('cls_adminenhance/video')->getCollection()
                ->addBannerFilter($this);
            $this->setData('videos', $videos);
        }
        return $this->getData('videos');
    }

    /**
     * Add a video to the collection
     * Mainly useful for initial saving of banner/video links
     *
     * @param CLS_AdminEnhance_Model_Video $video
     * @return CLS_AdminEnhance_Model_Banner
     */
    public function addVideo($video)
    {
        if (is_array($video)) {
            $video = Mage::getModel('cls_adminenhance/video')
                ->addData($video);
        }
        $this->getVideos()->addItem($video);
        return $this;
    }

    /**
     * Remove a video from the collection
     * Mainly useful for deleting the banner/video link
     *
     * @param int|CLS_AdminEnhance_Model_Video $videoId
     * @return CLS_AdminEnhance_Model_Banner
     */
    public function removeVideo($videoId) {
        if ($videoId instanceof CLS_AdminEnhance_Model_Video) {
            $videoId = $videoId->getId();
        }
        $this->getVideos()->load()->removeItemByKey($videoId);
        return $this;
    }

    /**
     * Get the related locations
     *
     * @return CLS_AdminEnhance_Model_Resource_Location_Collection
     */
    public function getLocations()
    {
        if (!$this->hasData('locations')) {
            $locations = Mage::getModel('cls_adminenhance/location')->getCollection()
                ->addFieldToFilter('banner_id', $this->getId());
            foreach ($locations as $location) {
                $location->setBanner($this);
            }
            $this->setData('locations', $locations);
        }
        return $this->getData('locations');
    }

    /**
     * Add a location to the collection
     * Mainly useful for initial saving of locations
     *
     * @param CLS_AdminEnhance_Model_Location $location
     * @return CLS_AdminEnhance_Model_Banner
     */
    public function addLocation($location)
    {
        if (($location instanceof CLS_AdminEnhance_Model_Location) && $location->getId() && $location->getBannerId() != $this->getId()) {
            Mage::throwException(Mage::helper('cls_adminenhance')->__('Cannot add location belonging to a different banner'));
        }

        if (is_array($location)) {
            $location = Mage::getModel('cls_adminenhance/location')
                ->addData($location);
        }
        if (!$location->getBannerId()) {
            $location->setBanner($this);
        }
        $this->getLocations()->addItem($location);
        return $this;
    }

    /**
     * Processing object after save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        foreach ($this->getLocations() as $location) {
            if (!$location->getBannerId()) {
                $location->setBannerId($this->getId());
            }
            $location->save();
        }

        foreach ($this->getVideos() as $video) {
            $video->save();
        }

        $this->getResource()->saveVideoLinks($this);

        return $this;
    }
}
