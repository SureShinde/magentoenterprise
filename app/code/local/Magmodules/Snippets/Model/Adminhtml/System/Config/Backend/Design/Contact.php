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

class Magmodules_Snippets_Model_Adminhtml_System_Config_Backend_Design_Contact extends Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array {    

 	protected function _beforeSave() 
 	{
        $value = $this->getValue();
        if(is_array($value)) {
            unset($value['__empty']);
            if(count($value)){            	            	
            	$keys = array();
            	for($i=0; $i < count($value); $i++) {
            		$keys[] = 'availability_' . uniqid();
            	}   
				foreach($value as $key => $field) {
					$value[$key]['telephone'] = $field['telephone'];	
					$value[$key]['contacttype'] = $field['contacttype'];	
					$value[$key]['contactoption'] = $field['contactoption'];	
					$value[$key]['area'] = $field['area'];	
					$value[$key]['languages'] = $field['languages'];	
				}
				$value = array_combine($keys, array_values($value));            	
            }
        }
        $this->setValue($value);
        parent::_beforeSave();
    }
    
}
