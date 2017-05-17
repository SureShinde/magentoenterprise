<?php
/**
 * @category   CLS
 * @package    CustomerAdvanced
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */
class CLS_CustomerAdvanced_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_customerLoggedIn = null;

    /**
     * Whether customer is logged in
     *
     * @return bool
     */
    public function customerLoggedIn()
    {
        if (is_null($this->_customerLoggedIn)) {
            $this->_customerLoggedIn = Mage::getSingleton('customer/session')->isLoggedIn();
        }
        return $this->_customerLoggedIn;
    }
}