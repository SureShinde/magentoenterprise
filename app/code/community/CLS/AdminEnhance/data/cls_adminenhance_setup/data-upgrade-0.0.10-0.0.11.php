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

$videos = Mage::getModel('cls_adminenhance/video')->getCollection();
foreach ($videos as $video) {
    switch ($video->getTitle()) {
        case 'Basic Admin Setup':
            $video->setPosition(10);
            break;
        case 'How to Change the Logo':
            $video->setPosition(20);
            break;
        case 'How to Set Up Payment Methods':
            $video->setPosition(30);
            break;
        case 'How to Set Up Shipping Method (USPS)':
            $video->setPosition(40);
            break;
        case 'Creating Categories':
            $video->setPosition(50);
            break;
        case 'How to Create Attributes and Attribute Sets':
            $video->setPosition(60);
            break;
        case 'How to Create a Simple Product':
            $video->setPosition(70);
            break;
        case 'How to Create a Configurable Product':
            $video->setPosition(80);
            break;
        case 'How to Process and Ship an Order':
            $video->setPosition(90);
            break;
        case 'Managing Static Blocks and Pages':
            $video->setPosition(100);
            break;
        case 'Slideshow Widgets':
            $video->setPosition(110);
            break;
        case 'Product Carousel Widgets':
            $video->setPosition(120);
            break;
    }
    $video->save();
}