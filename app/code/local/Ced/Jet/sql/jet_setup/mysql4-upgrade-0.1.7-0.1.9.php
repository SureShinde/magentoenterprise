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
 
$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();


if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','jet_brand')->getId()) {
	$setup->addAttribute('catalog_product', 'jet_brand', array(
	 'group'      => 'jetcom',
	 'note'	      =>'1 to 50 characters',
	 'input'         => 'text',
	 'type'          => 'text',
	 'label'         => 'Brand',
	 'backend'       => '',
	 'visible'       => 1,
	 'required'  => 0,
	 'user_defined' => 1,
	 'searchable' => 1,
	 'filterable' => 0,
	 'comparable' => 0,
	 'visible_on_front' => 0,
	 'visible_in_advanced_search'  => 0,
	 'is_html_allowed_on_front' => 0,
		'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));
}


if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','mfr_part_number')->getId()) {
	$setup->addAttribute('catalog_product', 'mfr_part_number', array(
	 'group'      => 'jetcom',
	 'note'	   =>'Part number provided by the original manufacturer of the merchant SKU
	',
	 'input'         => 'text',
	 'type'          => 'text',
	 'label'         => 'Manufacturer part number',
	 'backend'       => '',
	 'visible'       => 1,
	 'required'  => 0,
	 'user_defined' => 1,
	 'searchable' => 1,
	 'filterable' => 0,
	 'comparable' => 0,
	 'visible_on_front' => 0,
	 'visible_in_advanced_search'  => 0,
	 'is_html_allowed_on_front' => 0,
		'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));

}

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','isbn-13')->getId()) {

	$setup->addAttribute('catalog_product', 'isbn-13', array(
	 'group'      => 'jetcom',
	 'note'	   =>'If standard_product_code_type is "ISBN-13" - must be 13 digits',	
	 'input'         => 'text',
	 'type'          => 'text',
	 'label'         => 'ISBN-13',
	 'backend'       => '',
	 'visible'       => 1,
	 'required'  => 0,
	 'user_defined' => 1,
	 'searchable' => 1,
	 'filterable' => 0,
	 'comparable' => 0,
	 'visible_on_front' => 0,
	 'visible_in_advanced_search'  => 0,
	 'is_html_allowed_on_front' => 0,
		'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));
}

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','isbn-10')->getId()) {

	$setup->addAttribute('catalog_product', 'isbn-10', array(
	  'group'      => 'jetcom',
	  'note'	   =>'If standard_product_code_type is "ISBN-10" - must be 10 digits',	
	  'input'         => 'text',
	  'type'          => 'text',
	  'label'         => 'ISBN-10',
	  'backend'       => '',
	  'visible'       => 1,
	  'required'  => 0,
	  'user_defined' => 1,
	  'searchable' => 1,
	  'filterable' => 0,
	  'comparable' => 0,
	  'visible_on_front' => 0,
	  'visible_in_advanced_search'  => 0,
	  'is_html_allowed_on_front' => 0,
	  'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));
}

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','gtin-14')->getId()) {

	$setup->addAttribute('catalog_product', 'gtin-14', array(
	  'group'      => 'jetcom',
	  'note'	   =>'If standard_product_code_type is "GTIN-14" - must be 14 digits',	
	  'input'         => 'text',
	  'type'          => 'text',
	  'label'         => 'GTIN-14',
	  'backend'       => '',
	  'visible'       => 1,
	  'required'  => 0,
	  'user_defined' => 1,
	  'searchable' => 1,
	  'filterable' => 0,
	  'comparable' => 0,
	  'visible_on_front' => 0,
	  'visible_in_advanced_search'  => 0,
	  'is_html_allowed_on_front' => 0,
	  'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));

}

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','jet_product_status')->getId()) {
	$setup->addAttribute('catalog_product', 'jet_product_status', array(
		'type'       => 'varchar',
		'group'             => 'jetcom',
		'label'             => 'Jet Product Status',
		'input' 			=> 'select',
		'backend'    => 'eav/entity_attribute_backend_array',
		'frontend'          => '',
		'visible'           => true,
		'required'          => false,
		'user_defined'      => true,
		'searchable'        => true,
		'filterable'        => true,
		'comparable'        => true,
		'visible_on_front'  => false,
		'visible_in_advanced_search' => false,
		'default'	=> 'not_uploaded',
		'unique'            => false,
		'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'source'     => 'jet/source_productstatus',
		'is_configurable'   => false,
	));

}
$setup->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'upc', 'note','If standard_product_code_type is "UPC" - must be 12 digits');
$setup->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'ean', 'note','If standard_product_code_type is "EAN" - must be 13 digits');
$installer->endSetup();
