<?php
/**
 * Crius
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt
 *
 * @category   Crius
 * @package    Crius_Rac
 * @copyright  Copyright (c) 2011 Crius (http://www.criuscommerce.com)
 * @license    http://www.criuscommerce.com/CRIUS-LICENSE.txt
 */
/**
 * Registration block for success page
 */
class Crius_Rac_Block_Register extends Mage_Core_Block_Template
{
    /**
     * Is registration possible
     *
     * @return bool
     */
    public function canRegister()
    {
        $customer = Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getWebsite()->getId())
            ->loadByEmail($this->getOrder()->getCustomerEmail());
        $customerExists = (bool) $customer->getId();
        // Registration enabled if customer is not logged in (guest) and email does not already exist
        return (!Mage::getSingleton('customer/session')->isLoggedIn() && !$customerExists);
    }
    
    /**
     * Get last order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        $session = Mage::getSingleton('checkout/session');
        return Mage::getModel('sales/order')->load($session->getLastOrderId());
    }
}
