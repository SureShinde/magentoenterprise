<?php

$installer = $this;

$installer->startSetup();

/* @var $addressHelper Mage_Customer_Helper_Address */
$addressHelper = Mage::helper('customer/address');
$store         = Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID);

/* @var $eavConfig Mage_Eav_Model_Config */
$eavConfig = Mage::getSingleton('eav/config');

$installer->run("

select @entity_type_id:=entity_type_id from {$this->getTable('eav_entity_type')} where entity_type_code='customer_address';

insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'dest_type',
	backend_type	= 'varchar',
	frontend_input	= 'select',
	is_required	= 0,
	is_user_defined	= 1,
	source_model = 'desttype/customer_attribute_backend_destType',
	frontend_label	= 'Address Type';
	
select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='dest_type' and entity_type_id=@entity_type_id;

insert ignore into {$this->getTable('customer_eav_attribute')} 
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1;	
	
select @attribute_set_id:=attribute_set_id from {$this->getTable('eav_attribute_set')} where entity_type_id=@entity_type_id;

select @attribute_group_id:=attribute_group_id from {$this->getTable('eav_attribute_group')} where attribute_group_name='General' and attribute_set_id=@attribute_set_id;

replace into {$this->getTable('eav_entity_attribute')}
    set entity_type_id 		= @entity_type_id,
    	attribute_set_id 	= @attribute_set_id,
    	attribute_group_id	= @attribute_group_id,
    	attribute_id		= @attribute_id,
    	sort_order			= 999;	
    	
insert ignore into {$this->getTable('catalog_eav_attribute')} 
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 1;	
	
	
select @entity_type_id:=entity_type_id from {$this->getTable('eav_entity_type')} where entity_type_code='order_address';

insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'dest_type',
	backend_type	= 'varchar',
	frontend_input	= 'select',
	is_required	= 0,
	is_user_defined	= 1,
	frontend_label	= 'Address Type';

select @attribute_set_id:=attribute_set_id from {$this->getTable('eav_attribute_set')} where entity_type_id=@entity_type_id;
select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='dest_type' and entity_type_id=@entity_type_id;
select @attribute_group_id:=attribute_group_id from {$this->getTable('eav_attribute_group')} where attribute_group_name='General' and attribute_set_id=@attribute_set_id;

replace into {$this->getTable('eav_entity_attribute')}
    set entity_type_id 		= @entity_type_id,
    	attribute_set_id 	= @attribute_set_id,
    	attribute_group_id	= @attribute_group_id,
    	attribute_id		= @attribute_id,
    	sort_order			= 999;	
    	
insert ignore into {$this->getTable('catalog_eav_attribute')} 
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 1;	
	

ALTER TABLE {$this->getTable('sales_flat_quote_address')}  ADD dest_type varchar(5);
	

");

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


