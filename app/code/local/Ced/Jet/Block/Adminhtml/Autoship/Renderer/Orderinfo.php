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

class Ced_Jet_Block_Adminhtml_Autoship_Renderer_Orderinfo extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{


    public function render(Varien_Object $row)
    {
        $order  = Mage::getModel('sales/order')->loadByIncrementId($row->order_id);
        $html='';
        if(sizeof($order)>0){
            $html = "<a href=". $this->getUrl('adminhtml/sales_order/view',array('order_id'=>$order->getId())).">".$row->order_id."</a>";
        }else{
            $html = "<span><strong> Order Not Found!</strong></span>";
        }

        return $html;

    }

}
?>