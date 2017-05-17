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
 * @category   Nucleus
 * @package    Catalog
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Setup */

$installer->removeAttribute(Mage_Catalog_Model_Category::ENTITY, Nucleus_Catalog_Model_Switcher_Category::IS_ENABLED_CATEGORY_ATTR_CODE);
$installer->addAttribute(Mage_Catalog_Model_Category::ENTITY, Nucleus_Catalog_Model_Switcher_Category::IS_ENABLED_CATEGORY_ATTR_CODE, array(
    'group' => 'Display Settings',
    'type' => 'int',
    'label' => 'Enable Cross-navigation with Sibling Categories',
    'input' => 'select',
    'source' => 'eav/entity_attribute_source_boolean',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'default' => '0',
    'sort_order' => 120,
    'note' => 'If enabled, "next" and "prev" navigation to sibling categories appears on this category\'s page.',
));



