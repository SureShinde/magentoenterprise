<?php
/**
 * @category   CLS
 * @package    CustomerAdvanced
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

class CLS_CustomerAdvanced_Block_Customer_Quickform_Register extends Mage_Customer_Block_Form_Register
{
    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return Mage::getUrl('customer/quickaccount/createpost');
    }

    /**
     * Determine the URL to redirect the customer to after they log in
     */
    public function getSuccessUrl()
    {
        $url = parent::getSuccessUrl();
        if (is_null($url) && !(bool)Mage::getStoreConfig(Mage_Customer_Model_Customer::XML_PATH_IS_CONFIRM)) {
            $url = Mage::helper('core/url')->getCurrentUrl();
        }
        return $url;
    }
}