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

class Magmodules_Snippets_Block_Adminhtml_Config_Form_Field_Attributes extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {

	protected $_renders = array();

	public function __construct()
    {                
        $layout = Mage::app()->getFrontController()->getAction()->getLayout();
        $renderer_type = $layout->createBlock('snippets/adminhtml_config_form_renderer_select', '', array('is_render_to_js_template' => true));                							                
        $renderer_type->setOptions(Mage::getModel('snippets/adminhtml_system_config_source_attributes')->toOptionArray());
        $renderer_att = $layout->createBlock('snippets/adminhtml_config_form_renderer_select', '', array('is_render_to_js_template' => true));                							                
        $renderer_att->setOptions(Mage::getModel('snippets/source_attributes_all')->toOptionArray());

        $this->addColumn('type', array(
            'label' => Mage::helper('snippets')->__('Type'),
        	'renderer' => $renderer_type,
            'style' => 'width:140px',                   	
        ));
        
        $this->addColumn('source', array(
            'label' => Mage::helper('snippets')->__('Attribute'),
        	'renderer' => $renderer_att,
            'style' => 'width:140px',           
        ));

        $this->_renders['type'] = $renderer_type; 
        $this->_renders['source'] = $renderer_att; 
                                
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('snippets')->__('Add Option');
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