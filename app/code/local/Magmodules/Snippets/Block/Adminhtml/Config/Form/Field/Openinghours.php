<?php
/**
 * Magmodules.eu - http://www.magmodules.eu - info@magmodules.eu
 * =============================================================
 * NOTICE OF LICENSE [Single domain license]
 * This source file is subject to the EULA that is
 * available through the world-wide-web at:
 * http://www.magmodules.eu/license-agreement/
 * =============================================================
 * @category    Magmodules
 * @package     Magmodules_Snippets
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2016 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */

class Magmodules_Snippets_Block_Adminhtml_Config_Form_Field_Openinghours extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {

	protected $_renders = array();

	public function __construct()
    {                
        $layout = Mage::app()->getFrontController()->getAction()->getLayout();
        $renderer_days = $layout->createBlock('snippets/adminhtml_config_form_renderer_select', '', array('is_render_to_js_template' => true));                							                
        $renderer_days->setOptions(Mage::getModel('snippets/source_Localbusiness_Days')->toOptionArray());

        $this->addColumn('day', array(
            'label' => Mage::helper('snippets')->__('Day'),
            'style' => 'width:140px',           
        	'renderer' => $renderer_days
        ));
        
        $this->addColumn('from', array(
            'label' => Mage::helper('snippets')->__('Opening Time'),
            'style' => 'width:70px',            
        ));

        $this->addColumn('to', array(
            'label' => Mage::helper('snippets')->__('Closing Time'),
            'style' => 'width:70px',            
        ));
                
        $this->_renders['day'] = $renderer_days; 
                                
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('snippets')->__('Add new');
        parent::__construct();
    }

	protected function _prepareArrayRow(Varien_Object $row)
    {    	
    	foreach ($this->_renders as $key => $render){
	        $row->setData(
	            'option_extra_attr_' . $render->calcOptionHash($row->getData($key)),
	            'selected="selected"'
	        );
    	}
    } 

}