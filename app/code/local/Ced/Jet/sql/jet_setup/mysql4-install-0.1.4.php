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
$installer->run("CREATE TABLE IF NOT EXISTS {$this->getTable('jet/jetattribute')} (
  `id` int(11) NOT NULL  auto_increment,
  `jet_attr_id` int(11) NOT NULL,
  `magento_attr_id` int(11) NOT NULL,
  `freetext` int(4) NOT NULL,
  `variant` int(1) NOT NULL default '0',
  `variant_pair` int(1) NOT NULL default '0',
  `unit` varchar(90) default NULL,
  PRIMARY KEY (`id`)
);
		
CREATE TABLE IF NOT EXISTS {$this->getTable('jet/jetcategory')} (
 `id` int(11) NOT NULL  auto_increment,
  `jet_cate_id` int(11) NOT NULL,
  `magento_cat_id` int(11) NOT NULL,
  `jet_attributes` text,
	PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS {$this->getTable('jet/errorfile')} (
  `id` int(11) NOT NULL  auto_increment,
  `jet_file_id` varchar(70) NOT NULL,
  `file_name` varchar(70) NOT NULL,
  `file_type` varchar(70) NOT NULL,
  `status` varchar(60) NOT NULL,
  `error` varchar(300) NOT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS {$this->getTable('jet/fileinfo')} (
  `id` int(10) NOT NULL  auto_increment,
  `magento_batch_info` varchar(900) NOT NULL,
  `jet_file_id` varchar(400) NOT NULL,
  `token_url` varchar(200) NOT NULL,
  `file_name` varchar(100) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL default 'unprocessed',
  PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS {$this->getTable('jet/jetorder')} (
`id` int(10) NOT NULL  auto_increment,
  `order_item_id` varchar(100) NOT NULL,
  `merchant_order_id` varchar(100) NOT NULL,
  `merchant_sku` varchar(100) NOT NULL,
  `deliver_by` varchar(100) NOT NULL,
  `magento_order_id` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
	PRIMARY KEY (`id`)
)CHARACTER SET utf8 COLLATE utf8_general_ci;
");
$installer->endSetup();
