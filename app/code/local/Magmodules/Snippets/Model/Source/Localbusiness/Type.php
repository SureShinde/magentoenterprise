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
 
class Magmodules_Snippets_Model_Source_Localbusiness_Type {

	public function toOptionArray() 
	{
		$type = array();
		$type[] = array('value' => 'Store', 'label' => 'Store (general)');
		$type[] = array('value' => 'BikeStore', 'label' => 'BikeStore');
		$type[] = array('value' => 'BookStore', 'label' => 'BookStore');
		$type[] = array('value' => 'ClothingStore', 'label' => 'ClothingStore');
		$type[] = array('value' => 'ComputerStore', 'label' => 'ComputerStore');
		$type[] = array('value' => 'ConvenienceStore', 'label' => 'ConvenienceStore');
		$type[] = array('value' => 'DepartmentStore', 'label' => 'DepartmentStore');
		$type[] = array('value' => 'ElectronicsStore', 'label' => 'ElectronicsStore');
		$type[] = array('value' => 'Florist', 'label' => 'Florist');
		$type[] = array('value' => 'FurnitureStore', 'label' => 'FurnitureStore');
		$type[] = array('value' => 'GardenStore', 'label' => 'GardenStore');
		$type[] = array('value' => 'GardenStore', 'label' => 'GardenStore');
		$type[] = array('value' => 'HardwareStore', 'label' => 'HardwareStore');
		$type[] = array('value' => 'HobbyShop', 'label' => 'HobbyShop');
		$type[] = array('value' => 'HomeGoodsStore', 'label' => 'HomeGoodsStore');
		$type[] = array('value' => 'JewelryStore', 'label' => 'JewelryStore');
		$type[] = array('value' => 'LiquorStore', 'label' => 'LiquorStore');
		$type[] = array('value' => 'MensClothingStore', 'label' => 'MensClothingStore');
		$type[] = array('value' => 'MobilePhoneStore', 'label' => 'MobilePhoneStore');
		$type[] = array('value' => 'MovieRentalStore', 'label' => 'MovieRentalStore');
		$type[] = array('value' => 'MusicStore', 'label' => 'MusicStore');
		$type[] = array('value' => 'OfficeEquipmentStore', 'label' => 'OfficeEquipmentStore');
		$type[] = array('value' => 'OutletStore', 'label' => 'OutletStore');
		$type[] = array('value' => 'PawnShop', 'label' => 'PawnShop');
		$type[] = array('value' => 'PetStore', 'label' => 'PetStore');
		$type[] = array('value' => 'ShoeStore', 'label' => 'ShoeStore');
		$type[] = array('value' => 'SportingGoodsStore', 'label' => 'SportingGoodsStore');
		$type[] = array('value' => 'TireShop', 'label' => 'TireShop');
		$type[] = array('value' => 'ToyStore', 'label' => 'ToyStore');
		$type[] = array('value' => 'WholesaleStore', 'label' => 'WholesaleStore');		
		return $type;		
	}
	
}