<?php
class Ced_Jet_Block_Adminhtml_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
     private $parent;
 
    protected function _prepareLayout()
    {
        //get all existing tabs
        $this->parent = parent::_prepareLayout();
        //add new tab
        $this->addTab('form_section2', array(
          'label'     => Mage::helper('jet')->__('Jet Shipping Exception'),
          'title'     => Mage::helper('jet')->__('Jet Shipping Exception'),
          'content'   => $this->getLayout()->createBlock('jet/adminhtml_prod_edit_tab_shippingform')->toHtml(),
      ));
      $this->addTab('form_section3', array(
          'label'     => Mage::helper('jet')->__('Jet Return Exception'),
          'title'     => Mage::helper('jet')->__('Jet Return Exception'),
          'content'   => $this->getLayout()->createBlock('jet/adminhtml_prod_edit_tab_returnform')->toHtml(),
      ));
        return $this->parent;
    }
}
?>