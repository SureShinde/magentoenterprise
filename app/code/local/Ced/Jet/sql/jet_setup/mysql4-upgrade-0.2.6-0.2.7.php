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
$installer->startSetup();
$installer->run("
TRUNCATE TABLE {$this->getTable('jet/jetattribute')};

ALTER TABLE {$this->getTable('jet/jetattribute')} change `unit` `unit` text NULL;
CREATE TABLE IF NOT EXISTS `{$this->getTable('jet/batcherror')}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `product_sku` varchar(100) NOT NULL,
  `batch_num` int(11) NOT NULL,
  `is_write_mode` int(11) NOT NULL,
  `error` text NOT NULL,
  `date_added` datetime NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;	

CREATE TABLE IF NOT EXISTS `{$this->getTable('jet/catlist')}` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`csv_cat_id` bigint(11) NOT NULL,
`name` text NOT NULL,
`csv_parent_id` bigint(11) NOT NULL,
`path` text NOT NULL,
`level` text NOT NULL,
`attribute_ids` text NOT NULL,
`created_category` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
							
"); 
$this->getConnection()->addColumn($this->getTable('jet/jetattribute'), 'magento_attr_code', 'TEXT NOT NULL AFTER `magento_attr_id`');
$this->getConnection()->addColumn($this->getTable('jet/jetattribute'), 'list_option', 'TEXT DEFAULT NULL AFTER `unit`');
$this->getConnection()->addColumn($this->getTable('jet/jetattribute'), 'is_mapped_attr', 'TinyInt(1)  NOT NULL DEFAULT 0 AFTER `list_option`');				
$this->getConnection()->addColumn($this->getTable('jet/jetreturn'), 'return_details', 'TEXT NOT NULL AFTER `tax`');
$this->getConnection()->addColumn($this->getTable('jet/jetreturn'), 'details_saved_after', 'TEXT NOT NULL AFTER `return_details`');
$this->getConnection()->addColumn($this->getTable('jet/jetrefund'), 'saved_data', 'TEXT NOT NULL AFTER `refund_status`');
$installer->endSetup();		

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();


if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','amazon_item_type_keyword')->getId()) {
	$setup->addAttribute('catalog_product', 'amazon_item_type_keyword', array(
			'group'     	=> 'jetcom',
			'note'	   =>'ItemType allows customers to find your products as they browse to the most specific item types. Please use the exact selling from Amazon\'s browse tree guides',
			'input'         => 'text',
			'type'          => 'text',
			'label'         => 'Amazon Item Type Keyword',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));

}

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','category_path')->getId()) {
	$setup->addAttribute('catalog_product', 'category_path', array(
			'group'     	=> 'jetcom',
			'input'         => 'text',
			'type'          => 'text',
			'label'         => 'Category Path',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));
}


if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','bullets')->getId()) {
	$setup->addAttribute('catalog_product', 'bullets', array(
			'group'     	=> 'jetcom',
			'note'	   =>'Please enter product feature description.Add each feature inside \'{}\'.Example :- {This is first one.}{This is second one.} and so on.Each \'{}\' contains maximum of 500 characters.Maximum 5 \'{}\' is allowed.',
			'input'         => 'textarea',
			'type'          => 'text',
			'label'         => 'Bullets',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));

}

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','number_units_for_ppu')->getId()) {
	$setup->addAttribute('catalog_product', 'number_units_for_ppu', array(
			'group'     	=> 'jetcom',
			'note'	   =>'For Price Per Unit calculations, the number of units included in the merchant SKU. The unit of measure must be specified in order to indicate what is being measured by the unit-count.',
			'input'         => 'text',
			'type'          => 'text',
			'label'         => 'Number Units For Price Per Unit',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));

}

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','type_of_unit_for_ppu')->getId()) {
	$setup->addAttribute('catalog_product', 'type_of_unit_for_ppu', array(
			'group'     	=> 'jetcom',
			'note'	   =>'The type_of_unit_for_price_per_unit attribute is a label for the number_units_for_price_per_unit. The price per unit can then be constructed by dividing the selling price by the number of units and appending the text \"per unit value.\" For example, for a six-pack of soda, number_units_for_price_per_unit= 6, type_of_unit_for_price_per_unit= can, price per unit = price per can',
			'input'         => 'text',
			'type'          => 'text',
			'label'         => 'Type of unit for price per unit',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));

}

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','shipping_weight_pounds')->getId()) {
	$setup->addAttribute('catalog_product', 'shipping_weight_pounds', array(
			'group'     	=> 'jetcom',
			'note'	   =>'Weight of the merchant SKU when in its shippable configuration.',
			'input'         => 'text',
			'type'          => 'text',
			'label'         => 'Shipping Weight Pounds',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));

}

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','package_length_inches')->getId()) {
	$setup->addAttribute('catalog_product', 'package_length_inches', array(
			'group'     	=> 'jetcom',
			'note'	   =>	'Length of the merchant SKU when in its shippable configuration.',
			'input'         => 'text',
			'type'          => 'text',
			'label'         => 'Package Length Inches',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));

}

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','package_width_inches')->getId()) {
	$setup->addAttribute('catalog_product', 'package_width_inches', array(
			'group'     	=> 'jetcom',
			'note'	   =>	'Width of the merchant SKU when in its shippable configuration.',
			'input'         => 'text',
			'type'          => 'text',
			'label'         => 'Package Width Inches',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));

}

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','package_height_inches')->getId()) {
	$setup->addAttribute('catalog_product', 'package_height_inches', array(
			'group'     	=> 'jetcom',
			'note'	   =>	'Height of the merchant SKU when in its shippable configuration.',
			'input'         => 'text',
			'type'          => 'text',
			'label'         => 'Package height inches',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));

}

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','display_length_inches')->getId()) {
	$setup->addAttribute('catalog_product', 'display_length_inches', array(
			'group'     	=> 'jetcom',
			'note'	   =>	'Length of the merchant SKU when in its fully assembled/usable condition.',
			'input'         => 'text',
			'type'          => 'text',
			'label'         => 'Display Length Inches',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));

}

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','display_width_inches')->getId()) {
	$setup->addAttribute('catalog_product', 'display_width_inches', array(
			'group'     	=> 'jetcom',
			'note'	   =>	'Width of the merchant SKU when in its fully assembled/usable condition.',
			'input'         => 'text',
			'type'          => 'text',
			'label'         => 'Display width Inches',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));

}
if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','display_height_inches')->getId()) {
	$setup->addAttribute('catalog_product', 'display_height_inches', array(
			'group'     	=> 'jetcom',
			'note'	   =>	'Height of the merchant SKU when in its fully assembled/usable condition.',
			'input'         => 'text',
			'type'          => 'text',
			'label'         => 'Display Height Inches',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));

}
if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','prop_65')->getId()) {
	$setup->addAttribute('catalog_product', 'prop_65', array(
			'group'     	=> 'jetcom',
			'note'	   =>	'You must tell us if your product is subject to Proposition 65 rules and regulations. Proposition 65 requires merchants to provide California consumers with special warnings for products that contain chemicals known to cause cancer, birth defects, or other reproductive harm, if those products expose consumers to such materials above certain threshold levels. Please view this website for more information: http://www.oehha.ca.gov/.',
			 'type'                => 'int',
        	'input'                => 'boolean',
        	'source'               => 'eav/entity_attribute_source_boolean',
			'label'         => 'Proposition 65',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
			'default'            => 0,
			'is_configurable'   => false,
	));

}
if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','legal_disclaimer_description')->getId()) {
	$setup->addAttribute('catalog_product', 'legal_disclaimer_description', array(
			'group'     	=> 'jetcom',
			'note'	   =>	'Add any legal language required to be displayed with the product.',
			'input'         => 'textarea',
			'type'          => 'text',
			'label'         => 'Legal Disclaimer Description',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));

}

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','cpsia_cautionary_statements')->getId()) {
	$setup->addAttribute('catalog_product', 'cpsia_cautionary_statements', array(
			'group'     	=> 'jetcom',
			'note'	   =>	'Use this field to indicate if a cautionary statement relating to the choking hazards of children\'s toys and games applies to your product. These cautionary statements are defined in Section 24 of the Federal Hazardous Substances Act and Section 105 of the Consumer Product Safety Improvement Act of 2008. ',
			 'input' => 'multiselect',
			'type'          => 'text',
			'label'         => 'CPSIA Cautionary Statements',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
			"source" => "jet/System_Config_Source_Caution",
			'backend'           => 'eav/entity_attribute_backend_array',
			'is_configurable'   => false,
	));

}
if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','safety_warning')->getId()) {
	$setup->addAttribute('catalog_product', 'safety_warning', array(
			'group'     	=> 'jetcom',
			'note'	   =>	'If applicable, use to supply any associated warnings for your product.Maximum of 500 characters.',
			'input'         => 'textarea',
			'type'          => 'text',
			'label'         => 'Safety Warning',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));

}
if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','start_selling_date')->getId()) {
	$setup->addAttribute('catalog_product', 'start_selling_date', array(
			'group'     	=> 'jetcom',
			'note'	   =>	'If updating merchant SKU that has quantity = 0 at all FCs, date that the inventory in this message should be available for sale on Jet.com.',
			'input'         => 'date',
    		'type'          => 'datetime',
			'label'         => 'Start Selling Date',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
			'backend'	=> "eav/entity_attribute_backend_datetime",
	));

}
if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','fulfillment_time')->getId()) {
	$setup->addAttribute('catalog_product', 'fulfillment_time', array(
			'group'     	=> 'jetcom',
			'note'	   =>	'Number of business days from receipt of an order for the given merchant SKU until it will be shipped (only populate if it is different than your account default).',
			'input'         => 'text',
			'type'          => 'int',
			'label'         => 'Fulfillment Time',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));

}

if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','map_price')->getId()) {
	$setup->addAttribute('catalog_product', 'map_price', array(
			'group'     	=> 'jetcom',
			'note'	   =>	'Retailer price for the product for which member savings will be applied.',
			'input'         => 'price',
			'type'          => 'text',
			'label'         => 'Map Price',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));

}
if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','map_implementation')->getId()) {
	
	$setup->addAttribute('catalog_product', 'map_implementation', array(
			'group'     	=> 'jetcom',
			'note'	   =>	'The type of rule that indicates how Jet member savings are allowed to be applied to an item�s base price. ',
			 'input' => 'select',
			'type'          => 'text',
			'label'         => 'Map Implementation',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
			"source" => "jet/System_Config_Source_Mapimp",
			'backend' => 'eav/entity_attribute_backend_array',
			'is_configurable'   => false,
			
	));

}
if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','product_tax_code')->getId()) {
	
	$setup->addAttribute('catalog_product', 'product_tax_code', array(
			'group'     	=> 'jetcom',
			'note'	   =>	'Jet\'s standard code for the tax properties of a given product.',
			'input' => 'select',
			'type'          => 'text',
			'label'         => 'Product Tax Code',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
			"source" => "jet/System_Config_Source_Taxcode",
			'backend'           => 'eav/entity_attribute_backend_array',
			'is_configurable'   => false,
			
	));

}
if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','no_return_fee_adjustment')->getId()) {
	$setup->addAttribute('catalog_product', 'no_return_fee_adjustment', array(
			'group'     	=> 'jetcom',
			'input'         => 'price',
			'type'          => 'text',
			'label'         => 'No return fee adjustment',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));

}
if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','exclude_from_fee_adjust')->getId()) {
	$setup->addAttribute('catalog_product', 'exclude_from_fee_adjust', array(
			'group'     	=> 'jetcom',
			'note'	   =>	'This SKU will not be subject to any fee adjustment rules that are set up if this field is Yes.',
			'type'                => 'int',
			'input'                => 'boolean',
			'source'               => 'eav/entity_attribute_source_boolean',
			'label'         => 'Exclude from fee adjustments',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
			'default'            => 0,
			'is_configurable'   => false,
	));

}
if(!Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','ships_alone')->getId()) {
	$setup->addAttribute('catalog_product', 'ships_alone', array(
			'group'     	=> 'jetcom',
			'note'	   =>	'If this field is Yes, it indicates that this merchant SKU will always ship on its own.A separate merchant_order will always be placed for this merchant_SKU, one consequence of this will be that this merchant_sku will never contriube to any basket size fee adjustments with any other merchant_skus.',
			'type'                => 'int',
			'input'                => 'boolean',
			'source'               => 'eav/entity_attribute_source_boolean',
			'label'         => 'Ships alone',
			'backend'       => '',
			'visible'       => 1,
			'required'		=> 0,
			'user_defined' => 1,
			'searchable' => 1,
			'filterable' => 0,
			'comparable'	=> 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
			'default'            => 0,
			'is_configurable'   => false,
	));

}

$installer->endSetup();	
