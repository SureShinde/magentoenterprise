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

class Ced_Jet_Block_Adminhtml_Return_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('returnGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
       
    }
 
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('jet/jetreturn')->getCollection();

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

        $this->addColumn('returnid', array(
          'header'    => Mage::helper('jet')->__('Return Id'),
          'align'     =>'left',
          'index'     => 'returnid',
          'width'     => '50px',
        ));

        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('jet')->__('Magento Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'id',
            'renderer'  => 'Ced_Jet_Block_Adminhtml_Return_Renderer_Vieworder'
        ));

        $this->addColumn('merchant_order_id', array(
          'header'    => Mage::helper('jet')->__('Merchant Order Id'),
          'align'     =>'left',
          'index'     => 'merchant_order_id',
          'width'     => '50px',
        ));

         /*$this->addColumn('order_item_id', array(
          'header'    => Mage::helper('jet')->__('Order Item Id'),
          'align'     =>'left',
          'index'     => 'order_item_id',
          'width'     => '50px',
        ));

          $this->addColumn('qty_returned', array(
          'header'    => Mage::helper('jet')->__('Qty Returned'),
          'align'     =>'left',
          'index'     => 'qty_returned',
          'width'     => '50px',
        ));

          $this->addColumn('qty_refunded', array(
          'header'    => Mage::helper('jet')->__('Qty Refunded'),
          'align'     =>'left',
          'index'     => 'qty_refunded',
          'width'     => '50px',
        ));
          $this->addColumn('return_refundfeedback', array(
          'header'    => Mage::helper('jet')->__('Return Feedback'),
          'align'     =>'left',
          'index'     => 'return_refundfeedback',
          'width'     => '50px',
        ));

        $this->addColumn('agreeto_return', array(
          'header'    => Mage::helper('jet')->__('Agree To Return'),
          'align'     =>'left',
          'index'     => 'agreeto_return',
          'width'     => '50px',
           'type'	  =>'options',
          'options'   => array('1' => 'Yes', '0' => 'No'),
        ));*/
        $this->addColumn('status', array(
          'header'    => Mage::helper('jet')->__('Return Status'),
          'align'     =>'left',
          'index'     => 'status',
          'width'     => '50px',
         
        ));
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('jet')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('jet')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'renderer'  => 'Ced_Jet_Block_Adminhtml_Return_Renderer_Labelaction',
        ));
		
        $this->addExportType('*/*/exportReturnCsv', Mage::helper('jet')->__('CSV'));
        return parent::_prepareColumns();
    }
}