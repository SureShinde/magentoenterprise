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

class Ced_Jet_Model_Jetshipping extends Mage_Shipping_Model_Shipping {
    public function collectCarrierRates($carrierCode, $request) {
        if (!$this -> _checkCarrierAvailability($carrierCode, $request)) {
            return $this;
        }
        return
        parent::collectCarrierRates($carrierCode, $request);
    }
    protected function _checkCarrierAvailability($carrierCode, $request = null) {
		if ($carrierCode == 'shipjetcom') {
			if(Mage::getDesign()->getArea() == Mage_Core_Model_App_Area::AREA_ADMINHTML){
				return true;
			}else{
				return false;
			}
        }
        return true;
    }
}