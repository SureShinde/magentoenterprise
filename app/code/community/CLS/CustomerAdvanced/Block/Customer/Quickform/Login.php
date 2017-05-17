<?php
/**
 * @category   CLS
 * @package    CustomerAdvanced
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

class CLS_CustomerAdvanced_Block_Customer_Quickform_Login extends Mage_Customer_Block_Form_Login
{
    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        $params = array();
        if (Mage::app()->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME)) {
            $params = array(
                Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME => Mage::app()->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME)
            );
        }
        return Mage::getUrl('customer/quickaccount/loginPost', $params);
    }
}