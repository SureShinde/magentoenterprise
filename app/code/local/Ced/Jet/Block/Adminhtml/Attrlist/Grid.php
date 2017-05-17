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

class Ced_Jet_Block_Adminhtml_Attrlist_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('attrlistGrid');
        $this->setSaveParametersInSession(true);
        $this->setFilterVisibility(false);
        
       
    }
 
    protected function _prepareCollection()
    {
        $collection=Mage::getSingleton('adminhtml/session')->getData('attr_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
          'header'    => Mage::helper('jet')->__('Attribute id on Jet'),
          'align'     =>'left',
          'width'     => '10px',
          'index'     => 'id',
           'filter'=>false,
        ));
        $this->addColumn('magento_attr_id', array(
        		'header'    => Mage::helper('jet')->__('Attribute id on Magento'),
        		'align'     =>'left',
        		'width'     => '10px',
        		'index'     => 'magentoid',
        		'filter'=>false,
        ));
     
        $this->addColumn('name', array(
            'header'=> Mage::helper('jet')->__('Attribute Name'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'name',
            'filter'=>false,
        ));
        $this->addColumn('description', array(
            'header'=> Mage::helper('jet')->__('Description'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'description',
            'filter'=>false,
        ));
        $this->addColumn('status', array(
        		'header'=> Mage::helper('jet')->__('Status'),
        		'width' => '80px',
        		'type'  => 'text',
        		'index' => 'status',
        		'filter'=>false,
        ));
        $this->addColumn('freetext', array(
            'header'=> Mage::helper('jet')->__('Freetext'),
            'width' => '80px',
            //'type'  => 'text',
            'index' => 'freetext',
            'type'    => 'options',
            'options' => $this->getFreetextOptions(),
            'filter'=>false,
        ));
          $this->addColumn('units', array(
          'header'    => Mage::helper('jet')->__('Attribute Units'),
          'align'     =>'left',
          'index'     => 'units',
          'width'     => '50px',
           'filter'=>false,
        ));
         $this->addColumn('attrvalue', array(
          'header'    => Mage::helper('jet')->__('Attribute Value'),
          'align'     =>'left',
          'index'     => 'attrvalue',
          'width'     => '50px',
          'filter'=>false,
        ));
       

        
        return parent::_prepareColumns();
    }
    public function getFreetextOptions(){
          $arr=array();
          $arr[0]='dropdown';
          $arr[1]='text';
          $arr[2]='text with unit';
          return $arr;
    }
     
}