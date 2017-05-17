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


class Ced_Jet_Block_Adminhtml_Prod extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$ur = Mage::helper('adminhtml')->getUrl('*/');
		$zz = array();
		$zz = explode('index/index',$ur);
		
		$this->_controller = 'adminhtml_prod';
		$this->_blockGroup = 'jet';
		$this->_headerText = Mage::helper('jet')->__('Product Manager');
		$this->_addButtonLabel = 'Sync Jet Product Status';
		
		 $this->_addButton("Upload All Product", array(
            "label"     => Mage::helper("jet")->__("Upload All Product"),
             "onclick"   => "saveTrigger('".$zz[0]."')",
            "class"     => "btn btn-danger",
        ));
		$this->_addButton("Sync Inventory & Price", array(
            "label"     => Mage::helper("jet")->__("Sync Inventory & Price"),
            "onclick"   => "location.href = '".$this->getUrl('adminhtml/adminhtml_jetajax/sync')."';",
            "class"     => "btn btn-danger",
        ));
       
		
	   	parent::__construct();

	}
}
