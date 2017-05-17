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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Shipestimate
 * User         Genevieve Eddison
 * Time         11:45
 * Date         25 June 2013
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

class Webshopapps_Shipestimate_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function displaySavings()
    {
        return Mage::getStoreConfigFlag('shipping/shipestimate/show_savings');
    }

    public function useCartInEstimate()
    {
        return Mage::getStoreConfig('shipping/shipestimate/use_cart');
    }

    public function getAdditionalDisplay($rates, $code)
    {
        $results = array();
        //Shipper HQ installed and active
        if(Mage::helper('wsacommon')->isModuleEnabled('Shipperhq_Shipper', 'carriers/shipper/active')) {
            //Check for one relevant module installed
           $required = Mage::helper('shipperhq_shipper')->isModuleEnabled('Shipperhq_Calendar');
            if($required) {
                $results['shipperhq_frontend/catalog_shipresults'] = 'shipperhq/catalog/shipresults.phtml';
            }
        }
        return count($results) > 0 ? $results : false;
    }
}