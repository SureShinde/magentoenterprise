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
 * @package    Mage_Usa
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Fedex shipping rates estimation
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author      Magento Core Team <core@magentocommerce.com>
 */

/*
 @category    Webshopapps
* @package    Webshopapps_Desttype
* @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
* @license    http://www.webshopapps.com/license/license.txt
* @author     WebShopApps <sales@webshopapps.com>
*/
class Webshopapps_Desttype_Model_Usa_Shipping_Carrier_Fedex
    extends Mage_Usa_Model_Shipping_Carrier_Fedex
{
    /**
     * Forming request for rate estimation depending to the purpose
     * Taken from 1.7.0.0 Magento
     *
     * @param string $purpose
     * @return array
     */
    protected function _formRateRequest($purpose)
    {
        $r = $this->_rawRequest;
        $upsDestType = $this->_request->getUpsDestType();
        $destType = $upsDestType == 'RES' ? 1 : 0;
        $ratesRequest = array(
            'WebAuthenticationDetail' => array(
                'UserCredential' => array(
                    'Key'      => $r->getKey(),
                    'Password' => $r->getPassword()
                )
            ),
            'ClientDetail' => array(
                'AccountNumber' => $r->getAccount(),
                'MeterNumber'   => $r->getMeterNumber()
            ),
            'Version' => $this->getVersionInfo(),
            'RequestedShipment' => array(
                'DropoffType'   => $r->getDropoffType(),
                'ShipTimestamp' => date('c'),
                'PackagingType' => $r->getPackaging(),
                'TotalInsuredValue' => array(
                    'Amount'  => $r->getValue(),
                    'Currency' => $this->getCurrencyCode()
                ),
                'Shipper' => array(
                    'Address' => array(
                        'PostalCode'  => $r->getOrigPostal(),
                        'CountryCode' => $r->getOrigCountry()
                    )
                ),
                'Recipient' => array(
                    'Address' => array(
                        'PostalCode'  => $r->getDestPostal(),
                        'CountryCode' => $r->getDestCountry(),
                        'Residential' => $destType //(bool)$this->getConfigData('residence_delivery')
                    )
                ),
                'ShippingChargesPayment' => array(
                    'PaymentType' => 'SENDER',
                    'Payor' => array(
                        'AccountNumber' => $r->getAccount(),
                        'CountryCode'   => $r->getOrigCountry()
                    )
                ),
                'CustomsClearanceDetail' => array(
                    'CustomsValue' => array(
                        'Amount' => $r->getValue(),
                        'Currency' => $this->getCurrencyCode()
                    )
                ),
                'RateRequestTypes' => 'LIST',
                'PackageCount'     => '1',
                'PackageDetail'    => 'INDIVIDUAL_PACKAGES',
                'RequestedPackageLineItems' => array(
                    '0' => array(
                        'Weight' => array(
                            'Value' => (float)$r->getWeight(),
                            'Units' => 'LB'
                        ),
                        'GroupPackageCount' => 1,
                    )
                )
            )
        );

        if ($purpose == self::RATE_REQUEST_GENERAL) {
            $ratesRequest['RequestedShipment']['RequestedPackageLineItems'][0]['InsuredValue'] = array(
                'Amount'  => $r->getValue(),
                'Currency' => $this->getCurrencyCode()
            );
        } else if ($purpose == self::RATE_REQUEST_SMARTPOST) {
            $ratesRequest['RequestedShipment']['ServiceType'] = self::RATE_REQUEST_SMARTPOST;
            $ratesRequest['RequestedShipment']['SmartPostDetail'] = array(
                'Indicia' => ((float)$r->getWeight() >= 1) ? 'PARCEL_SELECT' : 'PRESORTED_STANDARD',
                'HubId' => $this->getConfigData('smartpost_hubid')
            );
        }

        return $ratesRequest;
    }

    /**
     * Do remote request for FedEx and handle errors for Magento 1.6 and earlier
     *
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _getQuotes()
    {
        //If 1.7 or greater use Magento default function
        if(Mage::helper('wsacommon')->getNewVersion() > 11){
            return parent::_getQuotes();
        }

        $r = $this->_rawRequest;
        $upsDestType = $this->_request->getUpsDestType();
        $destType = $upsDestType == 'RES' ? 1 : 0;
        $ratesRequest = array(
            'WebAuthenticationDetail' => array(
                'UserCredential' => array(
                    'Key'      => $r->getKey(),
                    'Password' => $r->getPassword()
                )
            ),
            'ClientDetail' => array(
                'AccountNumber' => $r->getAccount(),
                'MeterNumber'   => $r->getMeterNumber()
            ),
            'Version' => $this->getVersionInfo(),
            'RequestedShipment' => array(
                'DropoffType'   => $r->getDropoffType(),
                'ShipTimestamp' => date('c'),
                'PackagingType' => $r->getPackaging(),
                'TotalInsuredValue' => array(
                    'Ammount'  => $r->getValue(),
                    'Currency' => $this->getCurrencyCode()
                ),
                'Shipper' => array(
                    'Address' => array(
                        'PostalCode'  => $r->getOrigPostal(),
                        'CountryCode' => $r->getOrigCountry()
                    )
                ),
                'Recipient' => array(
                    'Address' => array(
                        'PostalCode'  => $r->getDestPostal(),
                        'CountryCode' => $r->getDestCountry(),
                        'Residential' => $destType//(bool)$this->getConfigData('residence_delivery')
                    )
                ),
                'ShippingChargesPayment' => array(
                    'PaymentType' => 'SENDER',
                    'Payor' => array(
                        'AccountNumber' => $r->getAccount(),
                        'CountryCode'   => $r->getOrigCountry()
                    )
                ),
                'RateRequestTypes' => 'LIST',
                'PackageCount'     => '1',
                'PackageDetail'    => 'INDIVIDUAL_PACKAGES',
                'RequestedPackageLineItems' => array(
                    '0' => array(
                        'Weight' => array(
                            'Value' => (float)$r->getWeight(),
                            'Units' => 'LB'
                        )
                    )
                )
            )
        );
        $requestString = serialize($ratesRequest);
        $response = $this->_getCachedQuotes($requestString);
        $debugData = array('request' => $ratesRequest);
        if ($response === null) {
            try {
                $client = $this->_createRateSoapClient();
                $response = $client->getRates($ratesRequest);
                $this->_setCachedQuotes($requestString, serialize($response));
                $debugData['result'] = $response;
            } catch (Exception $e) {
                $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
                Mage::logException($e);
            }
        } else {
            $response = unserialize($response);
            $debugData['result'] = $response;
        }
        $this->_debug($debugData);
        return $this->_prepareRateResponse($response);
    }
}
