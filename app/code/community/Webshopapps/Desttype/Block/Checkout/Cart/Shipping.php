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
 * @package    Mage_Checkout
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
class Webshopapps_Desttype_Block_Checkout_Cart_Shipping extends Mage_Checkout_Block_Cart_Shipping
{
	
    public function getDestTypeHtmlSelect($defValue=null) {

        if (is_null($defValue)) {
            $defValue = $this->getAddress()->getDestType();
            $defValue = Mage::helper('desttype')->checkDefaultAddress($defValue);
        }

        return Mage::helper('desttype')->getBasicDestTypeHtmlSelect($this->getLayout(),$defValue);
    		
    }
    
}