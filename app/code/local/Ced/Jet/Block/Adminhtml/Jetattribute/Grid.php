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


class Ced_Jet_Block_Adminhtml_Jetattribute_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	protected function _prepareCollection()
    {
			$data=Mage::getModel('eav/entity_attribute_group')->getCollection()->addFieldToFilter('attribute_group_name','jetcom')->getdata();
			
			$groupid=$data[0]['attribute_group_id'];
			
			$collection = Mage::getResourceModel('catalog/product_attribute_collection')
				->addVisibleFilter()
		   		->setAttributeGroupFilter($groupid);
			  
			$jetAttrTable =	Mage::getSingleton('core/resource')->getTableName('jet/jetattribute');
		
			$jetGroupIds = $collection->getAllIds();
			
			$jetAttr = Mage::getModel('jet/jetattribute')->getCollection();
			$jetAttrIds = $jetAttr->getColumnValues('magento_attr_id');
			$jetAttrIds = array_merge($jetGroupIds, $jetAttrIds);
			
			$newCollection = Mage::getResourceModel('catalog/product_attribute_collection')->addFieldToFilter('main_table.attribute_id',array('in'=>$jetAttrIds));
			
			
			$newCollection->getSelect()->joinLeft(
				array('jet_attr'=>$jetAttrTable),
			   'main_table.attribute_id = jet_attr.magento_attr_id',
				array('jet_attr_id')
			);

				$this->setCollection($newCollection);
			
			
		
        return parent::_prepareCollection();
    }

    /**
     * Prepare product attributes grid columns
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Attribute_Grid
     */
    protected function _prepareColumns()
    {
        Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract::_prepareColumns();
		//Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
       
		$this->addColumnAfter('jet_attr_id', array(
            'header'=>Mage::helper('catalog')->__('Jet Attribute Id'),
            'sortable'=>true,
            'index'=>'jet_attr_id',
            'align' => 'center',
        ), 'frontend_label');
		
        $this->addColumnAfter('is_global', array(
            'header'=>Mage::helper('catalog')->__('Scope'),
            'sortable'=>true,
            'index'=>'is_global',
            'type' => 'options',
            'options' => array(
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE =>Mage::helper('catalog')->__('Store View'),
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>Mage::helper('catalog')->__('Website'),
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL =>Mage::helper('catalog')->__('Global'),
            ),
            'align' => 'center',
        ), 'is_visible');

        $this->addColumn('is_searchable', array(
            'header'=>Mage::helper('catalog')->__('Searchable'),
            'sortable'=>true,
            'index'=>'is_searchable',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
        ), 'is_user_defined');

        $this->addColumnAfter('is_filterable', array(
            'header'=>Mage::helper('catalog')->__('Use in Layered Navigation'),
            'sortable'=>true,
            'index'=>'is_filterable',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('catalog')->__('Filterable (with results)'),
                '2' => Mage::helper('catalog')->__('Filterable (no results)'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
        ), 'is_searchable');

        $this->addColumnAfter('is_comparable', array(
            'header'=>Mage::helper('catalog')->__('Comparable'),
            'sortable'=>true,
            'index'=>'is_comparable',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
        ), 'is_filterable');

        return $this;
    }
	
	/**
     * Return url of given row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/catalog_product_attribute/edit', array('attribute_id' => $row->getAttributeId()));
    }
	
}
