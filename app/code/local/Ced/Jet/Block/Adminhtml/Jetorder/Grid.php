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


class Ced_Jet_Block_Adminhtml_Jetorder_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
    {
        parent::__construct();
        $this->setId('jet_order_grid');
        $this->setUseAjax(false);
        $this->setDefaultSort('magento_order_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'jet/jetorder';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel($this->_getCollectionClass())->getCollection();
		$salesFlatOrder = (string)Mage::getConfig()->getTablePrefix() .'sales_flat_order_grid';
		$jetOrderDetail = (string)Mage::getConfig()->getTablePrefix().'jet_order_detail';
		$collection->getSelect()->joinLeft(array('sales'=>$salesFlatOrder),'main_table.magento_order_id = sales.increment_id',array('sales.billing_name','sales.shipping_name','sales.billing_name','sales.grand_total'));
       	$this->setCollection($collection);

        //var_dump($collection->getData());die;

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
		$this->addColumn('magento_order_id', array(
            'header'=> Mage::helper('jet')->__('Magento Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'magento_order_id',
			'renderer'  => 'Ced_Jet_Block_Adminhtml_Jetorder_Renderer_Vieworder'
        ));
		$this->addColumn('reference_order_id', array(
				'header'    => Mage::helper('jet')->__('Jet Referece Order #'),
				'align'     =>'left',
				'index'     => 'reference_order_id',
		));
		$this->addColumn('merchant_order_id', array(
				'header'    => Mage::helper('jet')->__('Jet Merchant Order Id #'),
				'align'     =>'left',
				'index'     => 'merchant_order_id',
		));
		$this->addColumn('billing_name', array(
				'header'    => Mage::helper('jet')->__('Bill to Name'),
				'align'     =>'left',
				'index'     => 'billing_name',
				'filter_index'=>'sales.billing_name'
		));
		$this->addColumn('shipping_name', array(
				'header'    => Mage::helper('jet')->__('Ship to Name'),
				'align'     =>'left',
				'index'     => 'shipping_name',
				'filter_index'=>'sales.shipping_name'
		));
		$this->addColumn('delivery_by', array(
            'header' => Mage::helper('jet')->__('Delivery By'),
            'index' => 'deliver_by',
            'type' => 'datetime',
            'width' => '100px',
			'filter_index'=>'deliver_by'
        ));
		
		$this->addColumn('status', array(
			'header'    => Mage::helper('jet')->__('Status'),
			'width' 	=> '200px',
			'align'     => 'left',
			'index'     => 'status',
			'type' => 'options',
			 'options' => array('ready'=>'Ready','acknowledged'=>'Acknowledged',
			 					'rejected'=>'Rejected','inprogress'=>'In Progress','complete'=>'Completed','cancelled'=>'Cancelled'), 
			'filter_index'=>'main_table.status',
			'filter_condition_callback' => array($this, '_statusFilter'),					
			
		));
		$this->addColumn('grand_total', array(
            'header' => Mage::helper('jet')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
			'filter_index'=>'sales.grand_total'
        ));
		/*
        $this->addColumn('customer_cancelled', array(
            'header'    => Mage::helper('jet')->__('Order quantity cancelled by customer'),
            'align'     =>'left',
            'index'     => 'customer_cancelled',
            'filter_index'=>'customer_cancelled',
            'renderer'  => 'Ced_Jet_Block_Adminhtml_Jetorder_Renderer_Customercancel'
        ));
       */
	   
		$this->addExportType('*/*/exportCsv', Mage::helper('jet')->__('CSV'));
        
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        if (Mage::getSingleton('admin/session')->isAllowed('jet/jetorder/actions/acknowledge')) {
            $this->getMassactionBlock()->addItem('acknowledged', array(
                 'label'=> Mage::helper('jet')->__('Acknowledged'),
                 'url'  => $this->getUrl('adminhtml/adminhtml_jetorder/massacknowledgeorder'),
            ));
        }

        return $this;
    }

    

    public function getGridUrl()
    {
        //return $this->getUrl('*/*/grid', array('_current'=>true));
    }
	
	protected function _statusFilter($collection, $column) {
		$filterroleid = $column->getFilter()->getValue();        
		if (!$value = $column->getFilter()->getValue()) {
			return $this;
		}        
		$this->getCollection()->addFieldToFilter('main_table.status', array('eq' => $filterroleid));
		return ;
	}
}
