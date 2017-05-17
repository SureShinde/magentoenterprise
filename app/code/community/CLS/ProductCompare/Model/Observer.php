<?php
/**
 * @category   CLS
 * @package    ProductCompare
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */
class CLS_ProductCompare_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * When change of product comparison occurs, update the custom cookie that tracks product IDs
     *
     * @param Varien_Event_Observer $observer
     */
    public function updateCustomCookie(Varien_Event_Observer $observer)
    {
        Mage::helper('catalog/product_compare')->calculate();

        $listItems = Mage::helper('catalog/product_compare')->getItemCollection();
        $ids = array();
        foreach ($listItems as $item) {
            $ids[] = $item->getId();
        }
        sort($ids);

        Mage::getSingleton('core/cookie')->set(CLS_ProductCompare_Helper_Data::COOKIE_COMPARE_LIST, implode(',', $ids), null, null, null, null, false);
    }
}