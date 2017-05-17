<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento enterprise edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_AlsoViewed
 * @version    1.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_AlsoViewed_Model_Observer extends Varien_Event_Observer
{
    /**
     * On view product action
     * @param Varien_Event_Observer $observer
     * @return AW_AlsoViewed_Model_Observer
     */
    public function onViewProduct(Varien_Event_Observer $observer) {
        if (!Mage::getStoreConfig('aw_alsoviewed/general/enabled')) {
            return $this;
        }

        /** @var $product Mage_Catalog_Model_Product */
        $product = $observer->getProduct();

        Mage::getModel('aw_alsoviewed/history')
            ->setProductId($product->getId())
            ->save();

        return $this;
    }
}
