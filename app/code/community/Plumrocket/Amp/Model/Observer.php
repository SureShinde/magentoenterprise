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

class Plumrocket_Amp_Model_Observer
{

    /**
     * @var $observer
     * @return void
     */
    public function controllerActionLayoutLoadBefore($observer)
    {
        /**
         * Get pramp helper and check its status
         */
        $helper = Mage::helper('pramp');
        if (!$helper->moduleEnabled()) {
            return;
        }

        $allowedActions = Mage::helper('pramp')->getAllowedPages();

        if (Mage::getSingleton('plumbase/observer')->customer() == Mage::getSingleton('plumbase/product')->currentCustomer()) {
            /**
             * Get full action name and update object
             */
            $currentFullAction = $observer->getAction()->getFullActionName();
            $update = $observer->getEvent()->getLayout()->getUpdate();

            /**
             * Check get parameter amp
             */
            if ($helper->isAmpRequest()) {
                if (in_array($currentFullAction, $allowedActions)) {
                    if (function_exists('newrelic_disable_autorum')) {
                        newrelic_disable_autorum();
                    }

                    /**
                     *  Add layout handlers
                     */
                    $update->addHandle('amp_default');
                    $update->addHandle('amp_' . $currentFullAction);
                } else {
                    /**
                     *  Call redirect to canonical page
                     */
                    if ($currentFullAction != 'cms_index_noRoute') {
                        Mage::app()->getResponse()->setRedirect($helper->getCanonicalUrl(), 301)->sendResponse();
                        exit;
                    }
                }
            }
        }

        /**
         * Add layout changes
         */
        if (in_array($currentFullAction, $allowedActions)) {
            $update = $observer->getEvent()->getLayout()->getUpdate();
            $update->addHandle('amp_non_amp_page');
        }
    }

    /**
     * @var $observer
     * @return void
     */
    public function controllerActionLayoutRenderBefore($observer)
    {
        /**
         * Get pramp helper and check its status
         */
        $helper = Mage::helper('pramp');
        if (!$helper->moduleEnabled()) {
            return;
        }

        $layout = Mage::app()->getLayout();
        /**
         * Set root teplate
         */
        if($helper->isAmpRequest()) {
            $layout->getBlock('root')->setTemplate('pramp/1column.phtml');

            $contentBlock = $layout->getBlock('content');
            if ($contentBlock && !$helper->isEsiRequest()) {
                $allowedBlocks = $contentBlock->getAllowedBlocks();
                if ($allowedBlocks) {
                    $allowedBlocks = explode(',', $allowedBlocks);
                } else {
                    $allowedBlocks = array();
                }

                $allowedBlocks = array_merge($allowedBlocks,
                    array('category.products', 'product.info', 'cms.wrapper', 'page_content_heading')
                );

                foreach ($contentBlock->getChild() as $child) {
                    $blockName = $child->getNameInLayout();
                    if (!in_array($blockName, $allowedBlocks)) {
                        $contentBlock->unsetChild($blockName);
                    }

                }
            }
        }
    }

    public function responseSendBefore($observer)
    {
        /**
         * Get pramp helper and check its status
         */
        $helper = Mage::helper('pramp');
        if (!$helper->moduleEnabled()) {
            return;
        }

        if ($helper->isAmpRequest()) {

            $response = $observer->getResponse();
            $html = $response->getBody();

            $html = str_ireplace(
                array('<img','<video','/video>','<audio','/audio>'),
                array('<amp-img','<amp-video','/amp-video>','<amp-audio','/amp-audio>'),
                $html
            );

            $html = preg_replace('/<amp-img(.*?)\/{1}>/', '<amp-img$1></amp-img>',$html);

            $html = preg_replace(
                '/(style|itemprop|itemscope|itemtype|onclick|border|vocab|typeof|container)="[a-zA-Z0-9:\s#-.;!\/]*"/',
                '',
                $html); // do not remove "content", "id", "property", "title"

            $html = preg_replace(
                '/<span.*(content)="[a-zA-Z0-9:\s#-.;!\/]*"/',
                '',
                $html);

            $html =  str_replace('<link  href="In stock">', '', $html);
            $html = preg_replace(
                array(
                    '#<script((?!ampproject|application\/ld\+json).)*>.*</script>#isU',
                    '#<form.*>.*<\/form>#isU',
                    '#<link\s+href="https:\/\/schema\.org\/[a-zA-Z0-9_\-\/\?\&]*"\s?>#isU',
                ),
                '', $html);

            $html = preg_replace('#<amp-img((?(?!width).)*)>\s*</amp-img>#isU', '<amp-img$1 height="100" width="290" ></amp-img>',$html);

            $response->setBody($html);
        }
    }
}