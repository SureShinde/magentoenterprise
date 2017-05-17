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

$banner = Mage::getModel('cls_adminenhance/banner')->setData(array(
    'title' => 'Key Changes within System Configuration',
    'content' => '<p>These training videos cover make changes to the basic Magento system configuration settings and the logo displayed on your site.</p>',
))
    ->addLocation(array(
        'url_path' => 'admin/system_config/index',
        'selector' => '.middle'
    ))
    ->addLocation(array(
        'url_path' => 'admin/system_config/edit',
        'selector' => '.middle',
        'url_remainder_pattern' => '^section/cls_nucleus_info'
    ))
    ->addVideo(array(
        'title' => 'Basic Admin Setup',
        'embed_code' => '100829177',
        'thumbnail' => 'Basic_Admin_Setup_Training_Video.jpg',
        'run_time' => '8:34',
        'position' => 10,
    ))
    ->addVideo(array(
        'title' => 'How to Change the Logo',
        'embed_code' => '99875942',
        'thumbnail' => 'How_to_Change_the_Logo_Training_Video.jpg',
        'run_time' => '5:52',
        'position' => 20,
    ))
    ->addVideo(array(
        'title' => 'How to Set Up Payment Methods',
        'embed_code' => '99875943',
        'thumbnail' => 'How_to_Set_Up_Payment_Methods_Training_Video.jpg',
        'run_time' => '19:40',
        'position' => 30,
    ))
    ->addVideo(array(
        'title' => 'How to Set Up Shipping Method (USPS)',
        'embed_code' => '99875944',
        'thumbnail' => 'How_to_Set_Up_Shipping_Method_Training_Video.jpg',
        'run_time' => '7:09',
        'position' => 40,
    ));
$banner->save();

$video = Mage::getModel('cls_adminenhance/video')->load('99875942', 'embed_code');
$banner = Mage::getModel('cls_adminenhance/banner')->setData(array(
    'title' => 'Changing your Store Logo',
    'content' => '<p>This training video walks you through the process of changing the logo on your site.</p>',
))
    ->addLocation(array(
        'url_path' => 'admin/system_config/edit',
        'selector' => '.middle',
        'url_remainder_pattern' => '^section/design'
    ))
    ->addVideo($video);
$banner->save();

$video = Mage::getModel('cls_adminenhance/video')->load('99875943', 'embed_code');
$banner = Mage::getModel('cls_adminenhance/banner')->setData(array(
    'title' => 'How to Set Up Payment Methods',
    'content' => '<p>This training video explains the process of configuring payment methods for your store.</p>',
))
    ->addLocation(array(
        'url_path' => 'admin/system_config/edit',
        'selector' => '.middle',
        'url_remainder_pattern' => '^section/payment'
    ))
    ->addVideo($video);
$banner->save();

$video = Mage::getModel('cls_adminenhance/video')->load('99875944', 'embed_code');
$banner = Mage::getModel('cls_adminenhance/banner')->setData(array(
    'title' => 'How to Set Up Shipping Method (USPS)',
    'content' => '<p>This training video will prep you for configuring your store to support USPS shipping options.</p>',
))
    ->addLocation(array(
        'url_path' => 'admin/system_config/edit',
        'selector' => '.middle',
        'url_remainder_pattern' => '^section/carriers'
    ))
    ->addVideo($video);
$banner->save();

$banner = Mage::getModel('cls_adminenhance/banner')->setData(array(
    'title' => 'Creating Categories',
    'content' => '<p>This training video teaches you how to edit and create product categories and subcategories on your site.</p>',
))
    ->addLocation(array(
        'url_path' => 'admin/catalog_category/index',
        'selector' => '.middle'
    ))
    ->addVideo(array(
        'title' => 'Creating Categories',
        'embed_code' => '99755349',
        'thumbnail' => 'Creating_Categories_Training_Video.jpg',
        'run_time' => '23:19',
        'position' => 50,
    ));
$banner->save();

$banner = Mage::getModel('cls_adminenhance/banner')->setData(array(
    'title' => 'Manage and Create New Products',
    'content' => '<p>These training videos teach you how to add simple and configurable products to your store. Before adding a configurable product, you may want to read more about creating attributes and attribute sets.</p>',
))
    ->addLocation(array(
        'url_path' => 'admin/catalog_product/index',
        'selector' => '.middle'
    ))
    ->addVideo(array(
        'title' => 'How to Create Attributes and Attribute Sets',
        'embed_code' => '99759230',
        'thumbnail' => 'How_to_Create_Attributes_and_Attribute_Sets_Training_Video.jpg',
        'run_time' => '20:45',
        'position' => 60,
    ))
    ->addVideo(array(
        'title' => 'How to Create a Simple Product',
        'embed_code' => '99759227',
        'thumbnail' => 'How_to_Create_a_Simple_Product_Training_Video.jpg',
        'run_time' => '36:48',
        'position' => 70,
    ))
    ->addVideo(array(
        'title' => 'How to Create a Configurable Product',
        'embed_code' => '99759229',
        'thumbnail' => 'How_to_Create_a_Configurable_Product_Training_Video.jpg',
        'run_time' => '6:58',
        'position' => 80,
    ));
