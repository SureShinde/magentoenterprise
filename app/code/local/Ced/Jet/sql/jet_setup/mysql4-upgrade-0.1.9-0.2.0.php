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
CREATE TABLE IF NOT EXISTS {$this->getTable('jet/jetrefund')} (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `refundid` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)

);
CREATE TABLE IF NOT EXISTS {$this->getTable('jet/jetreturn')} (
  `id` int(11) NOT NULL  auto_increment,
  `merchant_order_id` varchar(50) NOT NULL,
  `order_item_id` varchar(70) NOT NULL,
  `qty_returned` int(11),
  `qty_refunded` int(10),
  `return_refundfeedback` varchar(70),
  `agreeto_return` varchar(5),
  `status` varchar(20),
  `returnid` varchar(50),
  `amount` varchar(50),
  `shipping_cost` varchar(50),
  `shipping_tax` varchar(50),
  `tax` varchar(60),
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS {$this->getTable('jet/jetshippingexcep')} (
      `id` int(11) NOT NULL  auto_increment,
      `sku` varchar(200),
      `shipping_carrier` varchar(200) default NULL,
      `shipping_method` varchar(200) default NULL,
      `shipping_override` varchar(200) default NULL,
      `shipping_charge` int(10) NOT NULL default '0',
      `shipping_excep` varchar(20) default NULL,
      PRIMARY KEY (`id`)
);

ALTER TABLE {$this->getTable('jet/errorfile')} modify `error` text;
ALTER TABLE {$this->getTable('jet/jetrefund')} CHANGE `refundid` `refund_id` varchar(100) NOT NULL;
  ALTER TABLE {$this->getTable('jet/jetrefund')} CHANGE `status` `refund_status` varchar(100) NOT NULL;

       
");
$this->getConnection()->addColumn($this->getTable('jet/errorfile'), 'date', 'DATETIME  NOT NULL AFTER `error`');
$this->getConnection()->addColumn($this->getTable('jet/errorfile'), 'jetinfofile_id', 'INT(11)  NOT NULL AFTER `date`');
$this->getConnection()->addColumn($this->getTable('jet/jetrefund'), 'order_item_id', 'TEXT AFTER `id`');
$this->getConnection()->addColumn($this->getTable('jet/jetrefund'), 'quantity_returned', 'TEXT AFTER `order_item_id`');
$this->getConnection()->addColumn($this->getTable('jet/jetrefund'), 'refund_quantity', 'TEXT AFTER `quantity_returned`');
$this->getConnection()->addColumn($this->getTable('jet/jetrefund'), 'refund_reason', 'TEXT AFTER `refund_quantity`');
$this->getConnection()->addColumn($this->getTable('jet/jetrefund'), 'refund_feedback', 'TEXT AFTER `refund_reason`');
$this->getConnection()->addColumn($this->getTable('jet/jetrefund'), 'refund_amount', 'TEXT AFTER `refund_feedback`');
$this->getConnection()->addColumn($this->getTable('jet/jetrefund'), 'refund_tax', 'TEXT AFTER `refund_amount`');
$this->getConnection()->addColumn($this->getTable('jet/jetrefund'), 'refund_shippingcost', 'TEXT AFTER `refund_tax`');
$this->getConnection()->addColumn($this->getTable('jet/jetrefund'), 'refund_shippingtax', 'TEXT AFTER `refund_shippingcost`');
$this->getConnection()->addColumn($this->getTable('jet/jetrefund'), 'refund_orderid', 'TEXT AFTER `refund_shippingtax`');
$this->getConnection()->addColumn($this->getTable('jet/jetrefund'), 'refund_merchantid', 'TEXT AFTER `refund_orderid`');
$installer->endSetup();


