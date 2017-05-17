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

class Ced_Jet_Block_Adminhtml_Category_Tab_Attribute extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {	

        parent::__construct();
        $this->setId('catalog_category_attributes');
        //$this->setDefaultSort('attribute_id');
        $this->setUseAjax(true);
    }

    public function getCategory()
    {
        return Mage::registry('category');
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_category') {
            $productIds =  0; 
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('attribute_id', array('in'=>$productIds));
            }
            elseif(!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('attribute_id', array('nin'=>$productIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    { 
        $jetAttrTable = Mage::getSingleton('core/resource')->getTableName('jet/jetattribute');
        $id=$this->getCategory()->getId();
        if(isset($id)){
             $attr=  Mage::getModel('jet/jetcategory')->getCollection()->addFieldToFilter('magento_cat_id',$id);

            foreach ($attr as $data) {
                $jets_attr_id=explode(',', $data['jet_attributes']);
            }
            $jetAttrIds=array();
            foreach ($jets_attr_id as $key => $value) {
               $model=Mage::getModel('jet/jetattribute')->getCollection()->addFieldToFilter('jet_attr_id',$value);
                $jet=$model->getData();
                
                foreach ($jet as $data){
                    $magentoattribute= $data['magento_attr_id'];
                        $jetAttrIds[]=$magentoattribute;
                    break;
                }
            }
             $newCollection = Mage::getResourceModel('catalog/product_attribute_collection')->addFieldToFilter('main_table.attribute_id',array('in'=>$jetAttrIds));
             $newCollection->getSelect()->joinLeft(
             array('jet_attr'=>$jetAttrTable),
             'main_table.attribute_id = jet_attr.magento_attr_id',array('jet_attr_id')
             );
             $this->setCollection($newCollection);
        }

        else{
		$data=Mage::getModel('eav/entity_attribute_group')->getCollection()->addFieldToFilter('attribute_group_name','jetcom')->getdata();
				$groupid=$data[0]['attribute_group_id'];
			$collection = Mage::getResourceModel('catalog/product_attribute_collection')
				->addVisibleFilter()
			   ->setAttributeGroupFilter($groupid);
			
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
    }

    protected function _prepareColumns()
    {
       /* if (!$this->getCategory()->getProductsReadonly()) {
            $this->addColumn('in_category', array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_category',
                'align'     => 'center',
                'index'=>'attribute_id',
            ));
        }*/
        $this->addColumn('attribute_id', array(
            'header'    => Mage::helper('jet')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'attribute_id'
        ));
        $this->addColumn('attribute_code', array(
            'header'    => Mage::helper('jet')->__('Attribute Code'),
            'index'     => 'attribute_code'
        ));
        $this->addColumn('frontend_label', array(
            'header'    => Mage::helper('jet')->__('Attribute Label'),
            'width'     => '80',
            'index'     => 'frontend_label'
        ));
       
      	$this->addColumn('jet_attr_id', array(
            'header'=>Mage::helper('catalog')->__('Jet Attribute Id'),
            'sortable'=>true,
            'index'=>'jet_attr_id',
            'align' => 'center',
        ), 'frontend_label');
	
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
	
        return $this->getUrl('adminhtml/adminhtml_jetattr/attrGrid', array('_current'=>true));
    }
	
}

