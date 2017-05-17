<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
/*
 *  WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Apishipping
 * User         Genevieve Eddison
 * Date         17 June 2013
 * Time         11:45
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 */

abstract class Webshopapps_Apishipping_Model_Quote_Api2_Quote_Rest extends Webshopapps_Apishipping_Model_Quote_Api2_Quote
{
    protected $_debug;

    /**
     * Retrieve quote of shipping charges
     *
     * @throws Mage_Api2_Exception
     * @return array
     */
    //TO DO change this back to protected
    protected function _retrieve()
    {
        $requestData = $this->getRequest()->getParam('id');
        $virtualCartData = $this->checkFormat($requestData);

        $validator = Mage::getModel('apishipping/quote_validator', array(
            'operation' => self::OPERATION_RETRIEVE
        ));

        if (!$validator->isValidData($virtualCartData)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }


        $shippingQuoter = Mage::getModel('apishipping/quote_quoteshipping');
        $debugData = array('request' => $virtualCartData);

        try {
            $quotedRates = $this->formatRates($shippingQuoter->quoteShipping($virtualCartData));
            $debugData['result'] = $quotedRates;
        } catch (Mage_Api2_Exception $e) {
            $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
            if ($this->isDebug()) {
                Mage::helper('wsacommon/log')->postCritical('apishipping', 'Mage API2 Exception from API Shipping', $e->getMessage());
            }
            $this->_error($debugData, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        catch (Exception $e) {
            $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
            Mage::logException($e);
            if ($this->isDebug()) {
                Mage::helper('wsacommon/log')->postCritical('apishipping', 'Exception from API Shipping', $e->getMessage());
            }
            $this->_error($debugData, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        if ($this->isDebug()) {
            Mage::helper('wsacommon/log')->postMinor('apishipping', 'Shipping Quote API response', $debugData);
        }
        return $quotedRates;

    }

    protected function checkFormat($virtualCartData)
    {
        parse_str($virtualCartData, $testData);
        return $testData;
    }

    protected function formatRates($rates)
    {
        $options = array();
        foreach ($rates as $rate) {
            foreach ($rate as $shipQuote) {
                $options[] = array('Carrier' => $shipQuote['carrier_title'],
                    'Type' => $shipQuote['method_title'],
                    'Cost' => $shipQuote['price']);
            }
        }
        return array($options);

    }

    protected function isDebug()
    {
        if (!($this->_debug)) {
            $this->_debug = $this->getConfig('shipping/apishipping/debug');
        }
        return $this->_debug;
    }

}