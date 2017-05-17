<?php
/**
 * @category   CLS
 * @package    Widgets
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */
class CLS_Widgets_Block_Widget_Cms_Slideshow extends Mage_Core_Block_Template
    implements Mage_Widget_Block_Interface
{
    const DEFAULT_PREV_NEXT_TEXT = '<span class="slide-arrow"></span>';

    protected $_slideshowModel = null;

    public function __construct(array $args = array())
    {
        $this->_slideshowModel = Mage::getModel('cls_widgets/slideshow', array('block' => $this));
        parent::__construct($args);
    }

    /**
     * Register the existence of this widget
     *
     * @return Mage_Core_Block_Abstract|void
     */
    protected function _prepareLayout()
    {
        $helper = Mage::helper('cls_widgets/block');
        $helper->registerWidgetType(CLS_Widgets_Helper_Block::TYPE_WIDGET);
        $helper->registerWidgetType(CLS_Widgets_Helper_Block::TYPE_SLIDESHOW);
        return parent::_prepareLayout();
    }

    /**
     * Get the text to use for the "previous" link
     *
     * @return string
     */
    public function getPrevText()
    {
        return $this->_slideshowModel->getPrevText();
    }

    /**
     * Get the text to use for the "next" link
     *
     * @return string
     */
    public function getNextText()
    {
        return $this->_slideshowModel->getNextText();
    }

    /**
     * Retrieve converted to an array and filtered parameter "block_ids"
     *
     * @return array
     */
    public function getBlockIds()
    {
        if (!$this->_getData('block_ids')) {
            $this->setData('block_ids', array(0));
        } elseif (is_string($this->_getData('block_ids'))) {
            $blockIds = explode(',', $this->_getData('block_ids'));
            foreach ($blockIds as $key => $id) {
                $blockIds[$key] = (int)trim($id);
            }
            $this->setData('block_ids', $blockIds);
        }

        return $this->_getData('block_ids');
    }

    /**
     * Get array of the slides
     *
     * @return array
     */
    public function getSlidesContent()
    {
        $content = array();
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();

        $blockCollection = Mage::getModel('cms/block')->getCollection()
            ->addStoreFilter(Mage::app()->getStore(), true)
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('main_table.block_id', array('in' => $this->getBlockIds()));
        foreach ($blockCollection as $block) {
            $content[$block->getId()] = $processor->filter($block->getContent());
        }

        return $content;
    }
}