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


class Ced_Jet_Block_Adminhtml_Failedorders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
	{
		parent::__construct();
		//$this->_removeButton('add');
		$this->setId('_failedorders');
		$this->setDefaultSort('id');
	    $this->setUseAjax(true);
		$this->setSaveParametersInSession(true);
		
		
	}
	
	protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
	
	/**
	 * prepare the collection of gift and set for grid
	 */
	
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('jet/orderimport')->getCollection();
        $this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	/**
	 * prepare the column in the grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('id', array(
				'header'    => Mage::helper('jet')->__('ID'),
				'align'     =>'right',
				'width'     => '80px',
				'index'     => 'id',
		));
		$this->addColumn('reference_number', array(
				'header'    => Mage::helper('jet')->__('Jet Reference Order ID'),
				'align'     =>'left',
				'index'     => 'reference_number',
		));
		$this->addColumn('merchant_order_id', array(
				'header'    => Mage::helper('jet')->__('Merchant order ID'),
				'align'     =>'left',
				'index'     => 'merchant_order_id',
		));
		$this->addColumn('reason',
            array(
                'header'=> Mage::helper('jet')->__('Reason to failed'),
                'align'     =>'left',
				'index' => 'reason',
        ));


		
		
		return parent::_prepareColumns();
		
	}
	
	/**
	 * Delete failed Jet Order's Log
	 */
	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        if (Mage::getSingleton('admin/session')->isAllowed('jet/jetorder/actions/delete')) {
            $this->getMassactionBlock()->addItem('delete', array(
                 'label'=> Mage::helper('jet')->__('Delete'),
                 'url'  => $this->getUrl('adminhtml/adminhtml_jetorder/deletejetorderlog'),
            ));
        }

        

        return $this;
    }
	
	// Used for AJAX loading
	public function getGridUrl()
	{
		return $this->getUrl('*/*/grid', array('_current'=>true));
	}
}
