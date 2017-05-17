<?php

/**
 * Magento Webshopapps Module
 *
 * @category   Webshopapps
 * @package    Webshopapps Wsavalidation
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    www.webshopapps.com/license/license.txt
 * @author     Karen Baker <sales@webshopapps.com>
 */

class Webshopapps_Desttype_Model_Observer extends Mage_Core_Model_Abstract
{

	/**
	 * Event controller_action_predispatch
	 * Enter description here ...
	 * @param unknown_type $observer
	 */
  	public function hookToControllerActionPreDispatch($observer)
    {
    	$actionName = $observer->getEvent()->getControllerAction()->getFullActionName();
       	//we compare action name to see if that's action for which we want to add our own event

    	switch ($actionName) {
    		case 'checkout_cart_estimatePost':
    			$this->_estimatePostQuote($observer);
    			break;
            case 'loworderfee_cart_estimatePost': //Added for compatibility with Mango Low Order Notification
                $this->_estimatePostQuote($observer);
                break;
    	}
    }

    private function _estimatePostQuote($observer) {
        $request = $observer->getControllerAction()->getRequest();
    	$addressType	    = (string) $request->getParam('dest_type');
        $country    		= (string) $request->getParam('country_id');
        $postcode   		= (string) $request->getParam('estimate_postcode');
        $city       		= (string) $request->getParam('estimate_city');
        $regionId   		= (string) $request->getParam('region_id');
        $region     		= (string) $request->getParam('region');

        $this->_getQuote()->getShippingAddress()
            ->setCountryId($country)
            ->setCity($city)
            ->setPostcode($postcode)
            ->setRegionId($regionId)
            ->setRegion($region)
            ->setDestType($addressType)
            ->setCollectShippingRates(true);
        $this->_getQuote()->save();
    }

    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }

   	protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
}