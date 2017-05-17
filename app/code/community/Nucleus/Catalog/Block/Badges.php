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
 * @category   Nucleus
 * @package    Catalog
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

class Nucleus_Catalog_Block_Badges extends Mage_Core_Block_Template
{

    const CACHE_TAG = 'nucleus_catalog_badges';

    static public $attributeBadgesOptions = null;

    /**
     * Class constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();

         // Set cache data
        $this->addCacheTag(array(
            self::CACHE_TAG
        ));

        // Init badge attribute options
        if (is_null(self::$attributeBadgesOptions)) {
            $badges = Mage::getModel('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'badges');
            if ($badges->usesSource()) {
                $badgesOptions = $badges->getSource()->getAllOptions(false);

                if (!empty($badgesOptions)) {
                    self::$attributeBadgesOptions = array();

                    foreach ($badgesOptions as $badgesOption) {
                        self::$attributeBadgesOptions[$badgesOption['value']] = $badgesOption['label'];
                    }
                }
            }
        }
    }

    public function scrubBadgeLabel($label)
    {
        return preg_replace("#[^a-zA-Z0-9-]#", "-", strtolower($label));
    }

    /**
     * Get product entity
     *
     * @return Mage_Catalog_Model_Product|null
     */
    public function getProduct()
    {
        if ((!$this->hasData('product') || !($this->getData('product') instanceof Mage_Catalog_Model_Product))
            && $product = Mage::registry('current_product')) {
            // If no product set outside the block, try to get one from the registry
            $this->setData('product', $product);
        }

        return $this->getData('product');
    }

    /**
     * Get badges applicable to the current product
     *
     * @return array
     */
    public function getApplicableBadges()
    {
        $badges = array();

        if ($product = $this->getProduct()) {
            $todayStartOfDayDate  = new Zend_Date();
            $todayStartOfDayDate->setTime('00:00:00');

            $todayEndOfDayDate = new Zend_Date();
            $todayEndOfDayDate->setTime('23:59:59');

            // Check for "New" badge
            if (Mage::getStoreConfig(Nucleus_Catalog_Helper_Data::XML_PATH_BADGE_NEW, $product->getStoreId())) {
                $productNewsFromDate = $product->getData('news_from_date');
                $productNewsToDate = $product->getData('news_to_date');

                if ($productNewsFromDate) {
                    $productNewsFromDate = new Zend_Date(strtotime($productNewsFromDate));
                    $productNewsFromDate->setTime('00:00:00');
                } else {
                    $productNewsFromDate = null;
                }

                if ($productNewsToDate) {
                    $productNewsToDate = new Zend_Date(strtotime($productNewsToDate));
                    $productNewsToDate->setTime('23:59:59');
                } else {
                    $productNewsToDate = null;
                }

                if (!is_null($productNewsFromDate) || !is_null($productNewsToDate)) {
                    $fromDateValidated = (is_null($productNewsFromDate) || ($productNewsFromDate->compare($todayStartOfDayDate) <= 0));
                    $toDateValidated = (is_null($productNewsToDate) || ($productNewsToDate->compare($todayEndOfDayDate) >= 0));
                    if ($fromDateValidated && $toDateValidated) {
                        $badges[] = $this->__('New');
                    }
                }
            }

            // Check for "Sale" badge
            if (
                Mage::getStoreConfig(Nucleus_Catalog_Helper_Data::XML_PATH_BADGE_SALE, $product->getStoreId())
                && $product->getData('special_price')
                && ($product->getData('special_price') > 0)
            ) {
                $saleBadge = true;

                $productSpecialFromDate = $product->getData('special_from_date');
                $productSpecialToDate = $product->getData('special_to_date');

                if ($productSpecialFromDate) {
                    $productSpecialFromDate = new Zend_Date(strtotime($productSpecialFromDate));
                    $productSpecialFromDate->setTime('00:00:00');
                } else {
                    $productSpecialFromDate = null;
                }

                if ($productSpecialToDate) {
                    $productSpecialToDate = new Zend_Date(strtotime($productSpecialToDate));
                    $productSpecialToDate->setTime('23:59:59');
                } else {
                    $productSpecialToDate = null;
                }

                if (!is_null($productSpecialFromDate) || !is_null($productSpecialToDate)) {
                    $fromDateValidated = (is_null($productSpecialFromDate) || ($productSpecialFromDate->compare($todayStartOfDayDate) <= 0));
                    $toDateValidated = (is_null($productSpecialToDate) || ($productSpecialToDate->compare($todayEndOfDayDate) >= 0));
                    $saleBadge = ($fromDateValidated && $toDateValidated);
                }

                if ($saleBadge) {
                    $badges[] = $this->__('Sale');
                }
            }

            // Check for attribute-based badges
            if (
                Mage::getStoreConfig(Nucleus_Catalog_Helper_Data::XML_PATH_BADGE_ATTRIBUTE_BASED, $product->getStoreId())
                && ($productBadges = $product->getData('badges'))
                && !is_null(self::$attributeBadgesOptions)
                && !empty(self::$attributeBadgesOptions)
            ) {
                $productBadges = explode(',', $productBadges);

                foreach ($productBadges as $productBadgeId) {
                    if (isset(self::$attributeBadgesOptions[$productBadgeId])) {
                        $badges[] = self::$attributeBadgesOptions[$productBadgeId];
                    }
                }
            }
        }

        return $badges;
    }

}