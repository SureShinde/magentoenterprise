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
 * @category   CLS
 * @package    NucleusCore
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */
/**
 * Pseudo-model to emulate 'checkout/session' model
 *
 */

class CLS_NucleusCore_Model_Checkout_Success extends Mage_Core_Model_Abstract
{

    /**
     * Keeps quote model
     *
     * @var Mage_Sales_Model_Quote|null
     */
    protected $_quote = null;

    /**
     * Get quote associated with last order
     *
     * @return  Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (is_null($this->_quote)) {
            $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
            if ($orderId) {
                /** @var Mage_Sales_Model_Order $order */
                $order = Mage::getModel('sales/order')->load($orderId);
                if ($order->getId()) {
                    $this->_quote = Mage::getModel('sales/quote')->load($order->getQuoteId());
                }
            }

            if (is_null($this->_quote)) {
                $this->_quote = Mage::getModel('sales/quote'); # Assign empty quote so that dependent functionality will still work
            }
        }

        return $this->_quote;
    }

}