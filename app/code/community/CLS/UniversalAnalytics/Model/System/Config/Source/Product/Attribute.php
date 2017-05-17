<?php
/**
 * @category   CLS
 * @package    UniversalAnalytics
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

class CLS_UniversalAnalytics_Model_System_Config_Source_Product_Attribute {

    public function toOptionArray() 
    {
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')->addVisibleFilter();
        $attributeArray = array();
        $attributeArray[] = array(
                    'label' => ' --- ',
                    'value' => 0
                );

        foreach($attributes as $attribute){
                $attributeArray[] = array(
                    'label' => $attribute->getData('frontend_label'),
                    'value' => $attribute->getData('attribute_code')
                );
        }
        return $attributeArray; 
    }

}