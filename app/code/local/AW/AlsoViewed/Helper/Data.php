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

class AW_AlsoViewed_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get also viewed products for product
     * @param int|Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function getAlsoViewedProductsCollection($product) {
        if (is_numeric($product)) {
            $product = Mage::getModel('catalog/product')->load($product);
        }

        $productsToDisplay = Mage::getStoreConfig('aw_alsoviewed/general/products_to_display');

        /** @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
        $collection = $product->getCollection();

        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
        $collection->addAttributeToSelect($attributes)
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addStoreFilter();

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        $collection->getSelect()
            ->join(array('av' => $collection->getTable('aw_alsoviewed/product')), 'av.link_product_id = e.entity_id', '')
            ->where('av.product_id = ?', $product->getId())
            ->order('av.views DESC')
            ->limit($productsToDisplay);

        return $collection;
    }

    /**
     * Check magento version
     * @param string $version
     * @return bool
     */
    public function checkVersion($version) {
        return version_compare(Mage::getVersion(), $version, '>=');
    }
}
