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

class Ced_Jet_Block_Adminhtml_Rejected extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		
		$this->_controller = 'adminhtml_rejected';
		$this->_blockGroup = 'jet';
		$this->_addButtonLabel = 'Fetch Rejected Files';
		$this->_headerText = Mage::helper('jet')->__('Rejected Jet Files Manager');
		$this->_addButton("Clear All Logs", array(
                    "label"     => Mage::helper("jet")->__("Clear All Logs"),
                    "onclick"   => "location.href = '".$this->getUrl('adminhtml/adminhtml_jetproduct/clearall')."';",
                    "class"     => "btn btn-danger",
                ));
		parent::__construct();

	}
}
