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
    'title' => 'Test Product Creation Banner',
    'content' => '<p>This is test content for the test banner that goes on the product creation page.</p>',
))
    ->addLocation(array(
        'url_path' => 'admin/catalog_product/new',
        'url_remainder_pattern' => '^set',
        'selector' => '.middle',
    ))
    ->addVideo(array(
        'title' => 'Video #1',
        'description' => 'Watch video #1',
        'embed_code' => '66658527',
        'thumbnail' => 'example1.jpg',
        'run_time'  => '2:33',
    ))
    ->addVideo(array(
        'title' => 'Video #2',
        'embed_code' => '19761455',
        'thumbnail' => 'example2.jpg',
        'run_time'  => '3:34',
    ));
$banner->save();



$banner = Mage::getModel('cls_adminenhance/banner')->setData(array(
    'title' => 'Test Product Categories Banner',
    'content' => '<p>This is test content for the test banner that goes on the product categories tab.</p>',
))
    ->addLocation(array(
        'url_path' => 'admin/catalog_product/categories',
        'selector' => '#product_info_tabs_categories_content'
    ))
    ->addVideo(array(
        'title' => 'Video #3',
        'description' => 'Watch video #3',
        'embed_code' => '1070986',
        'thumbnail' => 'example3.jpg',
        'run_time'  => '4:13',
    ));
$banner->save();



$video = Mage::getModel('cls_adminenhance/video')->load('Video #1', 'title');

$banner = Mage::getModel('cls_adminenhance/banner')->setData(array(
    'title' => 'Test Product Pre-Creation Banner',
    'content' => '<p>This is test content for the test banner that comes before we have selected an attribute set on product creation.</p>',
))
    ->addLocation(array(
        'url_path' => 'admin/catalog_product/new',
        'url_remainder_negative_pattern' => '^set',
        'selector' => '.middle',
    ))
    ->addVideo($video);
$banner->save();