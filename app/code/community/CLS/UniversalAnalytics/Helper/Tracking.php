<?php
/**
 * @category   CLS
 * @package    UniversalAnalytics
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

class CLS_UniversalAnalytics_Helper_Tracking extends Mage_Core_Helper_Abstract
{
    protected $_dimensionsAndMetrics;

    /**
     * Return regular page tracking javascript code
     *
     * @param string $accountId
     * @param string $anonymizationCode
     * @param array $orderIds
     * @return string
     */
    public function getPageTrackingCodeUniversal($accountId, $anonymizationCode, $orderIds)
    {
        $this->_dimensionsAndMetrics = array();

        $return = '';

        $return .= "
ga('create', '$accountId', 'auto');
" . $anonymizationCode;

        // Track order data
        if (is_array($orderIds) && !empty($orderIds)) {
            $collection = Mage::getResourceModel('sales/order_collection')
                ->addFieldToFilter('entity_id', array('in' => $orderIds));

            foreach ($collection as $order) {
                $this->_fillOrderCount($order);
            }
        }

        // Dimension and metrics
        $return .= $this->_getDimensionsAndMetrics();

        $return .= "\n";
        $return .= "ga('send', 'pageview');";

        return $return;
    }

    /**
     * Return dimension and metrics string for GA
     *
     * @return string
     */
    protected function _getDimensionsAndMetrics()
    {
        $product = Mage::registry('current_product');
        $this
            ->_fillPageType()
            ->_fillCustomerGroupCode()
            ->_fillCustomProductAttribute($product);

        $result = '';
        if (!empty($this->_dimensionsAndMetrics)) {
            $result = "\n";
            $result .= "ga('set', " . json_encode($this->_dimensionsAndMetrics) . ");";
            $result .= "\n";
        }
        return $result;
    }

    /**
     * Adds order count to $this->_dimensionsAndMetrics array
     *
     * @param Mage_Sales_Model_Order $order
     * @return CLS_UniversalAnalytics_Block_Googleanalytics_Ga
     */
    protected function _fillOrderCount($order)
    {
        $orderCount = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_email', $order->getCustomerEmail())->count();
        $code = Mage::helper('cls_universalanalytics/config')->getOrderCountCode();
        if ($orderCount && $code) {
            $this->_dimensionsAndMetrics[$code] =  (string)$orderCount;
        }
        return $this;
    }

    /**
     * Adds customer group code to $this->_dimensionsAndMetrics array
     *
     * @return CLS_UniversalAnalytics_Block_Googleanalytics_Ga
     */
    protected function _fillCustomerGroupCode()
    {
        $customerGroupCodeMage = Mage::getModel('customer/group')->load(Mage::getSingleton('customer/session')->getCustomerGroupId())->getCode();
        $code = Mage::helper('cls_universalanalytics/config')->getCustomerGroupCode();
        if ($customerGroupCodeMage && $code) {
            $this->_dimensionsAndMetrics[$code] =  (string)$customerGroupCodeMage;
        }
        return $this;
    }

    /**
     * Adds page type to $this->_dimensionsAndMetrics array
     *
     * @return CLS_UniversalAnalytics_Block_Googleanalytics_Ga
     */
    protected function _fillPageType()
    {
        $pageType = null;

        $currentCategory = Mage::registry('current_category');
        $currentProduct = Mage::registry('current_product');
        if ($currentProduct && $currentProduct->getId()) {
            $pageType = $this->__('Product-%s', ucfirst($currentProduct->getTypeId()));
        } elseif ($currentCategory && $currentCategory->getId()) {
            $pageType = $this->__('Category');
        } elseif (Mage::app()->getRequest()->getModuleName() == 'cms') {
            $pageType = $this->__('CMS');
        } elseif (Mage::app()->getLayout()->getBlock('customer_account_navigation') !== false) {
            $pageType = $this->__('Customer Account');
        }

        $code = Mage::helper('cls_universalanalytics/config')->getPageTypeCode();
        if ($pageType && $code) {
            $this->_dimensionsAndMetrics[$code] = $pageType;
        }
        return $this;
    }

    /**
     * Adds customer product atrtibute to $this->_dimensionsAndMetrics array
     *
     * @param Mage_Catalog_Model_Product $product
     * @return CLS_UniversalAnalytics_Block_Googleanalytics_Ga
     */
    protected function _fillCustomProductAttribute($product)
    {
        $productAttributeCode = Mage::helper('cls_universalanalytics/config')->getCustomProductAttribute();
        $code = Mage::helper('cls_universalanalytics/config')->getCustomProductAttributeCode();
        if ($code && $productAttributeCode && $product && $product->getId()) {
            $attributeValue = $product->getData($productAttributeCode);
            if (!$attributeValue) {
                $attribute = $product->getResource()->getAttribute($code);
                if ($attribute) {
                    $attributeValue = $attribute->getFrontend()->getValue($productAttributeCode);
                }
            }
            if ($attributeValue) {
                $this->_dimensionsAndMetrics[$code] = $attributeValue;
            }
        }
        return $this;
    }
}