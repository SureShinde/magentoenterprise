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

 
class Ced_Jet_Model_System_Config_Source_Taxcode extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions($withEmpty = false)
    {
       $options = array();
 		$options=array(
 				array('label' =>'','value' => ''),
				array('label' =>'Toilet Paper','value' => 'Toilet Paper'),
				array('label'=>'Thermometers','value'=>'Thermometers'),
				array('label'=>'Sweatbands','value'=>'Sweatbands'),
				array('label'=>'SPF Suncare Products','value'=>'SPF Suncare Products'),
				array('label'=>'Sparkling Water','value'=>'Sparkling Water'),
				array('label'=>'Smoking Cessation','value'=>'Smoking Cessation'),
				array('label'=>'Shoe Insoles','value'=>'Shoe Insoles'),
				array('label'=>'Safety Clothing','value'=>'Safety Clothing'),
				array('label'=>'Pet Foods','value'=>'Pet Foods'),
				array('label'=>'Paper Products','value'=>'Paper Products'),
				array('label'=>'OTC Pet Meds','value'=>'OTC Pet Meds'),
				array('label'=>'OTC Medication','value'=>'OTC Medication'),
				array('label'=>'Oral Care Products','value'=>'Oral Care Products'),
				array('label'=>'Non-Motorized Boats','value'=>'Non-Motorized Boats'),
				array('label'=>'Non Taxable Product','value'=>'Non Taxable Product'),
				array('label'=>'Mobility Equipment','value'=>'Mobility Equipment'),
				array('label'=>'Medicated Personal Care Items','value'=>'Medicated Personal Care Items'),
				array('label'=>'Infant Clothing','value'=>'Infant Clothing'),
				array('label'=>'Helmets','value'=>'Helmets'),
				array('label'=>'Handkerchiefs','value'=>'Handkerchiefs'),
				array('label'=>'Generic Taxable Product','value'=>'Generic Taxable Product'),
				array('label'=>'General Grocery Items','value'=>'General Grocery Items'),
				array('label'=>'General Clothing','value'=>'General Clothing'),
				array('label'=>'Fluoride Toothpaste','value'=>'Fluoride Toothpaste'),
				array('label'=>'Durable Medical Equipment','value'=>'Durable Medical Equipment'),
				array('label'=>'Drinks under 50 Percent Juice','value'=>'Drinks under 50 Percent Juice'),
				array('label'=>'Disposable Wipes','value'=>'Disposable Wipes'),
				array('label'=>'Disposable Infant Diapers','value'=>'Disposable Infant Diapers'),
				array('label'=>'Dietary Supplements','value'=>'Dietary Supplements'),
				array('label'=>'Diabetic Supplies','value'=>'Diabetic Supplies'),
				array('label'=>'Costumes','value'=>'Costumes'),
				array('label'=>'Contraceptives','value'=>'Contraceptives'),
				array('label'=>'Contact Lens Solution','value'=>'Contact Lens Solution'),
				array('label'=>'Carbonated Soft Drinks','value'=>'Carbonated Soft Drinks'),
				array('label'=>'Car Seats','value'=>'Car Seats'),
				array('label'=>'Candy with Flour','value'=>'Candy with Flour'),
				array('label'=>'Candy','value'=>'Candy'),
				array('label'=>'Breast Pumps','value'=>'Breast Pumps'),
				array('label'=>'Braces and Supports','value'=>'Braces and Supports'),
				array('label'=>'Bottled Water Plain','value'=>'Bottled Water Plain'),
				array('label'=>'Beverages with 51 to 99 Percent Juice','value'=>'Beverages with 51 to 99 Percent Juice'),
				array('label'=>'Bathing Suits','value'=>'Bathing Suits'),
				array('label'=>'Bandages and First Aid Kits','value'=>'Bandages and First Aid Kits'),
				array('label'=>'Baby Supplies','value'=>'Baby Supplies'),
				array('label'=>'Athletic Clothing','value'=>'Athletic Clothing'),
				array('label'=>'Adult Diapers','value'=>'Adult Diapers'),
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