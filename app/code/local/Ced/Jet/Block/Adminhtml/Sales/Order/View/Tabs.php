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
 
class Ced_Jet_Block_Adminhtml_Sales_Order_View_Tabs extends Mage_Adminhtml_Block_Sales_Order_View_Tabs{
	
	protected function _beforeToHtml()
	{
		$data= Mage::registry('current_order')->getData();
	
		$order = Mage::getModel('sales/order')->load($data['entity_id']);
    	$Incrementid = $order->getIncrementId();
    		
    	$orderdata=Mage::getModel('jet/jetorder')->getCollection()->addFieldToFilter('magento_order_id',$Incrementid)->getData();
    	if($orderdata){
			$this->addTab('Ship_By_Jet', array(
				'label'     => Mage::helper('jet')->__('Ship By  Jet'),
				'title'     => Mage::helper('jet')->__('Ship By Jet'),
				'content'   => $this->getLayout()->createBlock('core/template')->setTemplate('ced/jet/order/view/tab/custom_tab.phtml')->toHtml(),
			));
	  
		}
      return parent::_beforeToHtml();
	}
}
