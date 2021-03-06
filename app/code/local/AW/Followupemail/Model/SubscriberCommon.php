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
 * @package    AW_Followupemail
 * @version    3.6.4
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

/**
 * Compatibility with TBT_Rewards
 */
if (!class_exists('AW_Followupemail_Model_SubscriberCommon')) {
    if (Mage::helper('followupemail')->isTBTRewardsInstalled()) {
        class AW_Followupemail_Model_SubscriberCommon extends TBT_Rewards_Model_Newsletter
        {
        }
    } else {
        class AW_Followupemail_Model_SubscriberCommon extends Mage_Newsletter_Model_Subscriber
        {
        }
    }
}
