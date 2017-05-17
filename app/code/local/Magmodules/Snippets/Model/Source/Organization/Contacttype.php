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
 
class Magmodules_Snippets_Model_Source_Organization_Contacttype {

	public function toOptionArray() 
	{
		$type = array();
		$type[] = array('value' => 'customer service', 'label'=> 'customer service');
		$type[] = array('value' => 'technical support', 'label' => 'technical support');
		$type[] = array('value' => 'billing support', 'label' => 'billing support');
		$type[] = array('value' => 'bill payment', 'label' => 'bill payment');
		$type[] = array('value' => 'sales', 'label' => 'sales');
		$type[] = array('value' => 'reservations', 'label' => 'reservations');
		$type[] = array('value' => 'credit card support', 'label' => 'credit card support');
		$type[] = array('value' => 'emergency', 'label' => 'emergency');
		$type[] = array('value' => 'baggage tracking', 'label' => 'baggage tracking');
		$type[] = array('value' => 'roadside assistance', 'label' => 'roadside assistance');
		$type[] = array('value' => 'package tracking', 'label' => 'package tracking');	
		return $type;		
	}
	
}