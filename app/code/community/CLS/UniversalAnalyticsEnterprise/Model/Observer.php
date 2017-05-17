<?php
/**
 * @category   CLS
 * @package    UniversalAnalyticsEnterprise
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

class CLS_UniversalAnalyticsEnterprise_Model_Observer
{
    /**
     * Add order information into Universal Analytics block to render on checkout success pages
     *
     * @param Varien_Event_Observer $observer
     */
    public function setUniversalAnalyticsOnOrderSuccessPageView(Varien_Event_Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('google_analyticsuniversal');
        if ($block) {
            $block->setOrderIds($orderIds);
        }
    }
}
