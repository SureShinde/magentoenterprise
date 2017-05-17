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

class CLS_AdminEnhance_Model_Resource_Video_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_doAddBanners = false;

    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('cls_adminenhance/video');
    }

    /**
     * Filter the collection by a certain banner
     *
     * @param int|array|CLS_AdminEnhance_Model_Banner $bannerIds
     * @return CLS_AdminEnhance_Model_Resource_Video_Collection
     */
    public function addBannerFilter($bannerIds)
    {
        if ($bannerIds instanceof CLS_AdminEnhance_Model_Banner) {
            $bannerIds = $bannerIds->getId();
        }
        if (!is_array($bannerIds)) {
            $bannerIds = array($bannerIds);
        }

        // Join to the link table and filter on the right banners
        $this->getSelect()->join(
            array('banner_link' => $this->getTable('cls_adminenhance/banner_video_link')),
            'main_table.video_id = banner_link.video_id',
            array('banner_id')
        )->where('banner_link.banner_id IN (?)', $bannerIds);

        return $this;
    }

    /**
     * Add related banners to all videos
     *
     * @return CLS_AdminEnhance_Model_Resource_Video_Collection
     */
    public function addBanners()
    {
        // Determine if we need to add them now or on load
        if ($this->isLoaded()) {
            $this->_addBanners();
        } else {
            $this->_doAddBanners = true;
        }
        return $this;
    }

    /**
     * Do the adding of banners
     *
     * @return CLS_AdminEnhance_Model_Resource_Video_Collection
     */
    protected function _addBanners()
    {
        // Load banners filtered by videos
        $videoIds = array_keys($this->_items);
        $banners = Mage::getModel('cls_adminenhance/banner')->getCollection()
            ->addVideoFilter($videoIds);

        // Rearrange grouped by video
        $bannersByVideoId = array();
        foreach ($banners as $banner) {
            if ($bannerVideoIds = $banner->getVideoIds()) {
                foreach ($bannerVideoIds as $videoId) {
                    if (!isset($bannersByVideoId[$videoId])) {
                        $bannersByVideoId[$videoId] = new Varien_Data_Collection();
                    }
                    $bannersByVideoId[$videoId]->addItem($banner);
                }
            }
        }

        // Loop through videos and set banners collection
        foreach ($bannersByVideoId as $videoId => $collection) {
            $this->getItemById($videoId)->setBanners($collection);
        }

        return $this;
    }

    /**
     * Redeclare before load method for adding event
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _beforeLoad()
    {
        parent::_beforeLoad();

        if (empty($this->_orders)) {
            $this->setOrder('position', self::SORT_ORDER_ASC);
        }
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
                if (isset($this->_items[$row['video_id']])) {   // Get existing item in the collection if it exists
                    $item = $this->_items[$row['video_id']];
                } else {
                    $item = $this->getNewEmptyItem();
                    if ($this->getIdFieldName()) {
                        $item->setIdFieldName($this->getIdFieldName());
                    }
                    $item->addData($row);
                    $item->unsetData('banner_id');      // Unset the banner_id field, as it is not unique to this record
                    $this->addItem($item);
                }
                // This section can only occur if videos were joined to banners (many-to-many)
                if (isset($row['banner_id'])) {
                    // Accumulate an array of banner IDs in the filter that match this video
                    $bannerIds = ($item->hasData('banner_ids')) ? $item->getBannerIds() : array();
                    $bannerIds[] = $row['banner_id'];
                    $item->setBannerIds($bannerIds);
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
        if ($this->_doAddBanners) {
            $this->_addBanners();
        }
        return $this;
    }
}
