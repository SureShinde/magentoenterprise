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
 
class Magmodules_Snippets_Model_Adminhtml_System_Config_Source_Attributes {
   
    public function toOptionArray() 
    {
		$att = array();
		$att[] = array('value' => 'brand', 'label' => 'Brand');
		$att[] = array('value' => 'color', 'label' => 'Color');
		$att[] = array('value' => 'sku', 'label' => 'SKU');
		$att[] = array('value' => 'model', 'label' => 'Model');
		$att[] = array('value' => 'gtin8', 'label' => 'GTIN-8');
		$att[] = array('value' => 'gtin12', 'label' => 'GTIN-12');
		$att[] = array('value' => 'gtin13', 'label' => 'GTIN-13');
		$att[] = array('value' => 'gtin14', 'label' => 'GTIN-14');
		$att[] = array('value' => 'mpn', 'label' => 'MPN');		
		$att[] = array('value' => 'isbn', 'label' => 'ISBN');				
		return $att;		
    }

} 