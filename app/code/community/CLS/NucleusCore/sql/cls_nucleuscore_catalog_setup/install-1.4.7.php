<?php
/**
 * Nucleus
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Nucleus License
 * that is bundled with this package in the file LICENSE_NUCLEUS.txt.
 * It is also available through the World Wide Web at this URL:
 * http://www.nucleuscommerce.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the World Wide Web, please send an email
 * to support@nucleuscommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Nucleus
 * to newer versions in the future.
 *
 * @category   CLS
 * @package    NucleusCore
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Setup */

$installer->startSetup();

$installer->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'list_thumbnail', array(
    'group' => 'General Information',
    'type' => 'varchar',
    'label' => 'Category Listing Thumbnail',
    'backend' => 'catalog/category_attribute_backend_image',
    'input' => 'image',
    'sort_order' => 5,
    'required' => false,
    'user_defined' => true,
    'default' => '',
));

// Migrate any existing category thumbnails to the new attribute
$origThumbAttr = Mage::getModel('eav/config')->getAttribute(Mage_Catalog_Model_Category::ENTITY, 'thumbnail');
$listThumbAttr = Mage::getModel('eav/config')->getAttribute(Mage_Catalog_Model_Category::ENTITY, 'list_thumbnail');

if (
    ($origThumbAttr && ($origThumbId = $origThumbAttr->getId()))
    && ($listThumbAttr && ($listThumbId = $listThumbAttr->getId()))
) {
    $select = $installer->getConnection()->select()
        ->from(array('thumbVal' => $installer->getTable(array('catalog/category', 'varchar'))), array())
        ->columns(array(
            'thumbVal.entity_type_id',
            new Zend_Db_Expr($listThumbId),
            'thumbVal.store_id',
            'thumbVal.entity_id',
            'thumbVal.value',
        ))
        ->where('thumbVal.attribute_id=?', $origThumbId);

    $query = $select->insertFromSelect($installer->getTable(array('catalog/category', 'varchar')),
        array('entity_type_id', 'attribute_id', 'store_id', 'entity_id', 'value')
    );
    $installer->getConnection()->query($query);
}

$installer->endSetup();