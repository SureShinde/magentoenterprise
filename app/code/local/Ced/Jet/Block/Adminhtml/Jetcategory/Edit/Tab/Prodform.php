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


class Ced_Jet_Block_Adminhtml_Jetcategory_Edit_Tab_Prodform extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('jet_product',array('legend'=>Mage::helper('jet')->__('Product Information')));
        

        $fieldset->addField('sku', 'text', array(
          'label'     => Mage::helper('jet')->__('Sku'),
          'readonly' => true,
          'name'      => 'sku',
        ));

        $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('jet')->__('Product Title'),
          'readonly' => true,
          'name'      => 'title',
        ));

        $fieldset->addField('description', 'text', array(
          'label'     => Mage::helper('jet')->__('Detail description'),
          'readonly' => true,
          'name'      => 'description',
        ));

        $fieldset->addField('image', 'image', array(
          'label'     => Mage::helper('jet')->__('Image'),
          'name'      => 'image',
        ));


        $fieldset->addField('qty', 'text', array(
          'label'     => Mage::helper('jet')->__('Quantity'),
          'readonly'  => true,
          'name'      => 'qty',
        ));

        $fieldset->addField('price', 'text', array(
          'label'     => Mage::helper('jet')->__('Product Price'),
          'name'      => 'price',
          'readonly'  =>  true,
        ));

        $fieldset->addField('status', 'text', array(
          'label'     => Mage::helper('jet')->__('Status'),
          'name'      => 'status',
          'readonly'  =>  true,
        ));

        
        if(Mage::registry('prod_data'))
        {
          $form->setValues(Mage::registry('prod_data'));
        }
        return parent::_prepareForm();
    }
}
