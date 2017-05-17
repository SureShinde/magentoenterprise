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
 
class Magmodules_Snippets_Model_Source_Localbusiness_Days {

	public function toOptionArray() 
	{
		$days = array();
		$days[] = array('value' => 'Monday', 'label'=> Mage::helper('adminhtml')->__('Monday'));
		$days[] = array('value' => 'Tuesday', 'label'=> Mage::helper('adminhtml')->__('Tuesday'));
		$days[] = array('value' => 'Wednesday', 'label'=> Mage::helper('adminhtml')->__('Wednesday'));
		$days[] = array('value' => 'Thursday', 'label'=> Mage::helper('adminhtml')->__('Thursday'));
		$days[] = array('value' => 'Friday', 'label'=> Mage::helper('adminhtml')->__('Friday'));
		$days[] = array('value' => 'Saturday', 'label'=> Mage::helper('adminhtml')->__('Saturday'));
		$days[] = array('value' => 'Sunday', 'label'=> Mage::helper('adminhtml')->__('Sunday'));
		return $days;		
    }
	
}

