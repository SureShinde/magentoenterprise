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

abstract class Nucleus_Catalog_Model_Switcher_Abstract extends Varien_Object {
    
    protected $_isEnabledCategoryAttrCode;
    
    public function isEnabled() {
        return $this->getCategory() 
            && $this->_isEnabledForCategory()
            && ($this->hasPrev() || $this->hasNext());
    }
    
    public function getHeadingText() { 
        if ($this->getCrossNavigationImageData() || !$this->getNavigationCategory()) {
            return null;
        }
        return $this->getNavigationCategory()->getName();
    }

    abstract public function getNavigationCategory();
    
    abstract public function hasPrev();
    abstract public function hasNext();
    
    abstract public function getPrevUrl();
    abstract public function getNextUrl();
    
    abstract public function getPrevTitle();
    abstract public function getNextTitle();

    
    protected function _isEnabledForCategory() {
        if (!$this->getCategory()) {
            return false;
        }

        return (bool)$this->getCategory()->getData($this->_isEnabledCategoryAttrCode);
    }   
    
    /**
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory() {
        if (!$this->hasData('category')) {
            if (Mage::registry('current_category')) {
                $this->setCategory(Mage::registry('current_category'));
            } elseif ($this->getProduct()) {
                $this->setCategory($this->getProduct()->getCategory());
            } else {
                $this->setCategory(null);
            }
        }
        return $this->getData('category');
    }
    
    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct() {
        if (!$this->hasData('product')) {
            if (Mage::registry('current_product')) {
                $this->setProduct(Mage::registry('current_product'));
            } else {
                $this->setProduct(null);
            }
        }
        return $this->getData('product');
    }
    
    public function getProductId() {
        if (!$this->getProduct()) {
            return 0;
        }

        return $this->getProduct()->getId();
    }
    
    public function getStoreId() {
        if (!$this->hasData('store_id')) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }
        return $this->getData('store_id');
    }
    
    public function getCrossNavigationImageData() {
        return array();
    }
    
    
}
