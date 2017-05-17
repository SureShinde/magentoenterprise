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

class Ced_Jet_Model_System_Config_Source_Store
{
    public function toOptionArray()
    {
		
		$complete_opt = array();
		$webcall = Mage::app()->getWebsites();
		
		  foreach ($webcall as $website) {
		   
			foreach ($website->getGroups() as $group) {
			
			 $stores = $group->getStores();
			
			 foreach ($stores as $store) {
			 	$arr = array();
				$arr['value'] = $store->getId();
				$arr['label'] = $store->getName();
				$complete_opt[]= $arr;
			 }
			 
			}
		    
		  }
		 
        return $complete_opt;
		
    }
	
}