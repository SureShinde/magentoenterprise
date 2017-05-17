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

$installer->removeAttribute(Mage_Catalog_Model_Category::ENTITY, 'cross_navigation_image');
$installer->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'cross_navigation_image', array(
    'group' => 'Display Settings',
    'type' => 'varchar',
    'label' => 'Image for Product Cross-navigation',
    'input' => 'image',
    'backend' => 'catalog/category_attribute_backend_image',
    'required' => false,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => true,
    'sort_order' => 140,
    'note' => 'This image is displayed instead of the category name for Cross-navigation with Sibling Products.',
));





