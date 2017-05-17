<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('sales_flat_order_address')}  ADD dest_type varchar(5);

");

$installer->endSetup();


