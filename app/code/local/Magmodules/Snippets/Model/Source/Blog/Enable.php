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
 
class Magmodules_Snippets_Model_Source_Blog_Enable {

	public function toOptionArray() 
	{
		$enable = array();
		$enable[] = array('value' => '', 'label' => Mage::helper('snippets')->__('No'));
		$enable[] = array('value' => '1', 'label' => Mage::helper('snippets')->__('Always'));
		$enable[] = array('value' => '2', 'label' => Mage::helper('snippets')->__('Only when image is found'));
		$enable[] = array('value' => '3', 'label' => Mage::helper('snippets')->__('Only when large image is found'));
		return $enable;		
	}
	
}