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

class Nucleus_Catalog_Model_Switcher_Category extends Nucleus_Catalog_Model_Switcher_Abstract {
    
    const IS_ENABLED_CATEGORY_ATTR_CODE = 'cross_navigation_categories';
    
    protected $_isEnabledCategoryAttrCode = self::IS_ENABLED_CATEGORY_ATTR_CODE;

    public function getNavigationCategory() {
        if (!$this->getCategory()) {
            return null;
        }
        return $this->getCategory()->getParentCategory();
    }
    
    
    public function hasNext() {
        return (bool)$this->_getSiblingCategory('next');
    }

    public function hasPrev() {
        return (bool)$this->_getSiblingCategory('prev');
    }

    public function getNextUrl() {
        $nextCategory = $this->_getSiblingCategory('next');
        return (!is_null($nextCategory)) ? $nextCategory->getUrl() : '';
    }

    public function getPrevUrl() {
        $prevCategory = $this->_getSiblingCategory('prev');
        return (!is_null($prevCategory)) ? $prevCategory->getUrl() : '';
    }

    public function getNextTitle() {
        $nextCategory = $this->_getSiblingCategory('next');
        return (!is_null($nextCategory)) ? $nextCategory->getName() : '';
    }

    public function getPrevTitle() {
        $prevCategory = $this->_getSiblingCategory('prev');
        return (!is_null($prevCategory)) ? $prevCategory->getName() : '';
    }
    
    
    /**
     * @return Mage_Catalog_Model_Category
     */
    protected function _getSiblingCategory($type) {
        if (!$this->hasSiblingCategories()) {
            $ret = array(
                'prev' => null,
                'next' => null,
            );

            if ($this->getCategory()) {
                $parentId = $this->getCategory()->getParentId();

                $collection = $this->_getChildCategories($parentId);

                $lastCategory = null;
                foreach ($collection as $siblingCategory) {
                    if (!is_null($lastCategory) && $siblingCategory->getId() == $this->getCategory()->getId()) {
                        $ret['prev'] = $lastCategory;
                    } elseif (!is_null($lastCategory) && $lastCategory->getId() == $this->getCategory()->getId()) {
                        $ret['next'] = $siblingCategory;
                    }

                    if (!is_null($ret['prev']) && !is_null($ret['next'])) {
                        break;
                    }

                    $lastCategory = $siblingCategory;
                }
            }
            
            $this->setSiblingCategories($ret);
        }
        
        $nearestSiblings = $this->getSiblingCategories();
        return $nearestSiblings[$type];
    }

    protected function _getChildCategories($parent)
    {
        $tree = Mage::getResourceModel('catalog/category_tree');
        /* @var $tree Mage_Catalog_Model_Resource_Category_Tree */
        $tree->loadNode($parent)
            ->loadChildren(1);

        $tree->addCollectionData(null, 'position', $parent, false);

        return $tree->getCollection();
    }
    
}
