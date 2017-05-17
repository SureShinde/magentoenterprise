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


class Ced_Jet_Block_Adminhtml_Prod_Edit_Tab_Prodform extends Mage_Adminhtml_Block_Widget_Form
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

        $fieldset->addField('description', 'textarea', array(
          'label'     => Mage::helper('jet')->__('Detail description'),
          'readonly' => true,
          'name'      => 'description',
        ));
		
		$fieldset->addField('merchant_id', 'text', array(
          'label'     => Mage::helper('jet')->__('Merchant Id'),
          'readonly' => true,
          'name'      => 'merchant_id',
        ));
		$fieldset->addField('merchant_sku_id', 'text', array(
          'label'     => Mage::helper('jet')->__('Merchant Sku Id'),
          'readonly' => true,
          'name'      => 'merchant_sku_id',
        ));
		$fieldset->addField('multipack_quantity', 'text', array(
          'label'     => Mage::helper('jet')->__('Multipack Quantity'),
          'readonly' => true,
          'name'      => 'multipack_quantity',
        ));
		$fieldset->addField('sku_last_update', 'text', array(
          'label'     => Mage::helper('jet')->__('Sku Last Update Date'),
          'readonly' => true,
          'name'      => 'sku_last_update',
        ));
		
		$fieldset->addField('inventory_last_update', 'text', array(
          'label'     => Mage::helper('jet')->__('Inventory Last Update Date'),
          'readonly' => true,
          'name'      => 'inventory_last_update',
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
		
		$fieldset->addType('fulfillment_price', 'Ced_Jet_Block_Adminhtml_Prod_Edit_Renderer_Price');     
		$fieldset->addField('fulfillment_price', 'fulfillment_price', array(
			'name'      => 'fulfillment_price',
			'label'     => Mage::helper('jet')->__('Fulfillment Price'),
		));
		
		
		$fieldset->addType('fulfillment_qty', 'Ced_Jet_Block_Adminhtml_Prod_Edit_Renderer_Inventory');     
		$fieldset->addField('fulfillment_qty', 'fulfillment_qty', array(
			'name'      => 'fulfillment_qty',
			'label'     => Mage::helper('jet')->__('Fulfillment Inventory'),
		));
		
        $fieldset->addField('status', 'text', array(
          'label'     => Mage::helper('jet')->__('Status'),
          'name'      => 'status',
          'readonly'  =>  true,
        ));
		
		$fieldset->addField('sub_status', 'textarea', array(
          'label'     => Mage::helper('jet')->__('Sub Status'),
          'name'      => 'sub_status',
          'readonly'  =>  true,
        ));
		$fieldset->addField('relationship', 'text', array(
          'label'     => Mage::helper('jet')->__('Relationship'),
          'name'      => 'relationship',
          'readonly'  =>  true,
        ));
		$fieldset->addField('variation_refinements', 'text', array(
          'label'     => Mage::helper('jet')->__('variation Refinements'),
          'name'      => 'variation_refinements',
          'readonly'  =>  true,
        ));
        
		 $fieldset->addField('manufacturer', 'text', array(
		  'label'     => Mage::helper('jet')->__('Manufacturer'),
		  'name'      => 'manufacturer',
		  'readonly'  =>  true,

        ));
		$fieldset->addField('brand', 'text', array(
		  'label'     => Mage::helper('jet')->__('Brand'),
		  'name'      => 'brand',
		  'readonly'  =>  true,

        ));
		
		$fieldset->addField('main_image_url', 'text', array(
			'name'      => 'main_image_url',
			'label'     => Mage::helper('jet')->__('Product Image URL'),
			'readonly'  =>  true,
		));
		
        if(Mage::registry('prod_data'))
        {
          $form->setValues(Mage::registry('prod_data'));
        }
        return parent::_prepareForm();
    }
}
