<?php
/**
 * @category   CLS
 * @package    ProductCompare
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */
class CLS_ProductCompare_Block_Catalog_Product_Compare_List extends Mage_Catalog_Block_Product_Compare_List {

    /**
     * Retrieve Product Compare Attributes, but only ones with actual values for some products.
     *
     * @return array
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes = $this->getItems()->getComparableAttributes();
            //In addition to just getting the attributes, make sure each attribute has at least one real value among the products.
            $validAttributes = array();
            foreach ($this->_attributes as $attribute) {
                foreach ($this->getItems() as $product) {
                    //If a value is found, add this to the list of valid attributes and move on.
                    if ((string) $product->getData($attribute->getAttributeCode()) != '') {
                        $validAttributes[] = $attribute;
                        continue 2;
                    }
                }
            }
            $this->_attributes = $validAttributes;
        }

        return $this->_attributes;
    }

}