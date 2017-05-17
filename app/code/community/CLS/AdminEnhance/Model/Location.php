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

class CLS_AdminEnhance_Model_Location extends Mage_Core_Model_Abstract
{
    const SELECTOR_CONTEXT_TOP = 'top';
    const SELECTOR_CONTEXT_BOTTOM = 'bottom';
    const SELECTOR_CONTEXT_BEFORE = 'before';
    const SELECTOR_CONTEXT_AFTER = 'after';

    protected function _construct()
    {
        parent::_construct();
        $this->_init('cls_adminenhance/location');
    }

    public function getSelectorContext()
    {
        $context = $this->getData('selector_context');
        if (!$context) {
            $context = self::SELECTOR_CONTEXT_TOP;
        }
        return $context;
    }

    /**
     * Get the banner this location belongs to
     *
     * @return CLS_AdminEnhance_Model_Banner
     */
    public function getBanner()
    {
        if (!$this->hasData('banner')) {
            $banner = Mage::getModel('cls_adminenhance/banner')->load($this->getBannerId());
            $this->setData('banner', $banner);
        }
        return $this->getData('banner');
    }

    /**
     * Make sure the selector context set on this location is a valid value
     */
    protected function _validateSelectorContext()
    {
        $context = $this->getData('selector_context');
        if (!$context) {
            return;
        }

        switch ($context) {
            case self::SELECTOR_CONTEXT_TOP:
            case self::SELECTOR_CONTEXT_BOTTOM:
            case self::SELECTOR_CONTEXT_BEFORE:
            case self::SELECTOR_CONTEXT_AFTER:
                break;
            default:
                Mage::throwException(Mage::helper('cls_adminenhance')->__('Invalid selector context for banner location record'));
        }
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $this->_validateSelectorContext();

        parent::_beforeSave();
        $urlPath = $this->getUrlPath();
        $urlPath = trim($urlPath, '/');
        $this->setUrlPath($urlPath);

        return $this;
    }
}
