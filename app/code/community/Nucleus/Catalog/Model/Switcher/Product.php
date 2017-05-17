<?php
/**
 * Nucleus
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Nucleus License
 * that is bundled with this package in the file LICENSE_NUCLEUS.txt.
 * It is also available through the World Wide Web at this URL:
 * http://www.nucleuscommerce.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the World Wide Web, please send an email
 * to support@nucleuscommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Nucleus
 * to newer versions in the future.
 *
 * @category   Nucleus
 * @package    Catalog
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

class Nucleus_Catalog_Model_Switcher_Product extends Nucleus_Catalog_Model_Switcher_Abstract {
    
    const IS_ENABLED_CATEGORY_ATTR_CODE = 'cross_navigation_products';
    
    protected $_isEnabledCategoryAttrCode = self::IS_ENABLED_CATEGORY_ATTR_CODE;

    public function getNavigationCategory() {
        if (!$this->getCategory()) {
            return null;
        }
        return $this->getCategory();
    }
    
    public function hasNext() {
        return (bool)$this->_getSiblingProduct('next');
    }

    public function hasPrev() {
        return (bool)$this->_getSiblingProduct('prev');
    }

    public function getNextUrl() {
        $nextProduct = $this->_getSiblingProduct('next');
        return (!is_null($nextProduct)) ? $nextProduct->getProductUrl() : '';
    }

    public function getPrevUrl() {
        $prevProduct = $this->_getSiblingProduct('prev');
        return (!is_null($prevProduct)) ? $prevProduct->getProductUrl() : '';
    }

    public function getNextTitle() {
        $nextProduct = $this->_getSiblingProduct('next');
        return (!is_null($nextProduct)) ? $nextProduct->getName() : '';
    }

    public function getPrevTitle() {
        $prevProduct = $this->_getSiblingProduct('prev');
        return (!is_null($prevProduct)) ? $prevProduct->getName() : '';
    }
    
    
    /**
     * @return Mage_Catalog_Model_Product
     */
    protected function _getSiblingProduct($type) {
        if (!$this->hasSiblingProducts()) {
            $this->setSiblingProducts(array(
                'prev' => $this->_findPrevProduct(),
                'next' => $this->_findNextProduct(),
            ));
        }
        $nearestSiblings = $this->getSiblingProducts();
        return $nearestSiblings[$type];
    }
    
    protected function _findPrevProduct() {
        $collection = $this->_createProductsCollectionWithProductPosition('desc');
        if (is_null($collection)) {
            return null;
        }

        $where = "
            (cat_index.position <= cat_index_for_product.position AND e.entity_id < {$this->getProductId()})
          OR
            (cat_index.position <  cat_index_for_product.position AND e.entity_id != {$this->getProductId()})
        "; 
        return $this->_firstItemWhere($collection, $where);
    }
    
    protected function _findNextProduct() {
        $collection = $this->_createProductsCollectionWithProductPosition('asc');
        if (is_null($collection)) {
            return null;
        }

        $where = "
            (cat_index.position >= cat_index_for_product.position AND e.entity_id > {$this->getProductId()})
          OR
            (cat_index.position >  cat_index_for_product.position AND e.entity_id != {$this->getProductId()})
        ";
        return $this->_firstItemWhere($collection, $where);
    }

    protected function _firstItemWhere(Mage_Catalog_Model_Resource_Product_Collection $collection, $where) {
        $collection->getSelect()->where($where)->limit(1);
        $items = array_values($collection->getItems());
        return isset($items[0]) ? $items[0] : null;
    }
    
    protected function _createProductsCollectionWithProductPosition($orderDir) {
        $collection = $this->_createOrderedProductsCollection($orderDir);

        if (!is_null($collection)) {
            $collection->getSelect()
                ->joinInner(
                    array('cat_index_for_product' => 'catalog_category_product_index'),
                    "(1)
                    AND (cat_index_for_product.product_id={$this->getProductId()})
                    AND (cat_index_for_product.store_id={$this->getStoreId()})
                    AND (cat_index_for_product.visibility IN(" . implode(',', $this->getVisibilityIds()) . "))
                    AND (cat_index_for_product.category_id={$this->getCategory()->getId()})
                ",
                    array()
                );
        }
                    
        return $collection;
    }
    
    /**
     * 
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _createOrderedProductsCollection($orderDir) {
        $collection = $this->_createProductsCollection();

        if (!is_null($collection)) {
            $collection->getSelect()
                ->order("cat_index.position $orderDir")
                ->order("e.entity_id $orderDir")
            ;
        }
        return $collection;
    }

    /**
     * 
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _createProductsCollection() {
        if (!$this->getCategory()) {
            return null;
        }

        return Mage::getModel('catalog/layer')
            ->setCurrentCategory($this->getCategory())
            ->getProductCollection()
        ;
    }
    
    public function getVisibilityIds() {
        if (!$this->hasData('visibility_ids')) {
            $this->setVisibilityIds(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());
        }
        return $this->getData('visibility_ids');
    }

    public function getCrossNavigationImageData() {
        if (!$this->hasData('cross_navigation_image_data')) {
            if (!$this->getCategory()) {
                return array();
            }

            $image = $this->getCategory()->getData('cross_navigation_image');
            if (!$image) {
                return array();
            }
            $this->setCrossNavigationImageData(array(
                'src' =>  Mage::getBaseUrl('media') . 'catalog/category/' . $image,
                'width' => null,
                'height'=> null,
                'title' => $this->getCategory()->getName()
            ));
        }
        return $this->getData('cross_navigation_image_data');
    }
}
