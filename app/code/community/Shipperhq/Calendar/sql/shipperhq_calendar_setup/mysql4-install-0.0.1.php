<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$version = Mage::helper('wsalogger')->getNewVersion();

$dispatchDate = array(
    'type' => $version > 10 ? Varien_Db_Ddl_Table::TYPE_TEXT : Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'length' => 20,
    'comment' => 'ShipperHQ Dispatch Date',
    'nullable' => 'true');

$timeSlot = array(
    'type' => $version > 10 ? Varien_Db_Ddl_Table::TYPE_TEXT : Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'length' => 20,
    'comment' => 'ShipperHQ Time Slot',
    'nullable' => 'true');


$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'time_slot', $timeSlot);
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'dispatch_date', $dispatchDate);

$installer->getConnection()->addColumn($installer->getTable('sales/quote_address_shipping_rate'), 'dispatch_date', $dispatchDate);
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address_shipping_rate'), 'time_slot', $timeSlot);

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'dispatch_date', $dispatchDate);
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'time_slot', $timeSlot);

$installer->endSetup();