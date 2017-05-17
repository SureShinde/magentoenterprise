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

 
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');

$groupName = 'jetcom';
$entityTypeId = $installer->getEntityTypeId('catalog_product');
$attributeSetId= $installer->getDefaultAttributeSetId($entityTypeId);

$installer->addAttributeGroup($entityTypeId, $attributeSetId, $groupName, 100);
$attributeGroupId = $installer->getAttributeGroupId($entityTypeId, $attributeSetId, $groupName);

$installer->endSetup();

