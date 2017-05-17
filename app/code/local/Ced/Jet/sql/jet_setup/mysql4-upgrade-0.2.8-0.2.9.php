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
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
if(Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','jet_product_status')->getId()) {
	$setup->updateAttribute('catalog_product', 'jet_product_status', 'source_model','Ced_Jet_Model_Source_Productstatus');
}
if(Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','cpsia_cautionary_statements')->getId()) {
	$setup->updateAttribute('catalog_product', 'cpsia_cautionary_statements', 'source_model','Ced_Jet_Model_System_Config_Source_Caution');
}
if(Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','map_implementation')->getId()) {
	$setup->updateAttribute('catalog_product', 'map_implementation', 'source_model','Ced_Jet_Model_System_Config_Source_Mapimp');
}
if(Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','product_tax_code')->getId()) {
	$setup->updateAttribute('catalog_product', 'product_tax_code', 'source_model','Ced_Jet_Model_System_Config_Source_Taxcode');
}
$installer->endSetup();