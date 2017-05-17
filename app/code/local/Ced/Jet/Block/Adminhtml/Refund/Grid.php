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

class Ced_Jet_Block_Adminhtml_Refund_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('refundGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
       
    }
 
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('jet/jetrefund')->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
          'header'    => Mage::helper('jet')->__('ID'),
          'align'     =>'right',
          'width'     => '10px',
          'index'     => 'id',
        ));

        $this->addColumn('refund_id', array(
          'header'    => Mage::helper('jet')->__('Refund Id'),
          'align'     =>'left',
          'index'     => 'refund_id',
          'width'     => '50px',
        ));

        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('jet')->__('Magento Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'id',
            'renderer'  => 'Ced_Jet_Block_Adminhtml_Refund_Renderer_Vieworder'
        ));

         $this->addColumn('refund_orderid', array(
          'header'    => Mage::helper('jet')->__('Refund Order Id'),
          'align'     =>'left',
          'index'     => 'refund_orderid',
          'width'     => '50px',
        ));

        /* $this->addColumn('refund_merchantid', array(
          'header'    => Mage::helper('jet')->__('Refund Merchant Id'),
          'align'     =>'left',
          'index'     => 'refund_merchantid',
          'width'     => '50px',
        ));*/

        /*$this->addColumn('order_item_id', array(
          'header'    => Mage::helper('jet')->__('Order Item Id'),
          'align'     =>'left',
          'index'     => 'order_item_id',
          'width'     => '50px',
        ));

          $this->addColumn('quantity_returned', array(
          'header'    => Mage::helper('jet')->__('Qty Returned'),
          'align'     =>'left',
          'index'     => 'quantity_returned',
          'width'     => '50px',
        ));

          $this->addColumn('refund_quantity', array(
          'header'    => Mage::helper('jet')->__('Qty Refunded'),
          'align'     =>'left',
          'index'     => 'refund_quantity',
          'width'     => '50px',
        ));

        
        $this->addColumn('refund_amount', array(
          'header'    => Mage::helper('jet')->__('Refund Amount'),
          'align'     =>'left',
          'index'     => 'refund_amount',
          'width'     => '50px',
        ));
          $this->addColumn('refund_shippingcost', array(
          'header'    => Mage::helper('jet')->__('Refund Shipping Cost'),
          'align'     =>'left',
          'index'     => 'refund_shippingcost',
          'width'     => '50px',
        ));
        

        $this->addColumn('refund_reason', array(
          'header'    => Mage::helper('jet')->__('Refund Reason'),
          'align'     =>'left',
          'index'     => 'refund_reason',
          'width'     => '50px',
        ));*/
        $this->addColumn('refund_status', array(
          'header'    => Mage::helper('jet')->__('Refund Status'),
          'align'     =>'left',
          'index'     => 'refund_status',
          'width'     => '50px',
        ));
		
		$this->addColumn('action',
  			array(
  					'header'    =>  Mage::helper('jet')->__('Action'),
  					'width'     => '150',
  					'type'      => 'action',
  					'getter'    => 'getId',
  					'actions'   => array(
  							array(
  									'caption'   => Mage::helper('jet')->__('View Details'),
  									'url'       => array('base'=> '*/*/edit'),
  									'field'     => 'id'
  							)
  					),
  					'filter'    => false,
  					'sortable'  => false,
  					'index'     => 'action',
  					'is_system' => true,
  			));
		    $this->addExportType('*/*/exportRefundCsv', Mage::helper('jet')->__('CSV'));
        return parent::_prepareColumns();
    }
}