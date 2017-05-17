<?php
/**
 * @category   CLS
 * @package    Widgets
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */
class CLS_Widgets_Block_Widget_Catalog_Product_Carousel extends Mage_Catalog_Block_Product_Abstract
    implements Mage_Widget_Block_Interface
{
    const WIDGET_OPTION_SORT_VAL_POSITION = 'position';
    const WIDGET_OPTION_SORT_VAL_RAND = 'random';

    protected $_productCollection = null;

    /**
     * Register the existence of this widget
     *
     * @return Mage_Core_Block_Abstract|void
     */
    protected function _prepareLayout()
    {
        $helper = Mage::helper('cls_widgets/block');
        $helper->registerWidgetType(CLS_Widgets_Helper_Block::TYPE_WIDGET);
        $helper->registerWidgetType(CLS_Widgets_Helper_Block::TYPE_PRODUCT_CAROUSEL);
        return parent::_prepareLayout();
    }

    /**
     * Get the ID of the category that was assigned
     *
     * @return string
     */
    public function getCategoryId()
    {
        if (!$this->hasData('category_id')) {
            $id = null;
            if ($idPath = $this->getIdPath()) {
                $idPathParts = explode('/', $idPath);
                if (count($idPathParts) > 1) {
                    $id = $idPathParts[1];
                }
            }
            $this->setData('category_id', $id);
        }
        return $this->getData('category_id');
    }

    /**
     * Retrieve category product collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $this->_productCollection = false;

            $categoryId = $this->getCategoryId();
            if (!is_null($categoryId) && ($category = Mage::getModel('catalog/category')->load($categoryId))) {
                $this->_productCollection = $category->getProductCollection()
                    ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                    ->addMinimalPrice()
                    ->addFinalPrice()
                    ->addUrlRewrite($category->getId());

                Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);
                Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_productCollection);

                $limit = (int) $this->getItemLimit();
                if ($limit > 0) {
                    $this->_productCollection->setPageSize($limit);
                }

                if ($this->getSortOrder() == self::WIDGET_OPTION_SORT_VAL_RAND) {
                    $this->_productCollection->getSelect()
                        ->setPart(Zend_Db_Select::ORDER, array())
                        ->orderRand();
                }
            }
        }

        return $this->_productCollection;
    }

    /**
     * Width and height of the image in each item. The logic assumes that the carousel is being inserted into a single
     * column page and generates an image size that will display at 1:1 ratio on large viewports.
     *
     * @return int
     */
    public function getItemImgSize()
    {
        if (!$this->hasData('item_img_size')) {
            // ((Page container width - (prev/next padding * 2)) / items in carousel) - padding on carousel items)
            $imgSize = (((1200 - (30 * 2)) / (int) $this->getItemsPerSlide()) - 20);
            $this->setData('item_img_size', $imgSize);
        }
        return $this->getData('item_img_size');
    }

    /**
     * Whether prev/next links should be shown
     *
     * @return bool
     */
    public function getIncludePrevNext()
    {
        $include = $this->getData('include_prev_next');
        if (!$include && !$this->getAutostart() && !$this->getIncludePager()) {
            $include = true;
        }
        return (bool) $include;
    }
}