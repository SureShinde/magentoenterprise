<?php
/**
 * @category   CLS
 * @package    UniversalAnalytics
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

class CLS_UniversalAnalytics_Helper_Config extends Mage_Core_Helper_Abstract
{
    /**
     * Whether AdWords conversion tracking is enabled
     *
     * @return bool
     */
    public function isConversionEnabled()
    {
        return Mage::getStoreConfig('google/adwords/conversion_enabled');
    }

    /**
     * The AdWords conversion ID
     *
     * @return string
     */
    public function getConversionId()
    {
        return Mage::getStoreConfig('google/adwords/conversion_id');
    }

    /**
     * The AdWords conversion label
     *
     * @return string
     */
    public function getConversionLabel()
    {
        return Mage::getStoreConfig('google/adwords/conversion_label');
    }

    /**
     * The AdWords conversion language
     *
     * @return string
     */
    public function getConversionLanguage()
    {
        return Mage::getStoreConfig('google/adwords/conversion_language');
    }

    /**
     * Whether currency conversion is enabled for AdWords conversion tracking
     *
     * @return bool
     */
    public function isCurrencyConvertEnabled()
    {
        return Mage::getStoreConfig('google/adwords/currency_convert_enabled');
    }

    /**
     * The currency we want to convert to for AdWords conversion tracking, if it exists
     *
     * @return string
     */
    public function getNewCurrency()
    {
        return Mage::getStoreConfig('google/adwords/new_currency');
    }
    
    /**
     * Returns code of order count, e.g. metric2
     *
     * @param bool $returnShortCode
     * @return string
     */
    public function getOrderCountCode($returnShortCode = false)
    {
        $code = '';
        $index = Mage::getStoreConfig('google/analytics/order_count_index');
        if ($index) {
            $code = $this->_formatIndex('dimension', $index, $returnShortCode);
        }
        return $code;
    }
    
    /**
     * Returns code of custom group, e.g. dimension2
     *
     * @param bool $returnShortCode
     * @return string
     */
    public function getCustomerGroupCode($returnShortCode = false)
    {
        $code = '';
        $index = Mage::getStoreConfig('google/analytics/customer_group_index');
        if ($index) {
            $code = $this->_formatIndex('dimension', $index, $returnShortCode);
        }
        return $code;
    }
    
    /**
     * Returns code of page type, e.g. dimension2
     *
     * @param bool $returnShortCode
     * @return string
     */
    public function getPageTypeCode($returnShortCode = false)
    {
        $code = '';
        $index = Mage::getStoreConfig('google/analytics/page_type_index');
        if ($index) {
            $code = $this->_formatIndex('dimension', $index, $returnShortCode);
        }
        return $code;
    }
    
    /**
     * Returns code of custom product attribute, e.g. dimension2
     *
     * @param bool $returnShortCode
     * @return string
     */
    public function getCustomProductAttributeCode($returnShortCode = false)
    {
        $code = '';
        $index = Mage::getStoreConfig('google/analytics/custom_product_attribute_index');
        if ($index) {
            $code = $this->_formatIndex('dimension', $index, $returnShortCode);
        }
        return $code;
    }
    
    /**
     * returns code of custom product attribute dimension
     *
     * @return string
     */
    public function getCustomProductAttribute()
    {
        return Mage::getStoreConfig('google/analytics/custom_product_attribute');
    }
    
    /**
     * Returns code for metric/dimension + index. Code is different for JS and Measurement Protocol
     *
     * @param string $type
     * @param int $index
     * @param bool $shortCode
     * @return string
     */
    protected function _formatIndex($type, $index, $shortCode = false)
    {
        $formattedCode = '';
        if ($type == 'metric') {
            if ($shortCode) {
                $formattedCode = 'cm' . $index;
            } else {
                $formattedCode = 'metric' . $index;
            }
        } else {
            if ($shortCode) {
                $formattedCode = 'cd' . $index;
            } else {
                $formattedCode = 'dimension' . $index;
            }
        }
        return $formattedCode;
    }
}
