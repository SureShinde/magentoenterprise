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
 * Customer registration model
 */
class Crius_Rac_Model_Register
{
    /**
     * Get customer session object
     *
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    /**
     * Register customer account and attach last order to customer
     *
     * @param array $data
     * @return array
     */
    public function register($data)
    {
        $session = Mage::getSingleton('checkout/session');
        
        // Check that input data, quote and order is available
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }
        if (!$session->getLastOrderId()) {
            return array('error' => -1, 'message' => Mage::helper('rac')->__('Last order not found.'));
        }
        if (!$session->getLastQuoteId()) {
            return array('error' => -1, 'message' => Mage::helper('rac')->__('Last quote not found.'));
        }
        
        // Load order and quote data
        $order         = Mage::getModel('sales/order')->load($session->getLastOrderId()); /* @var $order Mage_Sales_Model_Order */
        $quote         = Mage::getModel('sales/quote')->load($session->getLastQuoteId()); /* @var $quote Mage_Sales_Model_Quote */
        $billing       = $quote->getBillingAddress();
        $shipping      = $quote->getIsVirtual() ? null : $quote->getShippingAddress();
        $orderBilling  = $order->getBillingAddress();
        $orderShipping = $order->getIsVirtual() ? null : $order->getShippingAddress();            
        
        // Create customer object
        $customer = $quote->getCustomer();
        /* @var $customer Mage_Customer_Model_Customer */
        
        // Add billing address to customer, and update reference in quote address to the customer address
        $customerBilling = $billing->exportCustomerAddress();
        $customer->addAddress($customerBilling);
        $billing->setCustomerAddress($customerBilling);
        $customerBilling->setIsDefaultBilling(true);
        
        // If quote has shipping address, add it to customer. Otherwise set billing addr as default shipping addr.
        $customerShipping = null;
        if ($shipping && !$shipping->getSameAsBilling()) {
            $customerShipping = $shipping->exportCustomerAddress();
            $customer->addAddress($customerShipping);
            $customerShipping->setIsDefaultShipping(true);
        } else {
            $customerBilling->setIsDefaultShipping(true);
        }
        
        // Update the reference in quote shipping address to the customer address
        if ($shipping) {
            if (!$shipping->getSameAsBilling()) {
                $shipping->setCustomerAddress($customerShipping);
            } else {
                $shipping->setCustomerAddress($customerBilling);
            }
        }

        // Copy quote data to customer object
        $result = Mage::helper('core')->copyFieldset('checkout_onepage_quote', 'to_customer', $quote, $customer);
        // Magento 1.4 does not have checkout_onepage_quote
        if (!$result) {
            // Copy billing address to customer, and customer to order
            Mage::helper('core')->copyFieldset('checkout_onepage_billing', 'to_customer', $orderBilling, $customer);
            $customer->setEmail($order->getCustomerEmail());
            Mage::helper('core')->copyFieldset('customer_account', 'to_order', $customer, $order);
        }
        
        // Set customer password
        $customer->setPassword($data['password']);
        $customer->setPasswordConfirmation($data['confirmation']);
        $customer->setPasswordHash($customer->hashPassword($customer->getPassword()));
        
        // Update quote reference to customer and change method from guest to register
        $quote->setCustomer($customer)
            ->setCustomerId(true)
            ->setCheckoutMethod('register');
            
        // Validate customer object
        $errors = $customer->validate();
        if ($errors !== true) {
            return array('error' => 1, 'message' => $errors);
        }
        
        // Save customer and quote
        $customer->save();
        $quote->save();
        
        // Update order with new customer data
        $order->setCustomer($customer);
        $order->setCustomerIsGuest(false);
        $order->setCustomerGroupId($customer->getGroupId());
        // Update order billing address with customer address reference
        $orderBilling->setCustomerId($customer->getId())
            ->setCustomerAddressId($customerBilling->getId());
        // Update order shipping address with customer address reference
        if ($orderShipping) {
            $orderShipping->setCustomerId($customer->getId());
            if ($shipping && !$shipping->getSameAsBilling()) {
                $orderShipping->setCustomerAddressId($customerShipping->getId());
            } else {
                $orderShipping->setCustomerAddressId($customerBilling->getId());
            }
        }
        // Save order
        $order->save();
        
        // Confirm customer
        $result = $this->_involveNewCustomer($customer);
        return $result;
    }
    
    /**
     * Involve new customer to system
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return array
     */
    protected function _involveNewCustomer($customer)
    {
        $result = array();
        if ($customer->isConfirmationRequired()) {
            // Confirmation required: Send confirmation email and return confirmation message
            $customer->sendNewAccountEmail('confirmation');
            $url = Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail());
            $this->getCustomerSession()->addSuccess(Mage::helper('customer')->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.', $url));
            $result['redirect'] = Mage::getUrl('customer/account/index', array('_secure'=>true));
        } else {
            // No confirmation required: Log in, send new account email, redirect to account with success message
            $this->getCustomerSession()->addSuccess(Mage::helper('customer')->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName()));
            $customer->sendNewAccountEmail();
            $this->getCustomerSession()->setCustomerAsLoggedIn($customer);
            $result['redirect'] = Mage::getUrl('customer/account/index', array('_secure'=>true));
        }
        return $result;
    }
}
