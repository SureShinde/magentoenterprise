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

class Ced_Jet_Model_Carrier_Shipjetcom 
	extends Mage_Shipping_Model_Carrier_Abstract 
	implements Mage_Shipping_Model_Carrier_Interface {

	protected $_code = 'shipjetcom';

	/**
	 * Collect rates for this shipping method based on information in $request
	 *
	 * @param Mage_Shipping_Model_Rate_Request $request
	 * @return Mage_Shipping_Model_Rate_Result
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
			return false;
		}
		if(Mage::getDesign()->getArea() != Mage_Core_Model_App_Area::AREA_ADMINHTML){
				return false;
		}
		
		$price = $this->getConfigData('price');
		// set a default shipping price maybe 0
		if(!$price)
			$price = 0;
			
		$handling = Mage::getStoreConfig('carriers/'.$this->_code.'/handling');
		$result = Mage::getModel('shipping/rate_result');
		
		$method = Mage::getModel('shipping/rate_result_method');
		$method->setCarrier($this->_code);
		$method->setMethod($this->_code);
		
		$method->setCarrierTitle($this->getConfigData('title'));
		$method->setMethodTitle($this->getConfigData('name'));
		
		$method->setPrice($price);
		$method->setCost(0);
		
		$result->append($method);		
		return $result;		
	}
	
	public function getAllowedMethods() {
		return array($this->_code => $this->getConfigData('title'));
	}
	
	public function getCode(){
		return $this->_code;
	}
}
