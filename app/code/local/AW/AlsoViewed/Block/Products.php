<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento enterprise edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_AlsoViewed
 * @version    1.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_AlsoViewed_Block_Products extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Get the current viewed product
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct() {
        return Mage::registry('product');
    }

    /**
     * Get also viewed products collection
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function getAlsoViewedCollection() {
        /** @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
        $collection = Mage::helper('aw_alsoviewed')->getAlsoViewedProductsCollection($this->getProduct());

        if (Mage::getStoreConfig('aw_alsoviewed/general/same_category')) {
            $collection->addCategoryFilter($this->getCategory());
        }

        return $collection;
    }

    protected function _beforeToHtml()
    {
        $this->_getBlockPosition();
        return parent::_beforeToHtml();
    }

    public function _getBlockPosition()
    {
        switch (Mage::getStoreConfig('aw_alsoviewed/general/block_position')){
            case 1:
                if($this->getLayout()->getBlock('awav.instead.native')) {
                    $this->getLayout()->getBlock('awav.instead.native')->setTemplate('catalog/product/list/alsoviewed.phtml');
                }
                break;
            case 2:
                if($this->getLayout()->getBlock('awav.inside.product.info.tabs')) {
                    $this->getLayout()->getBlock('awav.inside.product.info.tabs')->setTemplate('catalog/product/list/alsoviewed.phtml');
                    break;
                }
            case 3:
                if($this->getLayout()->getBlock('awav.right.column')) {
                    $this->getLayout()->getBlock('awav.right.column')->setTemplate('catalog/product/list/alsoviewed.phtml');
                    break;
                }
            case 0:
                if($this->getLayout()->getBlock('awav.custom')) {
                    $this->getLayout()->getBlock('awav.custom')->setTemplate('catalog/product/list/alsoviewed.phtml');
                    break;
                }
            default:

                break;

        }
    }
    /**
     * Get current category
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory() {
        return Mage::registry('current_category') ? Mage::registry('current_category') : Mage::getModel('catalog/category');
    }

    /**
     * Is module enabled?
     * @return bool
     */
    public function getEnabled() {
        return !!Mage::getStoreConfig('aw_alsoviewed/general/enabled');
    }
}