$banner->save();

$video = Mage::getModel('cls_adminenhance/video')->load('99759227', 'embed_code');
$banner = Mage::getModel('cls_adminenhance/banner')->setData(array(
    'title' => 'How to Create a Simple Product',
    'content' => '<p>This training video teaches you how to add simple products to your store.</p>',
))
    ->addLocation(array(
        'url_path' => 'admin/catalog_product/new',
        'selector' => '.middle',
        'url_remainder_pattern' => '^set.*simple',
    ))
    ->addVideo($video);
$banner->save();

$video = Mage::getModel('cls_adminenhance/video')->load('99759229', 'embed_code');
$banner = Mage::getModel('cls_adminenhance/banner')->setData(array(
    'title' => 'How to Create a Configurable Product',
    'content' => 'This training video teaches you how to add configurable products to your store. Before adding a configurable product, you may want to read more about creating attributes and attribute sets.',
))
    ->addLocation(array(
        'url_path' => 'admin/catalog_product/new',
        'selector' => '.middle',
        'url_remainder_pattern' => '^set.*configurable',
    ))
    ->addVideo($video);
$banner->save();

$video = Mage::getModel('cls_adminenhance/video')->load('99759230', 'embed_code');
$banner = Mage::getModel('cls_adminenhance/banner')->setData(array(
    'title' => 'How to Create Attributes and Attribute Sets',
    'content' => '<p>This training video teaches you how to edit and create attributes and attribute sets for products on your site.</p>',
))
    ->addLocation(array(
        'url_path' => 'admin/catalog_product_attribute/index',
        'selector' => '.middle'
    ))
    ->addLocation(array(
        'url_path' => 'admin/catalog_product_set/index',
        'selector' => '.middle'
    ))
    ->addVideo($video);
$banner->save();

$banner = Mage::getModel('cls_adminenhance/banner')->setData(array(
    'title' => 'How to Process and Ship an Order',
    'content' => '<p>This training video teaches you how to process pending orders and create shipments within your admin.</p>',
))
    ->addLocation(array(
        'url_path' => 'admin/sales_order/index',
        'selector' => '.middle'
    ))
    ->addVideo(array(
        'title' => 'How to Process and Ship an Order',
        'embed_code' => '99875941',
        'thumbnail' => 'How_to_Process_and_Ship_an_Order_Training_Video.jpg',
        'run_time' => '5:45',
        'position' => 90,
    ));
$banner->save();

$banner = Mage::getModel('cls_adminenhance/banner')->setData(array(
    'title' => 'Managing Static Blocks and Pages',
    'content' => '<p>This training video teaches you how to create pages and add static blocks to your site.</p>',
))
    ->addLocation(array(
        'url_path' => 'admin/cms_page/index',
        'selector' => '.middle'
    ))
    ->addLocation(array(
        'url_path' => 'admin/cms_block/index',
        'selector' => '.middle'
    ))
    ->addVideo(array(
        'title' => 'Managing Static Blocks and Pages',
        'embed_code' => '99754515',
        'thumbnail' => 'Managing_Static_Blocks_and_Pages_Training_Video.jpg',
        'run_time' => '21:55',
        'position' => 100,
    ));
$banner->save();

$banner = Mage::getModel('cls_adminenhance/banner')->setData(array(
    'title' => 'Creating and Managing Widgets',
    'content' => '<p>These training videos explain how to create and manage Nucleus’s Slideshow and Product Carousel widgets.</p>',
))
    ->addLocation(array(
        'url_path' => 'admin/widget_instance/index',
        'selector' => '.middle',
    ))
    ->addVideo(array(
        'title' => 'Slideshow Widgets',
        'embed_code' => '99751288',
        'thumbnail' => 'Slideshow_Widget_Training_Video.jpg',
        'run_time' => '24:36',
        'position' => 110,
    ))
    ->addVideo(array(
        'title' => 'Product Carousel Widgets',
        'embed_code' => '99754582',
        'thumbnail' => 'Product_Carousel_Training_Video.jpg',
        'run_time' => '16:16',
        'position' => 120,
    ));
$banner->save();









