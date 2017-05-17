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
 * @package    AdminEnhance
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table for banners
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('cls_adminenhance/banner'))
    ->addColumn('banner_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Banner ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => '',
    ), 'Title')
    ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => true,
    ), 'Content')
    ->addColumn('formatted', Varien_Db_Ddl_Table::TYPE_SMALLINT, 1, array(
        'nullable' => false,
        'default'  => 1,
    ), 'Formatted')
    ->addColumn('help_links', Varien_Db_Ddl_Table::TYPE_SMALLINT, 1, array(
        'nullable' => false,
        'default'  => 1,
    ), 'Include Help Links');
$installer->getConnection()->createTable($table);

/**
 * Create table for banner locations
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('cls_adminenhance/location'))
    ->addColumn('location_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Location Id')
    ->addColumn('banner_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'unsigned'  => true,
        'default'   => 0,
    ), 'Banner ID')
    ->addColumn('url_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => '',
    ), 'URL Controller/Action Path')
    ->addColumn('url_remainder_pattern', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
    ), 'Pattern for URL Remainder')
    ->addColumn('url_remainder_negative_pattern', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => true,
    ), 'Negative Pattern for URL Remainder')
    ->addColumn('selector', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => '',
    ), 'CSS Selector')
    ->addColumn('selector_context', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => true,
    ), 'Context for the CSS Selector')
    ->addForeignKey(
        $installer->getFkName('cls_adminenhance/location', 'banner_id', 'cls_adminenhance/banner', 'banner_id'),
        'banner_id', $installer->getTable('cls_adminenhance/banner'), 'banner_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addIndex(
        $installer->getIdxName(
            'cls_adminenhance/location',
            array('url_path'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('url_path'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX));
$installer->getConnection()->createTable($table);

/**
 * Create table for videos
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('cls_adminenhance/video'))
    ->addColumn('video_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Video Id')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => '',
    ), 'Title')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => true,
    ), 'Description')
    ->addColumn('embed_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => '',
    ), 'Embed Code')
    ->addColumn('thumbnail', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => true,
    ), 'Thumbnail')
    ->addColumn('run_time', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => true,
    ), 'Run-time')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => true,
    ), 'Position');
$installer->getConnection()->createTable($table);

/**
 * Create table for banner/video link
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('cls_adminenhance/banner_video_link'))
    ->addColumn('link_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Link ID')
    ->addColumn('banner_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
    ), 'Banner Id')
    ->addColumn('video_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
    ), 'Video ID')
    ->addForeignKey(
        $installer->getFkName('cls_adminenhance/banner_video_link', 'banner_id', 'cls_adminenhance/banner', 'banner_id'),
        'banner_id', $installer->getTable('cls_adminenhance/banner'), 'banner_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('cls_adminenhance/banner_video_link', 'video_id', 'cls_adminenhance/video', 'video_id'),
        'video_id', $installer->getTable('cls_adminenhance/video'), 'video_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
$installer->getConnection()->createTable($table);

/**
 * Create table for tracking users' collapsed banners
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('cls_adminenhance/location_collapsed'))
    ->addColumn('collapse_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Collapse Id')
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'unsigned'  => true,
        'default'   => 0,
    ), 'Admin User ID')
    ->addColumn('location_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'unsigned'  => true,
        'default'   => 0,
    ), 'Location ID')
    ->addColumn('collapsed', Varien_Db_Ddl_Table::TYPE_SMALLINT, 1, array(
        'nullable'  => false,
        'default'   => 1,
    ), 'Is Collapsed')
    ->addForeignKey(
        $installer->getFkName('cls_adminenhance/location_collapsed', 'user_id', 'admin/user', 'user_id'),
        'user_id', $installer->getTable('admin/user'), 'user_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('cls_adminenhance/location_collapsed', 'location_id', 'cls_adminenhance/location', 'location_id'),
        'location_id', $installer->getTable('cls_adminenhance/location'), 'location_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
$installer->getConnection()->createTable($table);

$installer->endSetup();
