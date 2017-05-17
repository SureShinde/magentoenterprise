<?php

$installer = $this;

$installer->startSetup();

/* @var $addressHelper Mage_Customer_Helper_Address */
$addressHelper = Mage::helper('customer/address');
$store         = Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID);

/* @var $eavConfig Mage_Eav_Model_Config */
$eavConfig = Mage::getSingleton('eav/config');



// update customer address system attributes data
$attributes = array(
 'dest_type'          => array(
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 115,
        'is_required'       => 0,
        'validate_rules'    => array(
        ),
    ),
);

foreach ($attributes as $attributeCode => $data) {
    $attribute = $eavConfig->getAttribute('customer_address', $attributeCode);
    $attribute->setWebsite($store->getWebsite());
    $attribute->addData($data);
    if (false === ($data['is_system'] == 1 && $data['is_visible'] == 0)) {
        $usedInForms = array(
            'adminhtml_customer_address',
            'customer_address_edit',
            'customer_register_address'
        );
        $attribute->setData('used_in_forms', $usedInForms);
    }
    $attribute->save();
}
    
$installer->endSetup();


