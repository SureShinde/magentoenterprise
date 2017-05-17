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

class Ced_Jet_Block_Adminhtml_Rejectorder_Form_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
		
		
		$jet_order = Mage::registry('current_jetorder');
		//print_r($jet_order); die;
			
		$orderid=$this->getRequest()->getParam('order_id');
		$Incrementid=$this->getRequest()->getParam('increment_id');
		
	
		 $form = new Varien_Data_Form(array(
			'id' => 'edit_form',
			'action' => $this->getUrl('adminhtml/adminhtml_jetorder/reject', array('id' => $this->getRequest()->getParam('id'))),
			'method' => 'post',
            //'enctype' => 'multipart/form-data',
        ));
		
		 $form->setUseContainer(true);
 
 	     $this->setForm($form);
		 
		 $fieldset = $form->addFieldset('order_reject_form', array(
             'legend' =>Mage::helper('jet')->__('Jet.com Order Reject Reason Form')
        ));
 
 		$fieldset->addField('order_id', 'hidden', array(
             'label'     => Mage::helper('jet')->__('Order Id'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'order_id',
			 'readonly' => true,
			 'value'    => $orderid,
            // 'note'     => Mage::helper('jet')->__('The name of the example.'),
        ));	
 		
		$fieldset->addField('increment_id', 'text', array(
             'label'     => Mage::helper('jet')->__('Order Increment Id'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'increment_id',
			 'readonly' => true,
			 'value'    => $Incrementid,
            // 'note'     => Mage::helper('jet')->__('The name of the example.'),
        ));
		
		$fieldset->addField('merchant_order_id', 'text', array(
             'label'     => Mage::helper('jet')->__('Jet.com Order Id'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'merchant_order_id',
			 'readonly' => true,
			 'value'    =>$jet_order->merchant_order_id 
            // 'note'     => Mage::helper('jet')->__('The name of the example.'),
        ));
		
		$fieldset->addField('reference_order_id', 'text', array(
             'label'     => Mage::helper('jet')->__('Jet.com Reference Order Id'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'reference_order_id',
			 'readonly' => true,
			 'value'    =>$jet_order->reference_order_id 
            // 'note'     => Mage::helper('jet')->__('The name of the example.'),
        ));
		//reference_order_id
		if($jet_order->order_detail->request_shipping_carrier!==""){
				$fieldset->addField('request_shipping_carrier', 'text', array(
				 'label'     => Mage::helper('jet')->__('Request Shipping carrier'),
				 'class'     => 'required-entry',
				 'required'  => true,
				 'name'      => 'request_shipping_carrier',
				 'readonly' => true,
				 'value'    =>$jet_order->order_detail->request_shipping_carrier
			));
		}
		
		
		$fieldset->addField('acknowledgement_status', 'select', array(
			'label' => Mage::helper('jet')->__('Acknowledgement Status'),
			//'title' => Mage::helper('advertisement')->__('Media'),
			'name' => 'acknowledgement_status',
			'required' => true,
			'options' => array(
				''  => 'Select Acknowledgement Type',
				'rejected_item_error'  => ' Item Level Error - The error occurred at the item level',
				'rejected_shiploc_error'  =>' Ship from location not available - The ship to location is invalid',
				'rejected_shipmeth_error'  =>' Shipping method not supported - The address requested cannot be shipped to',
				'rejected_addr_error'  =>' Unfulfillable address - The address requested cannot be shipped to',
				'accepted' =>' Accepted -All items in the order will be shipped',
			),
		));
		
		foreach($jet_order->order_items as $k=>$valdata){ 
		
			$fieldset->addField('product_title_'.$k, 'text', array(
             'label'     => Mage::helper('jet')->__('Jet.com Product Title'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'product_title[]',
			 'readonly' => true,
			 'value'    =>$valdata->product_title 
           ));
		   
		   $fieldset->addField('merchant_sku_'.$k, 'text', array(
             'label'     => Mage::helper('jet')->__('Jet.com Product SKU'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'merchant_sku[]',
			 'readonly' => true,
			 'value'    =>$valdata->merchant_sku 
           ));
		   
		   $fieldset->addField('order_item_id_'.$k, 'text', array(
             'label'     => Mage::helper('jet')->__('Jet.com Order item Id'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'order_item_id[]',
			 'readonly' => true,
			 'value'    =>$valdata->order_item_id 
           ));
		   
		   $fieldset->addField('request_order_quantity'.$k, 'text', array(
             'label'     => Mage::helper('jet')->__('Jet.com Product Quantity'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'request_order_quantity[]',
			 'readonly' => true,
			 'value'    =>$valdata->request_order_quantity 
           ));
			
			$fieldset->addField('order_item_acknowledgement_status_'.$k, 'select', array(
				'label' => Mage::helper('jet')->__('SKU '.$valdata->merchant_sku.' Acknowledgement Status'),
				'name' => 'order_item_acknowledgement_status[]',
				'required' => true,
				'options' => array(
					''  => 'Select Acknowledgement Type',
					'nonfulfillable_skuerr'  => ' Invalid merchant SKU - This is not recognized as a valid value',
					'nonfulfillable_inven_err'  =>' No inventory - No inventory for this product is available',
					'fulfillable'  =>' Fulfillable - The item in the order can be shipped',
				),
			));
		}
		
		
		
        return parent::_prepareForm();
    }
}