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

class Ced_Jet_Model_Source_Pricetype
{
	public function toOptionArray()
    {
			
        
            $_options = array(
                array(
                    'label' => 'Default Magento Price',
                    'value' => 'final_price'
                ),
             	array(
                    'label' =>'Add % Percent on Magento Price',
                    'value' =>'plus_percent'
                ),
				array(
                    'label' => 'Off % Percent on Magento Price',
                    'value' =>'off_percent'
                ),
				array(
                    'label' => 'Custom Fixed Price',
                    'value' =>'fixed'
                ),
			);
        return $_options;
    }
	
}