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

class Ced_Jet_Model_Source_Productstatus extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
  
    public function getAllOptions()
    {
	
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => 'Not Uploaded',
                    'value' => 'not_uploaded'
                ),
             	array(
                    'label' =>'Under Jet Review',
                    'value' =>'under_jet_review'
                ),
				array(
                    'label' => 'Missing Listing Data',
                    'value' =>'missing_listing_data'
                ),
				array(
                    'label' => 'Excluded',
                    'value' =>'excluded'
                ),
				array(
                    'label' => 'Unauthorized',
                    'value' =>'unauthorized'
                ),
				array(
                    'label' => 'Available for Purchase',
                    'value' =>'available_for_purchase'
                ),
				array(
                    'label' => 'Archived',
                    'value' =>'archived'
                ),
            );
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }

   
}
