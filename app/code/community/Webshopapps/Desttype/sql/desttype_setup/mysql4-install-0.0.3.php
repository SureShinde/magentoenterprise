<?php

$installer = $this;

$installer->startSetup();

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
");

if(Mage::helper('wsacommon')->getNewVersion() > 10) {
    $destTypeAttr = array(
        'type'    	=> Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' 	=> 'Dest Type',
        'length'  	=> 5,
        'nullable' 	=> true);

    $installer->getConnection()->addColumn($installer->getTable('sales/quote_address'),'dest_type', $destTypeAttr );
    $installer->getConnection()->addColumn($installer->getTable('sales/order_address'),'dest_type', $destTypeAttr );
} else {
    $installer->getConnection()->addColumn($installer->getTable('sales/quote_address'),'dest_type',"VARCHAR(5) NULL COMMENT 'Dest Type'");
    $installer->getConnection()->addColumn($installer->getTable('sales/order_address'),'dest_type',"VARCHAR(5) NULL COMMENT 'Dest Type'");
}



// logic lifted from customer/mysql4-data-upgrade-1.4.0.0.13-1.4.0.0.14.php 

// update customer address system attributes data
$attributes = array( 'dest_type' );


$defaultUsedInForms = array(
    'adminhtml_customer_address',
    'customer_address_edit',
    'customer_register_address'
);

foreach ($attributes as $attributeCode) {
    $attribute = $eavConfig->getAttribute('customer_address', $attributeCode);
    if (!$attribute) {
        continue;
    }
    if (false === ($attribute->getData('is_system') == 1 && $attribute->getData('is_visible') == 0)) {
        $attribute->setData('used_in_forms', $defaultUsedInForms);
    }
    $attribute->save();
};
    
$installer->endSetup();


