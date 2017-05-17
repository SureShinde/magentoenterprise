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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Shipestimate
 * User         Genevieve Eddison
 * Date         25 June 2013
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

class Webshopapps_Shipestimate_Block_Catalog_Shipestimate extends Mage_Catalog_Block_Product_Abstract
{

    protected $_customerAddress;

    public function isEnabled()
    {
        if (!Mage::getStoreConfigFlag('shipping/shipestimate/active') || $this->getProduct()->isVirtual()) {
            return false;
        }
        return true;
    }

    public function showField($fieldName)
    {
        $show = true;
        switch ($fieldName) {
            case 'country':
                $show = Mage::getStoreConfigFlag('shipping/shipestimate/show_country');
                break;
            case 'region':
                $show = Mage::getStoreConfigFlag('shipping/shipestimate/show_region');
                break;
            case 'postcode':
                $show = true;
                break;
            case 'city' :
                $show = Mage::getStoreConfigFlag('shipping/shipestimate/show_city');
                break;
            case 'qty' :
                $show = true;
                break;
            case 'desttype' :
                $show = Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Desttype', 'shipping/desttype/active') && Mage::getStoreConfigFlag('shipping/shipestimate/show_desttype');
                break;
        }
        return $show;
    }

    public function getField($fieldName)
    {
        if ($customerAddress = $this->getCustomerAddress()) {
            switch ($fieldName) {
                case 'country':
                    return $customerAddress->getCountryId();
                case 'region':
                    return $customerAddress->getRegionId();
                case 'postcode':
                    return $customerAddress->getPostcode();
                case 'city' :
                    return $customerAddress->getCity();
                case 'desttype' :
                    return $customerAddress->getDesttype();
            }
        }
        return null;

    }

    public function getDefaultCountry()
    {
        return Mage::getStoreConfig('shipping/origin/country_id');
    }

    public function getProductSku()
    {
        return $this->getProduct()->getSku();
    }

    public function isFieldRequired($fieldName)
    {
        $isRequired = false;
        switch ($fieldName) {
            case 'country':
            case 'postcode':
            case 'qty':
                $isRequired = true;
                break;
        }
        return $isRequired;
    }

    public function getEstimateUrl()
    {
        return $this->getUrl('shipestimate/estimate/estimate', array('_current' => true));
    }

    protected function getCustomerAddress()
    {
        if (!$this->_customerAddress) {
            if (Mage::getSingleton('customer/session')->getCustomer()->getPrimaryShippingAddress()) {
                $this->_customerAddress = Mage::getSingleton('customer/session')->getCustomer()->getPrimaryShippingAddress();
            }
        }
        return $this->_customerAddress;
    }

    public function getEstimateLabel()
    {
        if (Mage::getStoreConfig('shipping/shipestimate/estimate_text') != '') {
            return Mage::getStoreConfig('shipping/shipestimate/estimate_text');
        }
        return Mage::helper('shipestimate')->__('Estimate Shipping Cost');
    }

    public function getEstimateDescription()
    {
        if (Mage::getStoreConfig('shipping/shipestimate/estimate_desc') != '') {
            return Mage::getStoreConfig('shipping/shipestimate/estimate_desc');
        }
        return false;
    }

}
