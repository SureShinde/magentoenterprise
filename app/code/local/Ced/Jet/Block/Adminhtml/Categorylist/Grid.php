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

class Ced_Jet_Block_Adminhtml_Categorylist_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('categorylistGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
       
    }
 
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('jet/catlist')->getCollection();
        // $quickTable = Mage::getSingleton('core/resource')->getTableName('jet/jetcategory');
        //$collection->getSelect()->joinLeft(array('quick_table' => $quickTable), "main_table.csv_cat_id = quick_table.jet_cate_id", array('*'));
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

        $this->addColumn('csv_cat_id', array(
          'header'    => Mage::helper('jet')->__('Jet Category Id'),
          'align'     =>'left',
          'index'     => 'csv_cat_id',
          'width'     => '50px',
          'type'  => 'text',
        ));

        $this->addColumn('magento_id', array(
            'header'    => Mage::helper('jet')->__('Magento Category Id'),
            'align'     =>'left',
            'index'     => 'id',
            'width'     => '50px',
            'renderer'  => 'Ced_Jet_Block_Adminhtml_Categorylist_Renderer_Identifier',
            'filter'=>false,
         ));
        $this->addColumn('name', array(
            'header'=> Mage::helper('jet')->__('Jet Category Name'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'name',
           
        ));

         $this->addColumn('csv_parent_id', array(
          'header'    => Mage::helper('jet')->__('Jet Parent Category Id'),
          'align'     =>'left',
          'index'     => 'csv_parent_id',
          'width'     => '50px',
        ));

        /*$this->addColumn('path', array(
          'header'    => Mage::helper('jet')->__('Path'),
          'align'     =>'left',
          'index'     => 'path',
          'width'     => '50px',
        ));*/
		
		$this->addColumn('jetattr_names', array(
          'header'    => Mage::helper('jet')->__('Jet Attributes'),
          'align'     =>'left',
          'index'     => 'jetattr_names',
          'width'     => '50px',
        ));
         
         $this->addColumn('status', array(
         		'header'    => Mage::helper('jet')->__('Status'),
         		'align'     =>'left',
         		'index'     => 'id',
         		'width'     => '50px',
         		'renderer'  => 'Ced_Jet_Block_Adminhtml_Categorylist_Renderer_Status',
         		//'filter'=>false,
         		'filter_condition_callback' => array($this, '_statusFilter'),
         ));
          
        
		
		  $this->addColumn('action',
  			array(
  					'header'    =>  Mage::helper('jet')->__('Action'),
  					'width'     => '150',
  					'type'      => 'action',
  					'getter'    => 'getId',
  					'actions'   => array(
  							array(
  									'caption'   => Mage::helper('jet')->__('View Attribute Details'),
  									'url'       => array('base'=> '*/*/edit'),
  									'field'     => 'id'
  							)
  					),
  					'filter'    => false,
  					'sortable'  => false,
  					'index'     => 'action',
  					'is_system' => true,
  			));
		    //$this->addExportType('*/*/exportRefundCsv', Mage::helper('jet')->__('CSV'));
        return parent::_prepareColumns();
    }
	/*
	* Creating Jet category is depericated now
	* only Categgory mapping is allowed
	*/
	/*
    protected function _prepareMassaction(){
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->addItem('create', array(
            'label'=> Mage::helper('jet')->__('Create Category'),
            'url'  => $this->getUrl('adminhtml/adminhtml_jetattrlist/masscreate'),
        ));
        return $this;
    } 
	*/ 
    protected function _statusFilter($collection, $column){
    	if (!$value = $column->getFilter()->getValue()) {
    		return $this;
    	}
    	$arr=array();
    	$value=trim($value);
    	if($value == "Created"){
    			$collection=Mage::getModel('jet/jetcategory')->getCollection();
    			foreach($collection as $coll){
    						if($coll->getData('magento_cat_id')!=""){
    							$val="";
    							$val=$coll->getData('jet_cate_id');
    							$val=trim($val);
    							$arr[]=$val;
    						}
    			}
    	}
    	elseif($value == "Not Created"){
    		$arr1=array();
    		$arr2=array();
    		$new_arr=array();
    		$collection=Mage::getModel('jet/jetcategory')->getCollection();
    		foreach($collection as $coll){
    			if($coll->getData('magento_cat_id')!=""){
    				$val="";
    				$val=$coll->getData('jet_cate_id');
    				$val=trim($val);
    				$arr1[]=$val;
    			}
    		}
    		$collection1=Mage::getModel('jet/catlist')->getCollection();
    		foreach($collection1 as $coll1){
    			if($coll1->getData('csv_cat_id')!=""){
    				$val="";
    				$val=$coll1->getData('csv_cat_id');
    				$val=trim($val);
    				$arr2[]=$val;
    			}
    		}
    		$arr=array_diff($arr2, $arr1);
    		
    	}
    	$this->getCollection()->addFieldToFilter('csv_cat_id', array('in' => $arr));
    	return $this;
    }
}