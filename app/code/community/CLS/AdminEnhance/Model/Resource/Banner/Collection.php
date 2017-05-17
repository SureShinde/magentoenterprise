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

class CLS_AdminEnhance_Model_Resource_Banner_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_doAddLocations = false;
    protected $_doAddVideos = false;

    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('cls_adminenhance/banner');
    }

    /**
     * Filter the collection by a certain video
     *
     * @param int|array|CLS_AdminEnhance_Model_Video $videoIds
     * @return CLS_AdminEnhance_Model_Resource_Banner_Collection
     */
    public function addVideoFilter($videoIds)
    {
        if ($videoIds instanceof CLS_AdminEnhance_Model_Video) {
            $videoIds = $videoIds->getId();
        }
        if (!is_array($videoIds)) {
            $videoIds = array($videoIds);
        }

        // Join to the link table and filter on the right videos
        $this->getSelect()->join(
            array('video_link' => $this->getTable('cls_adminenhance/banner_video_link')),
            'main_table.banner_id = video_link.banner_id',
            array('video_id')
        )->where('video_link.video_id IN (?)', $videoIds);

        return $this;
    }

    /**
     * Add related locations to all banners
     *
     * @return CLS_AdminEnhance_Model_Resource_Banner_Collection
     */
    public function addLocations()
    {
        // Determine if we need to add them right away or on load
        if ($this->isLoaded()) {
            $this->_addLocations();
        } else {
            $this->_doAddLocations = true;
        }
        return $this;
    }

    /**
     * Add related videos to all banners
     *
     * @return CLS_AdminEnhance_Model_Resource_Banner_Collection
     */
    public function addVideos()
    {
        // Determine if we need to add them right away or on load
        if ($this->isLoaded()) {
            $this->_addVideos();
        } else {
            $this->_doAddVideos = true;
        }
        return $this;
    }

    /**
     * Do the adding of locations
     *
     * @return CLS_AdminEnhance_Model_Resource_Banner_Collection
     */
    protected function _addLocations()
    {
        // Load locations filtered by banners
        $bannerIds = array_keys($this->_items);
        $locations = Mage::getModel('cls_adminenhance/location')->getCollection()
            ->addFieldToFilter('banner_id', array('in', $bannerIds));

        // Rearrange grouped by banner
        $locationsByBannerId = array();
        foreach ($locations as $location) {
            $bannerId = $location->getBannerId();
            // Set the appropriate banner on the location model
            $location->setBanner($this->getItemById($bannerId));

            // Accumulate all locations for the banner
            if (!isset($locationsByBannerId[$bannerId])) {
                $locationsByBannerId[$bannerId] = new Varien_Data_Collection();
            }
            $locationsByBannerId[$bannerId]->addItem($location);
        }

        // Loop through banners and set locations collection
        foreach ($locationsByBannerId as $bannerId => $collection) {
            $this->getItemById($bannerId)->setLocations($collection);
        }

        return $this;
    }

    /**
     * Do the adding of videos
     *
     * @return CLS_AdminEnhance_Model_Resource_Banner_Collection
     */
    protected function _addVideos()
    {
        // Load videos filtered by banners
        $bannerIds = array_keys($this->_items);
        $videos = Mage::getModel('cls_adminenhance/video')->getCollection()
            ->addBannerFilter($bannerIds);

        // Rearrange grouped by banner
        $videosByBannerId = array();
        foreach ($videos as $video) {
            if ($videoBannerIds = $video->getBannerIds()) {
                foreach ($videoBannerIds as $bannerId) {
                    if (!isset($videosByBannerId[$bannerId])) {
                        $videosByBannerId[$bannerId] = new Varien_Data_Collection();
                    }
                    $videosByBannerId[$bannerId]->addItem($video);
                }
            }
        }

        // Loop through banners and set videos collection
        foreach ($videosByBannerId as $bannerId => $collection) {
            $this->getItemById($bannerId)->setVideos($collection);
        }

        return $this;
    }

    /**
     * Load data
     *
     * @param   bool $printQuery
     * @param   bool $logQuery
     *
     * @return  Varien_Data_Collection_Db
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        $this->_beforeLoad();

        $this->_renderFilters()
            ->_renderOrders()
            ->_renderLimit();

        $this->printLogQuery($printQuery, $logQuery);
        $data = $this->getData();
        $this->resetData();

        if (is_array($data)) {
            foreach ($data as $row) {
                if (isset($this->_items[$row['banner_id']])) {  // Get existing item in the collection if it exists
                    $item = $this->_items[$row['banner_id']];
                } else {
                    $item = $this->getNewEmptyItem();
                    if ($this->getIdFieldName()) {
                        $item->setIdFieldName($this->getIdFieldName());
                    }
                    $item->addData($row);
                    $item->unsetData('video_id');   // Unset the video_id field, as it is not unique to the record
                    $this->addItem($item);
                }
                // This section can occur if banners were joined to videos (many-to-many)
                if (isset($row['video_id'])) {
                    // Accumulate an array of video IDs in the filter that match this banner
                    $videoIds = ($item->hasData('video_ids')) ? $item->getVideoIds() : array();
                    $videoIds[] = $row['video_id'];
                    $item->setVideoIds($videoIds);
                }
            }
        }

        $this->_setIsLoaded();
        $this->_afterLoad();
        return $this;
    }

    /**
     * Redeclare after load method for specifying collection items original data
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->_doAddLocations) {
            $this->_addLocations();
        }
        if ($this->_doAddVideos) {
            $this->_addVideos();
        }
        return $this;
    }
}
