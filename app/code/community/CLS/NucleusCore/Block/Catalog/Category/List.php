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
 * @category   CLS
 * @package    NucleusCore
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */
class CLS_NucleusCore_Block_Catalog_Category_List extends Mage_Catalog_Block_Category_View
{
    protected $_defaultColumnCount = 3;

    protected $_columnCountLayoutDepend = array();

    protected $_categoryCollection = null;

    /**
     * Retrieve category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getCategoryCollection()
    {
        if (is_null($this->_categoryCollection)) {
            $this->_categoryCollection = $this->getCurrentCategory()->getCollection()
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('list_thumbnail')
                ->addAttributeToFilter('is_active', 1)
                ->addIdFilter($this->getCurrentCategory()->getChildren())
                ->setOrder('position', Varien_Db_Select::SQL_ASC)
                ->joinUrlRewrite();
        }

        return $this->_categoryCollection;
    }

    /**
     * Items per row in the category grid
     *
     * @return int
     */
    public function getColumnCount()
    {
        if (!$this->_getData('column_count')) {
            $pageLayout = false;

            $category = Mage::registry('current_category');
            if ($category) {
                $design = Mage::getSingleton('catalog/design');
                $settings = $design->getDesignSettings($category);
                $pageLayoutCode = $settings->getPageLayout();
                $pageLayout = Mage::getSingleton('page/config')->getPageLayout($pageLayoutCode);
            }
            if (!$pageLayout) {
                $pageLayout = $this->helper('page/layout')->getCurrentPageLayout();
            }

            if ($pageLayout && $this->getColumnCountLayoutDepend($pageLayout->getCode())) {
                $this->setData(
                    'column_count',
                    $this->getColumnCountLayoutDepend($pageLayout->getCode())
                );
            } else {
                $this->setData('column_count', $this->_defaultColumnCount);
            }
        }

        return (int) $this->_getData('column_count');
    }

    /**
     * Add row size depends on page layout
     *
     * @param string $pageLayout
     * @param int $columnCount
     * @return Mage_Catalog_Block_Product_List
     */
    public function addColumnCountLayoutDepend($pageLayout, $columnCount)
    {
        $this->_columnCountLayoutDepend[$pageLayout] = $columnCount;
        return $this;
    }

    /**
     * Remove row size depends on page layout
     *
     * @param string $pageLayout
     * @return Mage_Catalog_Block_Product_List
     */
    public function removeColumnCountLayoutDepend($pageLayout)
    {
        if (isset($this->_columnCountLayoutDepend[$pageLayout])) {
            unset($this->_columnCountLayoutDepend[$pageLayout]);
        }

        return $this;
    }

    /**
     * Retrieve row size depends on page layout
     *
     * @param string $pageLayout
     * @return int|boolean
     */
    public function getColumnCountLayoutDepend($pageLayout)
    {
        if (isset($this->_columnCountLayoutDepend[$pageLayout])) {
            return $this->_columnCountLayoutDepend[$pageLayout];
        }

        return false;
    }
}