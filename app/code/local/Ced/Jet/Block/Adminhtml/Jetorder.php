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

class Ced_Jet_Block_Adminhtml_Jetorder extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		parent::__construct();
		
		$this->_controller = 'adminhtml_jetorder';
		$this->_blockGroup = 'jet';
		$this->_headerText = Mage::helper('jet')->__('Jet Orders Details');
        $this->_addButton("Fetch Directed Cancel Orders", array(
            "label"     => Mage::helper("jet")->__("Fetch Latest Directed Cancel Orders"),
            "onclick"   => "location.href = '".$this->getUrl('adminhtml/adminhtml_jetorder/directedcancel')."';",
            "class"     => "btn btn-danger",
        ));
		$this->_addButton("Fetch Failed Orders", array(
                    "label"     => Mage::helper("jet")->__("Fetch Latest Jet Orders"),
                    "onclick"   => "location.href = '".$this->getUrl('adminhtml/adminhtml_jetorder/fetch')."';",
                    "class"     => "btn btn-danger",
                ));
		$this->removeButton('add');

	}
}
