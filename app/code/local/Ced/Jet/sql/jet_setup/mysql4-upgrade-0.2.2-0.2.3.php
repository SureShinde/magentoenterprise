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
$this->getConnection()->addColumn($this->getTable('jet/jetorder'), 'order_data', 'TEXT DEFAULT NULL AFTER `status`');
$this->getConnection()->addColumn($this->getTable('jet/jetorder'), 'shipment_data', 'TEXT DEFAULT NULL AFTER `order_data`');
$this->getConnection()->addColumn($this->getTable('jet/jetorder'), 'reference_order_id', 'TEXT DEFAULT NULL AFTER `shipment_data`');

  
$installer->endSetup();   