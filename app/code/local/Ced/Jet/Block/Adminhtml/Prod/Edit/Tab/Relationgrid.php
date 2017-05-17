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


class Ced_Jet_Block_Adminhtml_Prod_Edit_Tab_Relationgrid extends Mage_Adminhtml_Block_Widget_Grid
{
   
	public function __construct()
    	{
	  parent::__construct();
	  $id = $this->getRequest()->getParam('id',0);
	  $this->setId('relationshipGrid'.$id);
	  $this->setDefaultSort('created_at');
	  $this->setDefaultDir('DESC');
	  $this->setSaveParametersInSession(true);
	  $this->setUseAjax(true);
	  $id = $this->getRequest()->getParam('id');
	  $product = Mage::getModel('catalog/product')->load($id);
	  $sku = $product->getSku();
		/*If current product is configurable then show the get the lowest child*/
		if($product->getTypeId()=="configurable"){
			$childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
			 foreach($childProducts as $chp){
					$sku = $sProductSku[] = $chp->getSku();
			}
		}
		if($product->getTypeId()=="bundle"){
			$sku = Mage::helper('jet')->getMainProductSku($product);
		}
	    $productStatus = $product->getJetProductStatus();
	    $response = Mage::helper('jet')->CGetRequest('/merchant-skus/'.rawurlencode($sku).'');
	    $result=json_decode($response);
	    $array = get_object_vars($result);
            $children_sku=$array['children_skus'];
	    foreach($children_sku as $data)
	     {
		$relatedsku = get_object_vars($data);
                $skus=$relatedsku['merchant_sku'];
	     }
    	}
	protected function relatedSku(){
	    $result=Mage::registry('relationship');
	    $array = get_object_vars($result);
            $children_sku=$array['children_skus'];
	    $skus=array();
	    foreach($children_sku as $data)
	     {
		$relatedsku = get_object_vars($data);
                $skus[]=$relatedsku['merchant_sku'];
	     }

             return $skus;
	}
	protected function _getStore()
    	{
          $storeId = (int) $this->getRequest()->getParam('store', 0);
          return Mage::app()->getStore($storeId);
    	}
	protected function _prepareCollection()
	{	
		$result=Mage::getModel('jet/jetcategory') ->getCollection()->addFieldToSelect('magento_cat_id');
		$resultdata=array();
		foreach($result as $val){
			$resultdata[]=$val['magento_cat_id'];
		}
		$store = $this->_getStore();
		$collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
	    ->addAttributeToSelect('jet_product_status')->addAttributeToFilter('sku',array('in' => $this->relatedSku()));
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        if ($store->getId()) {
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
           
			$collection->joinAttribute(
                'jet_product_status',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );

			
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
           
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }			
		$collectionData = $collection;
	
		$collectionProd = Mage::getModel('catalog/product')->getCollection();
		$collectionProd->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id =entity_id', null, 'left');
		$collectionProd->addAttributeToSelect('*')
        ->addAttributeToFilter('category_id', array('in' => $resultdata));

		$ids = $collectionProd->getAllIds();
		
		$ids = array_unique($ids);
		
		$collection->addFieldToFilter('entity_id', array('in'=>$ids))
		->addAttributeToFilter('type_id', array('in' => array('simple','configurable','bundle','grouped')));//added condition for config deepak@cedcoss
		$this->setCollection($collection);
		
		return parent::_prepareCollection();

	}
	
	/**
	 * prepare the column in the grid
	 */
	//id,sku,type,price,name,qty,enabled
	protected function _prepareColumns()
	{
		$this->addColumn('sku', array(
				'header'    => Mage::helper('catalog')->__('Sku'),
				'align'     =>'left',
				'index'     => 'sku',
		));
		
		$store = $this->_getStore();
        
		$this->addColumn('name', array(
				'header'    => Mage::helper('catalog')->__('Name'),
				'width' 	=> '200px',
				'align'     => 'left',
				'index'     => 'name',
		));
		
		$this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));
		$jet_state = Mage::getModel('eav/config')->getAttribute('catalog_product', 'jet_product_status');
		$options = $jet_state->getSource()->getAllOptions(false);
		$jetcomstatus = array();
		foreach ($options as $option){
			$jetcomstatus[$option['value']] = $option['label'];
		}
		
		$this->addColumn('jet_product_status',
            array(
                'header'=> Mage::helper('catalog')->__('Jet Product Status'),
                'width' => '60px',
                'index' => 'jet_product_status',
                'type'  => 'options',
                'options' => $jetcomstatus,
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
  									'url'       => array('base'=> 'adminhtml/adminhtml_jetrequest/productDetails'),
  									'field'     => 'id'
  							)
  					),
  					'filter'    => false,
  					'sortable'  => false,
  					'index'     => 'action',
  					'is_system' => true,
  			));

		return parent::_prepareColumns();
		
	}
	
	public function getGridUrl()
        {
    	  return $this->getUrl('*/*/relationgrid', array('_current'=>true));
        }
	
	/**
	 * Remove existing column
	 *
	 * @param string $columnId
	 * @return Mage_Adminhtml_Block_Widget_Grid
	 */
	public function removeColumn($columnId)
	{
		if (isset($this->_columns[$columnId])) {
			unset($this->_columns[$columnId]);
			if ($this->_lastColumnId == $columnId) {
				$this->_lastColumnId = key($this->_columns);
			}
		}
		return $this;
	}
}
