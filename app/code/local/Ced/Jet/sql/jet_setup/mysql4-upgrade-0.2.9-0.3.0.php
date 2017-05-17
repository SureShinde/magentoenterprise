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
$setup->removeAttribute('catalog_category', 'jet_category_id');
$setup->removeAttribute('catalog_category', 'is_jet_category');

$installer->endSetup();


$installer = $this;
$installer->startSetup();
$this->getConnection()->dropColumn($this->getTable('jet/jetorder'), 'order_item_id');
$this->getConnection()->dropColumn($this->getTable('jet/jetorder'), 'merchant_sku');
$this->getConnection()->dropColumn($this->getTable('jet/jetreturn'), 'order_item_id');
$this->getConnection()->dropColumn($this->getTable('jet/jetreturn'), 'qty_returned');
$this->getConnection()->dropColumn($this->getTable('jet/jetreturn'), 'qty_refunded');
$this->getConnection()->dropColumn($this->getTable('jet/jetreturn'), 'return_refundfeedback');
$this->getConnection()->dropColumn($this->getTable('jet/jetreturn'), 'agreeto_return');
$this->getConnection()->dropColumn($this->getTable('jet/jetreturn'), 'amount');
$this->getConnection()->dropColumn($this->getTable('jet/jetreturn'), 'shipping_cost');
$this->getConnection()->dropColumn($this->getTable('jet/jetreturn'), 'shipping_tax');
$this->getConnection()->dropColumn($this->getTable('jet/jetreturn'), 'tax');
$this->getConnection()->dropColumn($this->getTable('jet/jetreturn'), 'is_csv_category');
$this->getConnection()->dropColumn($this->getTable('jet/jetreturn'), 'is_mapped_attr');
$this->getConnection()->addColumn($this->getTable('jet/jetorder'), 'customer_cancelled', 'TINYINT(1) NOT NULL DEFAULT 0 AFTER `reference_order_id`');
$this->getConnection()->addColumn($this->getTable('jet/orderimport'), 'reference_number', 'TEXT DEFAULT NULL AFTER `merchant_order_id`');

$installer->run("
	CREATE TABLE IF NOT EXISTS {$this->getTable('jet/jetcron')} (
	  `id` int(10) NOT NULL  auto_increment,
	  `event` varchar(50) CHARACTER SET utf8 NOT NULL,
	  `batch_start` int(11) NOT NULL,
	  `timestamp` int(11) NOT NULL,
	  PRIMARY KEY (`id`)
	) CHARACTER SET utf8 COLLATE utf8_general_ci;

");

$installer->endSetup();
