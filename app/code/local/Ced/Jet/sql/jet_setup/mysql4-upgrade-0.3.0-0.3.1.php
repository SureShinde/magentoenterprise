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
$this->getConnection()->addColumn($this->getTable('jet/jetattribute'), 'jet_attr_val', 'TEXT DEFAULT NULL AFTER `is_mapped_attr`');

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('jet/autoship')} (
`id` int(11) NOT NULL  auto_increment,
  `order_id` varchar(50) NOT NULL,
  `jet_reference_id` varchar(200) NOT NULL,
  `error` text NOT NULL,
  `jet_shipment_status` text NOT NULL,
  PRIMARY KEY (`id`)
);
");

$installer->endSetup();