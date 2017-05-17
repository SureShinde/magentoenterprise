<?php
/**
 * @category   CLS
 * @package    Widgets
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */
class SmartOsc_CategoryCarousel_Block_Widget_Catalog_Product_Carousel extends CLS_Widgets_Block_Widget_Catalog_Product_Carousel
{
    /**
     * Retrieve category product collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getProductCollection()
    {
        $this->_productCollection=Mage::getModel('smartosc_categorycarousel/banner')->getCollection()->addFieldToFilter("status", array('eq' => 1))->setOrder("ordering", "ASC");
        $this->_productCollection->getSelect()->limit(4);
        return $this->_productCollection;
    }

    public function getIncludePrevNext()
    {
        return false;
    }
}