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

class AW_Followupemail_Model_Coupons extends Mage_SalesRule_Model_Coupon
{
    public function fueLoadByRules($fueRule)
    {
        $prefix = $fueRule->getCouponPrefix();
        $expires = date('Y-m-d', strtotime('+' . ((int)$fueRule->getCouponExpireDays()) . ' day', time()));
        $coupons = $this->getCollection();
        $coupons->addRuleToFilter($fueRule->getCouponSalesRuleId());
        $coupons->getSelect()
            ->where("code LIKE ?", $prefix . dechex($fueRule->getId()) . 'X%')
            ->where("expiration_date = ?", $expires);

        foreach ($coupons as $coupon) {
            return $coupon;
        }
        return null;
    }

    public function fueLoadByCode($code)
    {
        $this->load($code, 'code');
        return $this;
    }
}
