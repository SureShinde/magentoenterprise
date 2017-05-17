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
 
class Magmodules_Snippets_Model_Source_Rating_Schema {

	public function toOptionArray() 
	{
		$schema = array();
		$schema[] = array('value' => 'LocalBusiness', 'label' => 'LocalBusiness');				
		$schema[] = array('value' => 'Organization', 'label' => 'Organization');				
		$schema[] = array('value' => 'WebPage', 'label' => 'WebPage');				
		return $schema;		
	}
	
}