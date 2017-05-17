<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */


class Amasty_SeoRichData_Model_Observer
{
    public function onAdminhtmlInitSystemConfig($observer)
    {
        if (!Mage::helper('amseorichdata')->isYotpoReviewsEnabled()) {
            $observer->getConfig()->setNode(
                'sections/amseorichdata/groups/yotpo', false, true
            );
        }
    }
}
