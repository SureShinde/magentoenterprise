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
$this->getConnection()->addColumn($this->getTable('jet/jetcategory'), 'is_csv_category', 'BOOLEAN NOT NULL DEFAULT 1');
$installer->run("
ALTER TABLE {$this->getTable('jet/jetcategory')} change `jet_cate_id` `jet_cate_id` bigint(20) unsigned NOT NULL default '0';	
ALTER TABLE {$this->getTable('jet/jetattribute')} change  `jet_attr_id` `jet_attr_id`  bigint(20) unsigned NOT NULL default '0';	
"); 
			
$installer->endSetup();		