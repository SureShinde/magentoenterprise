<?php
/**
 * WebShopApps.com
 *
[[[WEBSHOPAPPS_COPYRIGHT_TEXT]]]
 *
 * @category   WebShopApps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @package    WebShopApps_Invoicing
[[[WEBSHOPAPPS_COPYRIGHT]]]
 */
 
class Webshopapps_Desttype_Model_Shipping_Source_Customergroup  {
	
    public function toOptionArray()  {
    	
    	$groups = Mage::getResourceModel('customer/group_collection')
          //  ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionArray();
            
        return $groups;
    }
}