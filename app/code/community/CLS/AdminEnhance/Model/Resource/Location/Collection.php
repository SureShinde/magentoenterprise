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

class CLS_AdminEnhance_Model_Resource_Location_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_doAddBanners = false;

    protected $_urlFilterApplied = false;
    protected $_urlFilterRemainder = null;

    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('cls_adminenhance/location');
    }

    /**
     * Filter the collection on a URL path (route/controller/action)
     *
     * @param string $urlPath
     * @return CLS_AdminEnhance_Model_Resource_Location_Collection
     */
    public function addUrlPathFilter($urlPath)
    {
        $urlPath = trim($urlPath, '/');
        $this->addFieldToFilter('url_path', $urlPath);

        return $this;
    }

    /**
     * Filter the collection on an entire URL
     *
     * @param string $url
     * @return CLS_AdminEnhance_Model_Resource_Location_Collection
     */
    public function addUrlFilter($url)
    {
        // Because of filtering on the URL "remainder", can only apply this filter once
        if ($this->_urlFilterApplied) {
            return $this;
        }

        // Determine route/controller/action path and remainder
        $url = preg_replace('#^(https?://)?([^/]+\.[^/]+)?(/index\.php)?/+#', '', $url);
        $urlParts = explode('/', $url, 4);

        $urlRemainder = null;
        if (isset($urlParts[3])) {
            $urlRemainder = $urlParts[3];
            unset($urlParts[3]);
        }
        $urlPath = implode('/', $urlParts);

        // Set remainder for later, filter the actual SQL query on route/controller/action
        $this->_urlFilterRemainder = $urlRemainder;
        $this->addUrlPathFilter($urlPath);

        $this->_urlFilterApplied = true;

        return $this;
    }

    /**
     * After loading, filter any results by whether they match the URL "remainder"
     *
     * @return CLS_AdminEnhance_Model_Resource_Location_Collection
     */
    protected function _applyUrlRemainderFilter()
    {
        if (!is_null($this->_urlFilterRemainder)) {
            foreach ($this->getItems() as $item) {
                // Remove from the collection if the URL remainder we're matching DOES match the "negative" pattern or DOESN'T match the regular one
                if ((($pattern = $item->getUrlRemainderPattern()) && !preg_match('#'.$pattern.'#', $this->_urlFilterRemainder))
                    || (($negPattern = $item->getUrlRemainderNegativePattern()) && preg_match('#'.$negPattern.'#', $this->_urlFilterRemainder))) {
                    $this->removeItemByKey($item->getId());
                }
            }
        }

        return $this;
    }

    /**
     * Add banners associated with all locations
     *
     * @return CLS_AdminEnhance_Model_Resource_Location_Collection
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
     * Add banners to all locations
     *
     * @return CLS_AdminEnhance_Model_Resource_Location_Collection
     */
    protected function _addBanners()
    {
        // Get all banner IDs associated with the locations
        $bannerIds = array();
        foreach ($this->getItems() as $item) {
            $bannerIds[] = $item->getBannerId();
        }
        $bannerIds = array_unique($bannerIds);

        // Load filtered collection
        $banners = Mage::getModel('cls_adminenhance/banner')->getCollection()
            ->addFieldToFilter('banner_id', array('in'=>$bannerIds))
            ->addVideos();

        // Loop through locations and add banners
        foreach ($this->getItems() as $item) {
            if ($banner = $banners->getItemById($item->getBannerId())) {
                $item->setBanner($banner);
            }
        }

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

        $this->_applyUrlRemainderFilter();

        if ($this->_doAddBanners) {
            $this->_addBanners();
        }

        return $this;
    }
}
