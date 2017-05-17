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
 * @package    NucleusCore
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */
class CLS_NucleusCore_Helper_Elements extends Mage_Core_Helper_Abstract
{
    const CONFIG_PATH_NUCLEUS_ELEMENTS_VERSION = 'nucleus_elements/general/version';
    const START_PAGE_KEY = 'nucleus-elements';

    protected $_elementsVersionConfig = null;
    protected $_currentElementsVersion = null;

    /**
     * Get the config data model with the current version
     *
     * @return Mage_Core_Model_Config_Data|bool
     */
    protected function _getElementsVersionConfig()
    {
        if (is_null($this->_elementsVersionConfig)) {
            // Explicitly get from database, to avoid caching
            $values = Mage::getModel('core/config_data')->getCollection()
                ->addFieldToFilter('scope', 'default')
                ->addFieldToFilter('scope_id', 0)
                ->addFieldToFilter('path', self::CONFIG_PATH_NUCLEUS_ELEMENTS_VERSION);
            if ($values->count() > 0) {
                $this->_elementsVersionConfig = $values->getFirstItem();
            } else {
                $this->_elementsVersionConfig = false;
            }
        }
        return $this->_elementsVersionConfig;
    }

    /**
     * Get the version of Nucleus Elements currently installed
     *
     * @return string
     */
    public function getCurrentElementsVersion()
    {
        if (is_null($this->_currentElementsVersion)) {
            $versionConfig = $this->_getElementsVersionConfig();
            $this->_currentElementsVersion = ($versionConfig instanceof Mage_Core_Model_Config_Data) ? $versionConfig->getValue() : '';
        }
        return $this->_currentElementsVersion;
    }

    /**
     * Update the stored Elements version to a new value
     *
     * @param string $version
     */
    public function updateElementsVersion($version)
    {
        $versionConfig = $this->_getElementsVersionConfig();
        if (!($versionConfig instanceof Mage_Core_Model_Config_Data)) {
            $versionConfig = Mage::getModel('core/config_data')->setData(array(
                'scope' => 'default',
                'scope_id' => 0,
                'path' => self::CONFIG_PATH_NUCLEUS_ELEMENTS_VERSION,
            ));
        }

        $versionConfig->setValue($version)
            ->save();
    }

    /**
     * Remove the stored Elements version
     */
    public function unsetElementsVersion()
    {
        $versionConfig = $this->_getElementsVersionConfig();
        if ($versionConfig instanceof Mage_Core_Model_Config_Data) {
            $versionConfig->delete();
        }
    }

    /**
     * Determine if Nucleus Elements is installed
     *
     * @return bool
     */
    public function elementsInstalled()
    {
        $version = $this->getCurrentElementsVersion();
        return (!empty($version));
    }

    /**
     * Get the URL of the Nucleus Elements start page
     *
     * @return string
     */
    public function getStartPageUrl()
    {
        $page = Mage::getModel('cms/page')->load(self::START_PAGE_KEY, 'identifier');
        if (!$page->getId()) {
            return '';
        }

        $stores = $page->getStoreId();
        sort($stores);
        $firstStoreId = 0;
        $firstStoreCode = '';
        foreach ($stores as $storeId) {
            if ((int) $storeId > 0) {
                $firstStoreId = $storeId;
                $store = Mage::app()->getStore($firstStoreId);
                $firstStoreCode = $store->getCode();
                break;
            }
        }

        if ($firstStoreId == 0) {
            $stores = Mage::app()->getStores(false, true);
            $firstStoreId = current($stores)->getId();
            $firstStoreCode = key($stores);
        }

        $urlModel = Mage::getModel('core/url')->setStore($firstStoreId);
        $url = $urlModel->getUrl(
            $page->getIdentifier(), array(
                '_current' => false,
                '_query' => '___store='.$firstStoreCode
            )
        );
        return $url;
    }
}