<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Amp
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

$installer = $this;
$installer->startSetup();

$homePageIdentifier = Plumrocket_Amp_Helper_Data::AMP_HOME_PAGE_KEYWORD;

$ampHomePage = Mage::getModel('cms/page')->load($homePageIdentifier);
if (!$ampHomePage->getId()) {
    $content = '<div class="amp-cat">'
        . '<a href="{{store url="women.html"}}" class="women"><div><span>women</span></div></a>'
        . '<a href="{{store url="men.html"}}" class="men"><div><span>men</span></div></a>'
        . '<a href="{{store url="accessories.html"}}" class="accessories"><div><span>accessories</span></div></a>'
        . '<a href="{{store url="home-decor.html"}}" class="home-decor"><div><span>home decor</span></div></a>'
        . '<a href="{{store url="sale.html"}}" class="sale"><div><span>sale</span></div></a>'
        . '<a href="{{store url="vip.html"}}" class="vip"><div><span>vip</span></div></a>'
        . '</div>';
    $ampHomePage->setTitle('Amp Home Page')
        ->setContent($content)
        ->setIdentifier($homePageIdentifier)
        ->setLayout('one_column')
        ->setIsActive(true)
        ->setStores(array(0))
        ->save();
}

$footerLinksAmpBlockIdentifier = Plumrocket_Amp_Helper_Data::AMP_FOOTER_LINKS_KEYWORD;
$footeLinksAmpBlock = Mage::getModel('cms/block')->load($footerLinksAmpBlockIdentifier);
if (!$footeLinksAmpBlock->getId()) {
    $content = '<ul>'
        .'<li><a href="{{store url=\'about-magento-demo-store\'}}">About Us</a></li>'
        .'<li><a href="{{store url=\'contacts\'}}">Contact Us</a></li>'
        .'<li><a href="{{store url=\'customer-service\'}}">Customer Service</a></li>'
        .'<li><a href="{{store url=\'privacy-policy-cookie-restriction-mode\'}}">Privacy Policy</a></li>'
        .'</ul>';

    $footeLinksAmpBlock->setTitle('Footer links AMP block')
        ->setContent($content)
        ->setIdentifier($footerLinksAmpBlockIdentifier)
        ->setLayout('one_column')
        ->setIsActive(true)
        ->setStores(array(0))
        ->save();
}

$installer->endSetup();