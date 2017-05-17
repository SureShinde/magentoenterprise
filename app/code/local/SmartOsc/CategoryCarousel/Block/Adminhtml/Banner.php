<?php

/**
 * Banner admin block
 */
class SmartOsc_CategoryCarousel_Block_Adminhtml_Banner extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * constructor
     *
     */
    public function __construct()
    {
        $this->_controller         = 'adminhtml_banner';
        $this->_blockGroup         = 'smartosc_categorycarousel';
        parent::__construct();
        $this->_headerText         = Mage::helper('smartosc_categorycarousel')->__('Manage Category Items');
        $this->_updateButton('add', 'label', Mage::helper('smartosc_categorycarousel')->__('Add Items'));

    }
}
