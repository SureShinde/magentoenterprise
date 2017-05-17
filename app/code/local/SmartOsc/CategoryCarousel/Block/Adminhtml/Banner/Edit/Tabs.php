<?php

/**
 * Banner admin edit tabs
 */
class SmartOsc_CategoryCarousel_Block_Adminhtml_Banner_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('banner_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('smartosc_categorycarousel')->__('Manage'));
    }

    /**
     * before render html
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_banner',
            array(
                'label'   => Mage::helper('smartosc_categorycarousel')->__('Manage category'),
                'title'   => Mage::helper('smartosc_categorycarousel')->__('Manage category'),
                'content' => $this->getLayout()->createBlock(
                    'smartosc_categorycarousel/adminhtml_banner_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve banner entity
     */
    public function getBanner()
    {
        return Mage::registry('current_banner');
    }
}
