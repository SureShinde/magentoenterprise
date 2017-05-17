<?php
/**
 * Observer.php
 *
 * @category    CLS
 * @package     Custom
 * @author      Brendan Tull <brendan.tull@classyllama.com>
 * @copyright   Copyright (c) 2016 Classy Llama Studios, LLC
 */

class CLS_Custom_Model_Observer
{
    /**
     * Set cookie for Google Analytics to track user-account creation.
     *
     * @param $observer
     */
    public function accountCreationSetCookie($observer)
    {
        // getIsNewCustomer comes from AW_Followupemail
        if ($observer->getCustomer()->getIsNewCustomer()) {
            Mage::getSingleton('core/cookie')->set('google_conversion_label', Mage::helper('cls_custom')->getAccountCreateLabel(), 300, null, null, null, false);
        }
    }
}
