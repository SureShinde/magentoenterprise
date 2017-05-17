<?php
/**
 * Nucleus
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Nucleus License
 * that is bundled with this package in the file LICENSE_NUCLEUS.txt.
 * It is also available through the World Wide Web at this URL:
 * http://www.nucleuscommerce.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the World Wide Web, please send an email
 * to support@nucleuscommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Nucleus
 * to newer versions in the future.
 *
 * @category   Nucleus
 * @package    Integration
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

class Nucleus_Integration_Model_Adminhtml_Sales_Order_Create extends CLS_Paypal_Model_Adminhtml_Sales_Order_Create
{
    /**
     * COPIED FROM OnePica_AvaTax_Model_Adminhtml_Sales_Order_Create
     *
     * If a session message has been added.
     *
     * @var bool
     */
    protected $_messageAdded = false;

    /**
     * COPIED FROM OnePica_AvaTax_Model_Adminhtml_Sales_Order_Create
     *
     * Overrides the parent to validate the shipping address.
     *
     * @param array $address
     * @return OnePica_AvaTax_Model_Adminhtml_Sales_Order_Create
     */
    public function setShippingAddress($address)
    {
        parent::setShippingAddress($address);

        if ($this->getQuote()->getIsVirtual()) {
            return $this;
        }

        if (Mage::helper('avatax')->isAvataxEnabled()) {
            if (!Mage::app()->getFrontController()->getRequest()->getParam('isAjax')) {
                $result = $this->getShippingAddress()->validate();
                if ($result !== true) {
                    $storeId = $this->_session->getStore()->getId();
                    if(Mage::helper('avatax')->fullStopOnError($storeId)) {
                        foreach ($result as $error) {
                            $this->getSession()->addError($error);
                        }
                        Mage::throwException('');
                    }
                }
                else if ($this->getShippingAddress()->getAddressNormalized() && !$this->_messageAdded) {
                    Mage::getSingleton('avatax/session')->addNotice(Mage::helper('avatax')->__('The shipping address has been modified during the validation process.  Please confirm the address below is accurate.'));
                    $this->_messageAdded = true;  // only add the message once
                }
            }
        }
        return $this;
    }
}