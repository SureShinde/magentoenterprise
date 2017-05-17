<?php
/**
 * @category   CLS
 * @package    Widgets
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */
class CLS_Widgets_Block_Widget_Cms_Slideshow_Banners extends Enterprise_Banner_Block_Widget_Banner
{
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
     * Get array of the slides
     *
     * @return array
     */
    public function getSlidesContent()
    {
        return $this->getBannersContent();
    }

    /**
     * Retrieve right rotation mode or return null
     *
     * @return string|null
     */
    public function getRotate()
    {
        if (!array_key_exists('rotate', $this->_data)) {
            $this->setData(self::BANNER_WIDGET_RORATE_NONE);
        }
        return parent::getRotate();
    }
}