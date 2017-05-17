<?php
/**
 * WDCA - Sweet Tooth
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS
 * License, which extends the Open Software License (OSL 3.0).
 * The Sweet Tooth License is available at this URL:
 * https://www.sweettoothrewards.com/terms-of-service
 * The Open Software License is available at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * By adding to, editing, or in any way modifying this code, WDCA is
 * not held liable for any inconsistencies or abnormalities in the
 * behaviour of this code.
 * By adding to, editing, or in any way modifying this code, the Licensee
 * terminates any agreement of support offered by WDCA, outlined in the
 * provided Sweet Tooth License.
 * Upon discovery of modified code in the process of support, the Licensee
 * is still held accountable for any and all billable time WDCA spent
 * during the support process.
 * WDCA does not guarantee compatibility with any other framework extension.
 * WDCA is not responsbile for any inconsistencies or abnormalities in the
 * behaviour of this code if caused by other framework extension.
 * If you did not receive a copy of the license, please send an email to
 * support@sweettoothrewards.com or call 1.855.699.9322, so we can send you a copy
 * immediately.
 *
 * @category   [TBT]
 * @package    [TBT_Rewards]
 * @copyright  Copyright (c) 2014 Sweet Tooth Inc. (http://www.sweettoothrewards.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * This class is used as an aggregation class for object in different areas
 * @package     TBT_Rewards
 * @subpackage  Model
 * @author      Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Model_Sales_Aggregated_Cart
    extends Mage_Core_Model_Abstract
{
    /**
     * Loads quote based on action area (adminhtml or frontend)
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::getSingleton('adminhtml/session_quote')->getQuote();
        }
        
        return Mage::getSingleton('checkout/session')->getQuote();
    }
    
    /**
     * Loads website id based on action area (adminhtml or frontend)
     * @return int
     */
    public function getWebsiteId()
    {
        $storeId = $this->getQuote()->getStoreId();
        
        return Mage::app()->getStore($storeId)->getWebsiteId();
    }
    
    public function getCustomerGroupId()
    {
        if ($customer = $this->getCustomer()) {
            return $customer->getGroupId();
        }
        
        return Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;
    }
    
    /**
     * Getter for Current Customer in checkout process on frontend or admin order creation
     * @return TBT_Rewards_Model_Customer|null if null then the customer is a guest in frontend
     */
    public function getCustomer()
    {
        $customer = null;
        
        if (Mage::app()->getStore()->isAdmin()) {
            $customer = Mage::getSingleton('adminhtml/session_quote')
                ->getCustomer();
        } else {
            $customerSession = Mage::getSingleton('customer/session');
            $customer = ($customerSession->isLoggedIn()) ? $customerSession->getCustomer() : null;
        }
        
        return ($customer && $customer->getId()) ? 
            Mage::getModel('rewards/customer')->getRewardsCustomer($customer) : null;
    }
    
    public function getStoreId()
    {
        return $this->getQuote()->getStoreId();
    }
    
    public function getStore()
    {
        return Mage::app()->getStore($this->getStoreId());
    }
}