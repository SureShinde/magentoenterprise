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

class AW_AlsoViewed_Model_Cron extends Mage_Core_Model_Abstract
{
    /**
     * On cron run
     * @return AW_AlsoViewed_Model_Cron
     */
    public function run() {
        $lifetime = Mage::getStoreConfig('aw_alsoviewed/general/session_lifetime');

        /** @var $collection AW_AlsoViewed_Model_Mysql4_History_Collection */
        $collection = Mage::getModel('aw_alsoviewed/history')->getCollection();
        $collection->getSelect()
            ->where("main_table.created_at < ?",  Zend_Date::now()->subMinute($lifetime)->getIso());

        $sessions = array();
        foreach ($collection as $item) {
            $sessions[$item->getSessionId()][] = $item->getProductId();
        }

        $sessionsIds = array_keys($sessions);
        $sessions = array_filter($sessions, array($this, 'filterSessions'));

        /** @var $product AW_AlsoViewed_Model_Product */
        $product = Mage::getModel('aw_alsoviewed/product');

        foreach ($sessions as $products) {
            for ($i = 0; $i < count($products); $i++) {
                for ($j = 0; $j < count($products); $j++) {
                    if ($i != $j) {
                        $product->unsetData()
                                ->setProductId($products[$i])
                                ->setLinkProductId($products[$j])
                                ->save();
                    }
                }
            }
        }

        /** @var $resource AW_AlsoViewed_Model_Mysql4_History */
        $resource = Mage::getResourceModel('aw_alsoviewed/history');
        $resource->clearHistoryBySessions($sessionsIds);

        return $this;
    }

    protected function filterSessions($item) {
        return count($item) > 1;
    }
}
