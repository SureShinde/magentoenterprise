<?php
/**
 * CedCommerce
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Cedcommerce.com license and  can be accessed 
 * through the world-wide-web at this URL:
 * http://www.cedcommerce.com/license-agreement.txt
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to the file if you wish to upgrade this extension to newer
 * version in the future.
 */
class Ced_Jet_Model_Jetorder extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		$this->_init('jet/jetorder');
	}

}