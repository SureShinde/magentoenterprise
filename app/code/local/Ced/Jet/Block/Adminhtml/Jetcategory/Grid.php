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

 

class Ced_Jet_Block_Adminhtml_Jetcategory_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('_jetcategory');
		$this->setDefaultSort('jet_cate_id');
	    $this->setUseAjax(false);
		$this->setSaveParametersInSession(true);
	}
	
	protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
	
	/**
	 * prepare the collection and set for grid
	 */
	
	protected function _prepareCollection()
	{	
		$collection = Mage::getModel('jet/jetcategory')->getCollection();
		
        $this->setCollection($collection);
		
        parent::_prepareCollection();
        return $this;
	}
	
	/**
	 * prepare the column in the grid
	 */
	
	protected function _prepareColumns()
	{
		$this->addColumn('jet_cate_id', array(
				'header'    => Mage::helper('jet')->__('Jet Category Id'),
				'align'     =>'right',
				'width'     => '80px',
				'index'     => 'jet_cate_id',
		));

		$this->addColumn('magento_cat_id', array(
				'header'    => Mage::helper('jet')->__('Magento Category Id'),
				'align'     =>'right',
				'width'     => '80px',
				'index'     => 'magento_cat_id',
		));
		$this->addColumn('magento_category_name', array(
				'header'    => Mage::helper('jet')->__('Magento Category Name'),
				'align'     =>'right',
				'width'     => '80px',
				'index'     => 'magento_cat_id',
				'renderer'  => 'Ced_Jet_Block_Adminhtml_Jetcategory_Renderer_Category',
		));
		
		return parent::_prepareColumns();
		
	}	
}
