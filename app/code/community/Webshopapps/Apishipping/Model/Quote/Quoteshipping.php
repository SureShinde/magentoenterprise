<?php
/** WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Apishipping
 * User         Genevieve Eddison
 * Date         17 June 2013
 * Time         11:45
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

class Webshopapps_Apishipping_Model_Quote_Quoteshipping
{

    protected $_quote = null;
    protected $_request = null;

    protected $_debug = false;

    public function quoteShipping($virtualCartData)
    {
        $this->_debug = Mage::helper('wsalogger')->isDebug('Webshopapps_Apishipping');
        $quote = $this->getQuote();
        $shippingAddress = $quote->getShippingAddress();
        $addressData = $this->_getAddressData($virtualCartData);
        if ($this->_debug) {
            Mage::helper('wsalogger/log')->postDebug('Apishipping', 'Request data: ', $virtualCartData);
        }
        if (is_null($addressData)) {
            Mage::throwException(
                Mage::helper('apishipping')->__('No address data supplied in shipping quote request')
            );
        }
        $cartContents = $this->_getProductsInCart($virtualCartData);

        if (is_null($cartContents)) {
            Mage::throwException(
                Mage::helper('apishipping')->__('No product data supplied in shipping quote request')
            );
        }
        if (!isset($addressData['Country'])) {
            Mage::throwException(
                Mage::helper('apishipping')->__('Country not specified in shipping quote request')
            );
        }
        $shippingAddress->setCountryId($addressData['Country']);
        if (isset($addressData['City'])) {
            $shippingAddress->setCity($addressData['City']);
        }
        if (isset($addressData['Zip'])) {
            $shippingAddress->setPostcode(strtolower($addressData['Zip']));
        }
        if (isset($addressData['State'])) {
            $shippingAddress->setRegionId($addressData['State']);
        }
        if (isset($addressData['Address Type'])) {
            $shippingAddress->setDestType($addressData['Address Type']);
        }

        foreach ($cartContents as $productInCart) {
            $product = Mage::getModel('catalog/product');
            if(array_key_exists('product_id', $productInCart)) {
                $product->load($productInCart['product_id']);
            }
            else {
                $product->load($prodId = $product->getIdBySku($productInCart['sku']));
            }

            $conf = Mage::getModel('catalog/product_type_configurable');

            if(array_key_exists('super_attribute', $productInCart)) {
                $simple = $conf->getProductByAttributes($productInCart['super_attribute'], $product);
    
                if(is_object($simple)) {//PVR-8 We want the associate simple product if parent is configurable
                    $product = $product->load($simple->getId());
                }
            }
            if (!is_null($product)) {
                $request = new Varien_Object(array('qty' => $productInCart['qty']));
                if ($stockItem = $product->getStockItem()) {
                    $product->getStockItem()->setIsInStock(1);
                    $minimumQty = $product->getStockItem()->getMinSaleQty();
                    if ($minimumQty > 0 && $request->getQty() < $minimumQty) {
                        $request->setQty($minimumQty);
                    }
                }
                if(array_key_exists('options', $productInCart)) {
                    $request->setOptions($productInCart['options']);
                }
                if(array_key_exists('super_attribute', $productInCart)) {
                    $request->setSuperAttribute($productInCart['super_attribute']);
                }
                if(array_key_exists('super_group', $productInCart)) {
                    $request->setSuperGroup($productInCart['super_group']);
                }
                if(array_key_exists('bundle_option', $productInCart)) {
                    $request->setBundleOption($productInCart['bundle_option']);
                    $request->setBundleOptionQty($productInCart['bundle_option_qty']);
                }
                $quote->addProduct($product, $request);

            } else {
                Mage::throwException(
                    Mage::helper('apishipping')->__('Product does not exist: ' . $productInCart['sku'])
                );
            }
        }
        $allItems = $quote->getAllItems();
        $count = 1;
        foreach($allItems as $item) {
            $item->setId($count);
            $count++;
        }

        $shippingAddress->collectTotals();
        $shippingAddress->setCollectShippingRates(true);
        $shippingAddress->collectShippingRates();
        $rates = $shippingAddress->getGroupedAllShippingRates();

        return $rates;

    }

    public function getQuote()
    {
        if ($this->_quote == null) {
            $quote = Mage::getModel('sales/quote');
            $this->_quote = $quote;
        }
        return $this->_quote;
    }

    protected function _getAddressData($virtualCartData)
    {
        return $virtualCartData['Address'];
    }

    protected function _getProductsInCart($virtualCartData)
    {
        return $virtualCartData['Products'];
    }


}