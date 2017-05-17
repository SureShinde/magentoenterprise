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

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

// Set default package and theme
$packageConfigPath = 'design/package/name';
Mage::getModel('core/config_data')
    ->load($packageConfigPath, 'path')
    ->setValue('nucleus')
    ->setPath($packageConfigPath)
    ->save();

$themeConfigPath = 'design/theme/default';
Mage::getModel('core/config_data')
    ->load($themeConfigPath, 'path')
    ->setValue(Mage::helper('cls_nucleuscore')->getNucleusTheme())
    ->setPath($themeConfigPath)
    ->save();