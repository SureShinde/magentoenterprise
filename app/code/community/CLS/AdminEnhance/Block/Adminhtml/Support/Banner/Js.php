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

class CLS_AdminEnhance_Block_Adminhtml_Support_Banner_Js extends Mage_Adminhtml_Block_Template
{
    /**
     * Get the URL we use to fetch banners
     *
     * @return string
     */
    public function getFetchUrl()
    {
        return Mage::getUrl('adminhtml/support_banners/ajaxFetch');
    }

    /**
     * Get URL for toggling banner collapsed state
     *
     * @return string
     */
    public function getToggleUrl()
    {
        return Mage::getUrl('adminhtml/support_banners/ajaxToggle');
    }

    /**
     * Whether the banner init should be done for the current URL
     *
     * @return bool
     */
    public function shouldInitCurrentUrl()
    {
        if (!$this->getData('should_init_current_url')) {
            $request = Mage::app()->getRequest();

            // Determine if there are any locations matching the current URL
            $locations = Mage::getModel('cls_adminenhance/location')->getCollection()
                ->addUrlFilter($request->getRequestUri());

            $this->setData('should_init_current_url', ($locations->count() > 0));
        }

        return $this->getData('should_init_current_url');
    }
}