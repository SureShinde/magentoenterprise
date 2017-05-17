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
 * @package    Cart
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

class Nucleus_Cart_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CONFIG_PATH_ENABLE_AJAX = 'checkout/ajax_cart/enable';

    /**
     * Check whether Ajax Add to Cart is enabled
     *
     * @return bool
     */
    public function ajaxCartEnabled()
    {
        return (bool) Mage::getStoreConfig(self::CONFIG_PATH_ENABLE_AJAX);
    }

    /**
     * Get URL for Ajax Add to Cart
     *
     * @return string
     */
    public function getAjaxAddUrl()
    {
        return Mage::getUrl('checkout/cart/ajaxAdd');
    }
}
