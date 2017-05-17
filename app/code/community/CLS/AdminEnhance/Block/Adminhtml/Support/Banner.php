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

class CLS_AdminEnhance_Block_Adminhtml_Support_Banner extends Mage_Adminhtml_Block_Template
{

    protected function _construct()
    {
        $this->setTemplate('cls/adminenhance/support/banner.phtml');

        parent::_construct();
    }

    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();

        // Add the videos block
        $videosBlock = $this->getLayout()->createBlock('cls_adminenhance/adminhtml_support_videos')
            ->setBanner($this->getBanner())
            ->setRestrictTitles(true);
        $this->setChild('banner_videos', $videosBlock);
    }

    /**
     * Get the attached banner model
     *
     * @return CLS_AdminEnhance_Model_Banner
     */
    public function getBanner()
    {
        if (!$this->hasData('banner')) {
            if ($this->hasData('banner_id')) {
                $banner = Mage::getModel('cls_adminenhance/banner')->load($this->getBannerId());
                if ($banner->getId()) {
                    $this->setData('banner', $banner);
                }
            }
        }

        return $this->getData('banner');
    }

    /**
     * Determine whether the attached banner is collapsed for this user
     *
     * @return bool
     */
    public function bannerIsCollapsed()
    {
        if (!$this->hasData('location_id')) {
            return false;
        }

        $sessionCollapsedLocations = Mage::helper('cls_adminenhance')->getSessionCollapsedLocations();
        return (isset($sessionCollapsedLocations[$this->getLocationId()]));
    }
}