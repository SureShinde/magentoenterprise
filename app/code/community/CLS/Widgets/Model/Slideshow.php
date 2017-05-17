<?php
/**
 * @category   CLS
 * @package    Widgets
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */
class CLS_Widgets_Model_Slideshow extends Varien_Object
{
    const DEFAULT_PAGER_CSS_CLASS = 'pager-box';
    const DEFAULT_PREV_NEXT_TEXT = '<span class="slide-arrow"></span>';

    protected $_block = null;

    public function __construct($params = array())
    {
        if ( empty($params) || !isset($params['block']) ) {
            Mage::throwException(Mage::helper('cls_paypal')->__('Internal error: Cannot initialize slideshow model.'));
        }

        $this->_block = $params['block'];

        parent::__construct();
    }

    /**
     * Get a unique, sequential index for all slideshows on a page
     *
     * @return int
     */
    public function getSlideshowIndex()
    {
        if (!$this->_block->hasData('slideshow_index')) {
            $index = Mage::registry('cls_widget_slideshow_index');
            if (is_null($index)) {
                $index = 0;
            } else {
                $index++;
            }
            Mage::unregister('cls_widget_slideshow_index');
            Mage::register('cls_widget_slideshow_index', $index);

            $this->_block->setData('slideshow_index', $index);
        }
        return $this->_block->getData('slideshow_index');
    }

    /**
     * Get the text to use for each pager link
     *
     * @return string
     */
    public function getPagerText()
    {
        $text = $this->_block->getData('pager_text');
        $cssClass = (empty($text)) ? ' class="'.self::DEFAULT_PAGER_CSS_CLASS.'"' : '';
        return '<span'.$cssClass.'>'.$text.'</span>';
    }

    /**
     * Get the text to use for the "previous" link
     *
     * @return string
     */
    public function getPrevText()
    {
        $text = $this->_block->getData('prev_text');
        return (!empty($text)) ? '<span>'.$text.'</span>' : self::DEFAULT_PREV_NEXT_TEXT;
    }

    /**
     * Get the text to use for the "next" link
     *
     * @return string
     */
    public function getNextText()
    {
        $text = $this->_block->getData('next_text');
        return (!empty($text)) ? '<span>'.$text.'</span>' : self::DEFAULT_PREV_NEXT_TEXT;
    }
}