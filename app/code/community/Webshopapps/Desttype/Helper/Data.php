<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Shipping
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/*
 * Webshopapps Residential Delivery Extension
 *
 * @author 	Webshopapps
 * @license www.webshopapps.com/license/license.txt
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * (c) Webshopapps.com Zowta Ltd 2010 - All rights reserved.
 */

/**
 * Shipping data helper
 */
class Webshopapps_Desttype_Helper_Data extends Mage_Core_Helper_Abstract
{

    private $_priceThreshold;
    private static $_debug;
    private static $_extensionEnabled;

    public static function extensionEnabled()
    {

        if (self::$_extensionEnabled == null) {
            self::$_extensionEnabled = Mage::getStoreConfig('shipping/desttype/active');
        }

        return self::$_extensionEnabled;
    }

    public function isFreightEnabled()
    {
        if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Wsafreightcommon', 'shipping/wsafreightcommon/active')) {
            if (Mage::helper('wsafreightcommon')->isActive()) {
                $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
                $hideFreight = Mage::helper('wsafreightcommon')->dontShowCommonFreight($items);

                if (!$hideFreight) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getDestTypeHtmlSelect($layout, $destType, $type = '')
    {
        if (!Mage::getStoreConfig('shipping/desttype/active')) {
            return null;
        }

        $options = Mage::getModel('desttype/shipping_source_adminAddressType')->toOptionArray();

        return $this->getBasicDestTypeHtmlSelect($layout, $destType, $options, $type);
    }

    /**
     * Also called from shipping estimator where just want the dropdown, no frills);
     * Enter description here ...
     *
     * @param string $layout
     * @param string $destType
     * @param array  $options
     * @param string $type
     * @return \Mage_Core_Block_Html_Select
     */
    public function getBasicDestTypeHtmlSelect($layout, $destType, $options = array(), $type = '')
    {

        if (count($options) < 1) {
            $options = Mage::getModel('desttype/shipping_source_adminAddressType')->toOptionArray();
        }

        if ($type == 'productview') {
            $name = 'estimate[dest_type]';
            $id = 'ship_dest_type';
        } elseif ($type != '') {
            $name = $type . '[dest_type]';
            $id = $type . ':dest_type';
        } else {
            $name = 'dest_type';
            $id = 'dest_type';
        }

        if (!$destType && Mage::getStoreConfigFlag('shipping/desttype/default_address')) {
            $destType = 'COM';
        }

        $html = $layout->createBlock('core/html_select')
            ->setName($name)
            ->setTitle(Mage::helper('desttype')->__('Address Type'))
            ->setId($id)
            ->setClass('required-entry')
            ->setValue($destType)
            ->setOptions($options)
            ->getHtml();

        return $html;
    }

    public function checkDefaultAddress($addressType)
    {
        if (Mage::getStoreConfigFlag('shipping/desttype/default_address') && !$addressType) {
            if ($addressType == 'RES') {
                $addressType = 'COM';
            } elseif ($addressType == 'COM') {
                $addressType = 'RES';
            }
        }

        return $addressType;
    }

    public function isDestTypeActive($baseSubtotalWithDiscount, $customerGroupId)
    {

        if ($baseSubtotalWithDiscount > $this->getPriceThreshold()) {
            if ($customerGroupId != "") {
                if (self::isDebug()) {
                    Mage::helper('wsalogger/log')->postDebug('desttype', 'Customer Group', $customerGroupId);
                }
                if (!in_array($customerGroupId, explode(',', Mage::getStoreConfig('shipping/desttype/group')))) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    private function getPriceThreshold()
    {
        if (null == $this->_priceThreshold) {
            $this->_priceThreshold = Mage::getStoreConfig('shipping/desttype/min_order_value');
            if (!is_numeric($this->_priceThreshold)) {
                $this->_priceThreshold = 1000000;
                if (self::isDebug()) {
                    Mage::helper('wsalogger/log')->postDebug('desttype', 'Price Threshold', $this->_priceThreshold);
                }
            }
        }

        return $this->_priceThreshold;
    }

    public static function isDebug()
    {
        if (self::$_debug == null) {
            self::$_debug = Mage::helper('wsalogger')->isDebug('Webshopapps_Desttype');
        }

        return self::$_debug;
    }

    public function getTemplate()
    {
        if (Mage::helper('wsacommon')->getNewVersion() > 13) {
            return 'webshopapps/desttype/checkout/cart/shipping19.phtml';
        }

        return 'webshopapps/desttype/checkout/cart/shipping.phtml';
    }

    public function getHtmlSelectCart($layout, $defValue=null)
    {
        if (is_null($defValue)) {
            $defValue = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getDestType();
            $defValue = self::checkDefaultAddress($defValue);
        }

        return self::getBasicDestTypeHtmlSelect($layout, $defValue);
    }

    public function getHtmlSelectProductView($layout, $defValue=null)
    {
        if (is_null($defValue)) {
            $defValue = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getDestType();
            $defValue = self::checkDefaultAddress($defValue);
        }

        $options = array();

        $type = 'productview';

        return self::getBasicDestTypeHtmlSelect($layout, $defValue, $options, $type);
    }
}