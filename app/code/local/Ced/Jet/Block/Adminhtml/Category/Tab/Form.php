<?php
/**
* CedCommerce
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
*
* @category    Ced
* @package     Ced_Jet
* @author      CedCommerce Core Team <connect@cedcommerce.com>
* @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

class Ced_Jet_Block_Adminhtml_Category_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm(){
                $form = new Varien_Data_Form();
                $this->setForm($form);

                $jetUrl = Mage::helper('adminhtml')->getUrl('adminhtml/adminhtml_jetattrlist/categorylist');
                $category_id=Mage::app()->getRequest()->getParam('id');
                $value= Mage::getModel('jet/jetcategory')->getCollection()->addFieldToFilter('magento_cat_id',$category_id)->getFirstItem();
                $jet_mapped_id=$value->getData('jet_cate_id');
                $jet_mapped_id=($jet_mapped_id==0?'':$jet_mapped_id);
                $fieldset = $form->addFieldset('custom_category_tab_form', array('legend'=>Mage::helper('catalog')->__('Jet Category Mapping')));
                $fieldset->addField('custom_tab_text', 'text', array(
                'label'=> Mage::helper('catalog')->__('Jet Category Id'),
                'class' => '',
                'required' => false,
                'name' => 'custom_tab_text',
                'value' => $jet_mapped_id,
                'note' => 'Please Enter correct Jet Category Id , to get Jet Category Id , <a href='.$jetUrl.' target="_blank">click here</a> . Leave blank if don\'t want to accociate it with Jet Category .',
            ));
            return parent::_prepareForm();
    }
}