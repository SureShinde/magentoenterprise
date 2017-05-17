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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Shipestimate
 * User         Genevieve Eddison
 * Date         25 June 2013
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

require_once 'Mage/Catalog/controllers/ProductController.php';
class Webshopapps_Shipestimate_EstimateController extends Mage_Catalog_ProductController
{
    protected $_debug;

    public function estimateAction()
    {
        Mage::getSingleton('core/session')->setProductPageEstimate(true);
        $requestEstimateData = $this->getRequest()->getParams();
        $this->loadLayout(false);
        $quoteBlock = $this->getLayout()->getBlock('shipping_estimate_result');
        $useCart = Mage::helper('shipestimate')->useCartInEstimate();
        $showSavings = Mage::helper('shipestimate')->displaySavings();

        if ($quoteBlock) {

            $shippingQuoter = Mage::getModel('apishipping/quote_quoteshipping');
            $productsInCart = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
            try {
                if(!$useCart || count($productsInCart) == 0 || ($showSavings && $useCart)) {
                    $quoteData = $this->_formatData($requestEstimateData);
                    $quotedRates = $shippingQuoter->quoteShipping($quoteData);
                    //cache data for quoted rates of single item here
                    $debugData['request'] = $quoteData;
                }

                if($useCart && count($productsInCart) > 0) {
                    $inclCartQuoteData = $this->_formatData($requestEstimateData, $productsInCart);
                    $inclCartQuoter = Mage::getModel('apishipping/quote_quoteshipping');
                    $inclCartQuotedRates = $inclCartQuoter->quoteShipping($inclCartQuoteData);
                    $debugData['including cart request'] = $inclCartQuoteData;

                    $justCartQuoteData = $this->_formatData($requestEstimateData, $productsInCart, true);
                    $justCartQuoter = Mage::getModel('apishipping/quote_quoteshipping');
                    $justCartQuotedRates = $justCartQuoter->quoteShipping($justCartQuoteData);
                    $debugData['excluding item cart request'] = $justCartQuoteData;

                    $useCartQuotedRates = $this->_calculateCartInclRates($inclCartQuotedRates, $justCartQuotedRates);
                    if($showSavings) {
                        $quotedRates = $this->_calculateSavings($useCartQuotedRates, $quotedRates);
                    }
                    else {
                        $quotedRates = $useCartQuotedRates;
                    }
                    if(empty($quotedRates) && !empty($inclCartQuotedRates)) {
                        if ($this->isDebug()) {
                            Mage::helper('wsalogger/log')->postInfo('Shipestimate', 'Could not match rates when including item and cart',
                                'Displaying rates including cart');
                        }
                        $quotedRates = $inclCartQuotedRates;
                        $quoteBlock->setNotice(Mage::helper('shipestimate')->__('Includes items already in your cart'));
                    }
                    else {
                        $quoteBlock->setNotice(false);
                    }
                }
                $quoteBlock->setResults($quotedRates);
                $debugData['results from '] = array_keys($quotedRates);

            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('catalog/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('catalog/session')->addError($e->getMessage());
            }
        }
        if ($this->isDebug()) {
            Mage::helper('wsalogger/log')->postDebug('Shipestimate', 'Request and Response', $debugData);
        }
        Mage::getSingleton('core/session')->setProductPageEstimate(false);

        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();

    }

    protected function _calculateCartInclRates($inclCartQuotedRates, $justCartQuotedRates)
    {
        $debugData = array();
        foreach($inclCartQuotedRates as $code => $rates)
        {
            if(array_key_exists($code, $justCartQuotedRates)) {
                $foundJustCartRates = $justCartQuotedRates[$code];
                foreach($rates as $inclRate) {
                    foreach($foundJustCartRates as $exclRate) {
                        if($inclRate['error_message'] == '' && $inclRate['method'] == $exclRate['method']) {
                            $debugData[$code .'_'.$inclRate['method']] = array('rate including item' => $inclRate['price'], 'rate excluding item' => $exclRate['price']);
                            $inclRate['price'] = $inclRate['price'] - $exclRate['price'];
                        }
                    }
                }
            }
            else {
                unset($inclCartQuotedRates[$code]);
            }
        }
        if ($this->isDebug()) {
            Mage::helper('wsalogger/log')->postDebug('Shipestimate', 'Calculating rates using cart', $debugData);
        }
        return $inclCartQuotedRates;
    }

    protected function _calculateSavings($rawRates, $singularRates)
    {
        foreach($rawRates as $code =>$rates) {
            $singleItemRates = $singularRates[$code];
            foreach($rates as $rate) {
                foreach($singleItemRates as $comparisonrate) {
                    if($comparisonrate['method_title'] == $rate['method_title']) {
                        $savings = $comparisonrate['price'] - $rate['price'];
                        $rate['savings'] = $savings;
                        $debugData[$code .'_'.$rate['method']] = array('rate including item' => $rate['price'], 'rate of item alone' => $comparisonrate['price']);
                    }
                }
            }
        }
        if ($this->isDebug()) {
            Mage::helper('wsalogger/log')->postDebug('Shipestimate', 'Calculating savings for prices using cart', $debugData);
        }
        return $rawRates;
    }

    protected function _formatData($rawData, $cartItems = null, $omitViewed = false)
    {
        $testProducts = array();
        if(!array_key_exists('estimate', $rawData)) {
            if ($this->isDebug()) {
                Mage::helper('wsalogger/log')->postCritical('Shipestimate', 'Error - Cant find estimate data', $rawData);
            }
            return null;
        }

        if(!$omitViewed) {
            $product1 = array('sku' => $rawData['estimate']['sku'], 'qty' => $rawData['estimate']['qty']);
            if(array_key_exists('options', $rawData)) {
                $product1['options'] = $rawData['options'];
            }

            if(array_key_exists('super_attribute', $rawData)) {
                $product1['super_attribute'] = $rawData['super_attribute'];
            }

            if(array_key_exists('super_group', $rawData)) {
                $product1['super_group'] = $rawData['super_group'];
            }

            if(array_key_exists('bundle_option', $rawData)) {
                $product1['bundle_option'] = $rawData['bundle_option'];
            }

            if(array_key_exists('bundle_option_qty', $rawData)) {
                $product1['bundle_option_qty'] = $rawData['bundle_option_qty'];
            }

            $testProducts[] = $product1;
        }

        if($cartItems) {
            $productsInCart = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
            foreach ($productsInCart as $item) {
                $cartItem = array('sku' => $item->getSku(), 'qty' => $item->getQty(), 'product_id' => $item->getProductId());
                $buyRequest = $item->getBuyRequest();

                if($buyRequest->getOptions()) $cartItem['options'] = $buyRequest->getOptions();
                if($buyRequest->getSuperAttribute()) $cartItem['super_attribute'] = $buyRequest->getSuperAttribute();
                if($buyRequest->getSuperGroup()) $cartItem['super_group'] = $buyRequest->getSuperGroup();
                if($buyRequest->getBundleOption()) {
                    $cartItem['bundle_option'] = $buyRequest->getBundleOption();
                    $cartItem['bundle_option_qty'] = $buyRequest->getBundleOptionQty();
                }
                $testProducts[] = $cartItem;
            }
        }

        $testAddress = array('Zip' => $rawData['estimate']['postcode'], 'Country' => $rawData['estimate']['country_id']);
        if (array_key_exists('city', $rawData['estimate'])) {
            $testAddress['City'] = $rawData['estimate']['city'];
        }
        if (array_key_exists('region_id', $rawData['estimate'])) {
            $testAddress['State'] = $rawData['estimate']['region_id'];
        }
        if (array_key_exists('dest_type', $rawData['estimate'])) {
            $testAddress['Address Type'] = $rawData['estimate']['dest_type'];
        }
        $testData = array('Products' => $testProducts, 'Address' => $testAddress);
        return $testData;
    }

    protected function isDebug()
    {
        if (is_null($this->_debug)) {
            $this->_debug = Mage::helper('wsalogger')->isDebug('Webshopapps_Shipestimate');
        }
        return $this->_debug;
    }
}
