<?php
/**
 * Carousel.php
 *
 * @category    CLS
 * @package     Custom
 * @author      Chris Huffman <chris.huffman@classyllama.com>
 * @copyright   Copyright (c) 2015 Classy Llama Studios, LLC
 */

class CLS_Custom_Block_Widget_Catalog_Product_Carousel extends CLS_Widgets_Block_Widget_Catalog_Product_Carousel
{
    protected $_badges;
    protected $_defaultBadgesTemplate = 'nucleus/catalog/badges/badges.phtml';

    /**
     * Add badges block to be accessible in template
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->_badges = $this->getLayout()->createBlock('nucleus_catalog/badges');
        if (!$this->_badges) {
            return $this;
        }
        if ($this->getBadgesTemplate()) {
            $this->_badges->setTemplate($this->getBadgesTemplate());
        } else {
            $this->_badges->setTemplate($this->_defaultBadgesTemplate);
        }
        return $this;
    }

    /**
     * Return badges block instance
     *
     * @return Nucleus_Catalog_Block_Badges
     */
    public function getBadgesBlock()
    {
        return $this->_badges;
    }

}