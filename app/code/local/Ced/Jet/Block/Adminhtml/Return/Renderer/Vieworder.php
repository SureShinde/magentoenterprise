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

class Ced_Jet_Block_Adminhtml_Return_Renderer_Vieworder extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$merchant_order_id = $row->getData('merchant_order_id');
		$order_item_id = $row->getData('order_item_id');

		$collection = Mage::getModel('jet/jetorder')->getCollection();
		$collection->addFieldToFilter('merchant_order_id', $merchant_order_id );
		//$collection->addFieldToFilter('order_item_id', $order_item_id);

		if($collection->count()>0){
			foreach($collection as $coll){
				$magento_order_id=$coll->getData('magento_order_id');
				break;
			}
		}
		$html='';
		if($magento_order_id){
			$order = Mage::getModel('sales/order')->loadByIncrementId($magento_order_id);
			if(sizeof($order)>0){
				$orderId = $order->getId();
			}
			$html = "<a href=". $this->getUrl('adminhtml/sales_order/view',array('order_id'=>$orderId)).">".$magento_order_id."</a>";
		}else{
			$html = "<span><strong> Order Not Found!</strong></span>";
		}
		return $html;

	}
 		
}
?>