<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Amp
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

class Plumrocket_Amp_Helper_Data extends Plumrocket_Amp_Helper_Main
{
    /**
     * Default value for homepage alias
     */
    const AMP_HOME_PAGE_KEYWORD = 'amphomepage';
    const AMP_FOOTER_LINKS_KEYWORD = 'footer_links_amp';

    protected $_allowedPages;

    /**
     * Retrieve allowed full action names
     * @param  int $store
     * @return array
     */
    public function getAllowedPages($store = null)
    {
        if ($this->_allowedPages === null) {
            $this->_allowedPages = explode(',', Mage::getStoreConfig('pramp/general/pages', $store));
            $this->_allowedPages[] = 'turpentine_esi_getBlock';
        }
        return $this->_allowedPages;
    }

    public function isEsiRequest()
    {
        $_request = Mage::app()->getRequest();
        return implode('_', array($_request->getModuleName(), $_request->getControllerName(), $_request->getActionName())) == 'turpentine_esi_getBlock';
    }

    /**
     * @return bool
     * Check magento configuration
     */
    public function moduleEnabled($store = null)
    {
        return (bool)Mage::getStoreConfig('pramp/general/enable', $store);
    }

    /**
     * @return bool
     * Return true if module enabled and exist request param amp
     */
    public function isAmpRequest()
    {
        return $this->moduleEnabled()
            && (Mage::app()->getRequest()->getParam('amp') == 1) ? true : false;
    }

    /**
     * @return string
     * url string path
     */
    public function getCurrentPath()
    {
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        return  Mage::getSingleton('core/url')->parseUrl($currentUrl)->getPath();
    }

    /**
     * @return string
     * url string without amp parameter
     */
    public function getCanonicalUrl($url = null, $params = null)
    {
        $url = $url ? $url : Mage::helper('core/url')->getCurrentUrl();
        $urlData = parse_url($url);
        $dataQuery = isset($urlData['query']) ? explode('&', $urlData['query']) : array();

        if (is_array($params) && count($params)) {
            $tempData = array();
            foreach($params as $key => $value) {
                $tempData[] = $key . '=' . $value;
            }
            $dataQuery = array_merge($dataQuery, $tempData);
        }

        foreach ($dataQuery as $key => $value) {
            if (strtolower($value) == 'amp=1') {
                unset($dataQuery[$key]);
            }
        }

        $url = $urlData['scheme'] . '://' . $urlData['host'] . $urlData['path'];
        if (count($dataQuery)) {
            $url .= '?' . implode('&', $dataQuery);
        }

        return $url;
    }

    /**
     * @return null
     * Set dafault values for module
     */
    public function disableExtension()
    {
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');
        $connection->delete($resource->getTableName('core/config_data'), array($connection->quoteInto('path IN (?)', array('pramp/general/enable'))));
        $config = Mage::getConfig();
        $config->reinit();
        Mage::app()->reinitStores();
    }

    /**
     * @return string
     * url string with amp parameter
     */
    public function getAmpUrl($url = null, $params = null)
    {
        $url = $url ? $url : Mage::helper('core/url')->getCurrentUrl();
        $urlData = parse_url($url);
        $dataQuery = isset($urlData['query']) ? explode('&', $urlData['query']) : array();

        if (is_array($params) && count($params)) {
            $tempData = array();
            foreach($params as $key => $value) {
                $tempData[] = $key . '=' . $value;
            }
            $dataQuery = array_merge($dataQuery, $tempData);
        }

        if (!in_array('amp=1', $dataQuery)) {
            $dataQuery[] = 'amp=1';
        }

        return $urlData['scheme'] . '://' . $urlData['host'] . $urlData['path'] . '?' . implode('&', $dataQuery);
    }

    /**
     * @var object Mage_Catalog_Model_Product
     * @return string add to cart url
     */
    public function getAddToCartUrl($product)
    {
        return $this->getCanonicalUrl(Mage::getUrl('pramp/cart/add', array('product'=>$product->getId())));
    }

}