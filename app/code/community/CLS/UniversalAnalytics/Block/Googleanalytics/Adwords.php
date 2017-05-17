<?php
/**
 * @category   CLS
 * @package    UniversalAnalytics
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

class CLS_UniversalAnalytics_Block_Googleanalytics_Adwords extends Mage_Core_Block_Template
{
    /**
     * Is AdWords integration enabled?
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::helper('cls_universalanalytics/config')->isConversionEnabled();
    }

    /**
     * AdWords conversion ID
     *
     * @return string
     */
    public function getConversionId()
    {
        return Mage::helper('cls_universalanalytics/config')->getConversionId();
    }

    /**
     * AdWords conversion label
     *
     * @return string
     */
    public function getLabel()
    {
        return Mage::helper('cls_universalanalytics/config')->getConversionLabel();
    }

    /**
     * AdWords conversion language
     *
     * @return string
     */
    public function getLanguage()
    {
        return Mage::helper('cls_universalanalytics/config')->getConversionLanguage();
    }

    /**
     * URL to the AdWords conversion JS
     *
     * @return string
     */
    public function getConversionUrl()
    {
        $protocol = $this->getRequest()->isSecure() ? 'https://' : 'http://';
        return $protocol . 'www.googleadservices.com/pagead/conversion.js';
    }

    /**
     * The value of the conversion to be sent to AdWords
     *
     * @return int|string
     */
    public function getValue()
    {
        $value = 0;
        $order = $this->_getLastOrder();
        if ($order->getId()){
            $value = $order->getBaseGrandTotal();
            if (Mage::helper('cls_universalanalytics/config')->isCurrencyConvertEnabled()) {
                $newCurrency = Mage::getModel('directory/currency')->load(Mage::helper('cls_universalanalytics/config')->getNewCurrency());
                if ($newCurrency && $newCurrency->getCode()) {
                    $value = sprintf("%01.4f", Mage::app()->getStore()->roundPrice($order->getBaseCurrency()->convert($order->getBaseGrandTotal(), $newCurrency)));
                }
            }
        }
        return $value;
    }

    /**
     * Get the order that was just placed
     *
     * @return Mage_Sales_Model_Order
     */
    private function _getLastOrder()
    {
        $quoteId = Mage::getSingleton('checkout/session')->getLastQuoteId();
        return Mage::getModel('sales/order')->loadByAttribute('quote_id', $quoteId);
    }
}
