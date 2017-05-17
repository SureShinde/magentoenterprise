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

class Ced_Jet_Block_Adminhtml_Return_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        
		$this->_removeButton('back');
        $this->addButton('back', array(
            'label'   => $this->__('Back'),
            'onclick' => "setLocation('{$this->getUrl('*/*/return')}')",
            'class'   => 'back'
        ));         
        $this->_objectId = 'id';
        $this->_blockGroup = 'jet';
        $this->_controller = 'adminhtml_return';
        $this->_removeButton('delete'); 
        $this->_removeButton('reset');
		if($result=Mage::registry('return_data')){
            if($result['status']=='completed'){
                  $this->_removeButton('save');
            }else{
                $this->_updateButton('save', 'label', Mage::helper('jet')->__('Submit Return'));
            }
           
		}else{
			$this->_updateButton('save', 'label', Mage::helper('jet')->__('Submit Return'));
		}
	}
 
    public function getHeaderText()
    {
        if( Mage::registry('return_data'))
        {
            return 'Return Fields';
            return 'Return Fields'.$this->htmlEscape(
            Mage::registry('return_data')->getTitle()).'<br />';
        }
    }
}