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

 
class Ced_Jet_Model_System_Config_Source_Mapimp extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions($withEmpty = false)
    {
       $options = array();
       $options=array(
 				array('label' =>'','value' => ''),
 				array('label' =>'Jet member savings never applied to product','value' => '103'),
 				array('label' =>'Jet member savings on product only visible to logged in Jet members','value' =>'102'),
 				array('label' =>'no restrictions on product based pricing','value' =>'101'),
		);
		
        return $options;
    }
 
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
 
    public function getOptionText($value)
    {
        $options = $this->getAllOptions(false);
        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
        return false;
    }
}
?>