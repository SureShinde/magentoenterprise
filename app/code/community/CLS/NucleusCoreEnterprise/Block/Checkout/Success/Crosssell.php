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
 * @package    NucleusCoreEnterprise
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

class CLS_NucleusCoreEnterprise_Block_Checkout_Success_Crosssell extends Enterprise_TargetRule_Block_Checkout_Cart_Crosssell
{

    /**
     * Retrieve quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('cls_nucleuscore/checkout_success')->getQuote();
    }

    /**
     * Retrieve array of cross-sell products for just added product to cart
     *
     * @return array
     */
    protected function _getProductsByLastAddedProduct()
    {
        // We do not have "last added product" anymore so force cross-sells selection for the whole quote
        return array();
    }

}