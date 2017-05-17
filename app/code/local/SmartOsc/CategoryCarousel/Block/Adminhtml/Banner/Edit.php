<?php

/**
 * Banner admin edit form
 *
 */
class SmartOsc_CategoryCarousel_Block_Adminhtml_Banner_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'smartosc_categorycarousel';
        $this->_controller = 'adminhtml_banner';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('smartosc_categorycarousel')->__('Save Item')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('smartosc_categorycarousel')->__('Delete Item')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('smartosc_categorycarousel')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ),
            -100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * get the edit form header
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_banner') && Mage::registry('current_banner')->getId()) {
            return Mage::helper('smartosc_categorycarousel')->__(
                "Edit Item '%s'",
                $this->escapeHtml(Mage::registry('current_banner')->getTitle())
            );
        } else {
            return Mage::helper('smartosc_categorycarousel')->__('Add Item');
        }
    }
}
