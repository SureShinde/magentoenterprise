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

class Ced_Jet_Block_Adminhtml_Attrlist extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
		$this->_controller = 'adminhtml_attrlist';
	    $this->_blockGroup = 'jet';
	    $text="";
	    if($this->getRequest()->getParam('id')){
	    	$id="";
	    	$name="";
	    	$id=$this->getRequest()->getParam('id');
	    	$model="";
	    	$model=Mage::getModel('jet/catlist')->load($id);
	    	$name=$model->getData('name');
	    	$jet_id=$model->getData('csv_cat_id');
	    	$text=" - ".$name." ( jet category id : ".$jet_id." )" ;

	    }
	    $this->_headerText = 'Jet Attribute Listing'.$text;
		
	    parent::__construct();
		
        $this->_removeButton('add');
        $this->setFilterVisibility(false);
					
        $this->addButton('back', array(
            'label'   => $this->__('Back'),
            'onclick' => "setLocation('".$this->getUrl('adminhtml/adminhtml_jetattrlist/categorylist')."')",
            'class'   => 'back',
        ));  
	}
	public function gettitle(){
		$id =$this->getRequest()->getparam(id);
	}
}
