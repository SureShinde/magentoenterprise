<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Amp
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

/**
 * require parent controller class
 */
require_once(Mage::getModuleDir('controllers','Mage_Checkout') . DS . 'CartController.php');

class Plumrocket_Amp_CartController extends Mage_Checkout_CartController
{
    /**
     * Replace parent Validate Form Key method if module enabled
     * @return bool
     */
    protected function _validateFormKey()
    {
        /**
         * Get pramp helper and check its status
         */
        $helper = Mage::helper('pramp');
        if(!$helper->moduleEnabled()) {
            return parent::_validateFormKey();
        }

        return true;
    }
}
