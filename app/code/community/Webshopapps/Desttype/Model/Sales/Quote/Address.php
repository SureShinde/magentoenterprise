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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales Quote address model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
 /*
 @category   Webshopapps
* @package    Webshopapps_Desttype
* @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
* @license    http://www.webshopapps.com/license/license.txt
* @author     Karen Baker <sales@webshopapps.com
*/
class Webshopapps_Desttype_Model_Sales_Quote_Address extends OnePica_AvaTax_Model_Sales_Quote_Address
{
	private $_debug;

    /**
     * Import address data from order address
     *
     * @param   Mage_Sales_Model_Order_Address $address
     * @return  Mage_Sales_Model_Quote_Address
     */
    public function importOrderAddress(Mage_Sales_Model_Order_Address $address)
    {

        if (!Mage::getStoreConfig('shipping/desttype/active')) {
     		return parent::importOrderAddress($address);
     	}
        $this->setAddressType($address->getAddressType())
            ->setCustomerId($address->getCustomerId())
            ->setCustomerAddressId($address->getCustomerAddressId())
            ->setDestType($address->getDestType())
            ->setEmail($address->getEmail());

        Mage::helper('core')->copyFieldset('sales_convert_order_address', 'to_quote_address', $address, $this);

        return $this;
    }


    /**
     * Import quote address data from customer address object
     *
     * @param   Mage_Customer_Model_Address $address
     * @return  Mage_Sales_Model_Quote_Address
     */
     public function importCustomerAddress(Mage_Customer_Model_Address $address)
     {
         	if (!Mage::getStoreConfig('shipping/desttype/active')) {
     			return parent::importCustomerAddress($address);
     		}
            Mage::helper('core')->copyFieldset('customer_address', 'to_quote_address', $address, $this);
            $email = null;
            if ($address->hasEmail()) {
                $email =  $address->getEmail();
            }
            elseif ($address->getCustomer()) {
                $email = $address->getCustomer()->getEmail();
            }
            if ($email) {
                $this->setEmail($email);
            }
            $this->setDestType($address->getDestType());

            return $this;
    }

    public function requestShippingRates(Mage_Sales_Model_Quote_Item_Abstract $item = null)
    {
        if (!Mage::getStoreConfig('shipping/desttype/active')) {
            return parent::requestShippingRates();
        }

        $this->_debug = Mage::helper('wsalogger')->isDebug('Webshopapps_Desttype');

        if (!Mage::helper('wsacommon')->checkItems('c2hpcHBpbmcvZGVzdHR5cGUvc2hpcF9vbmNl','aW1hZ2luZXRoaXM=','c2hpcHBpbmcvZGVzdHR5cGUvc2VyaWFs')) {
            Mage::helper('wsalogger/log')->postCritical('desttype',base64_decode('TGljZW5zZQ=='),base64_decode('U2VyaWFsIEtleSBJbnZhbGlk'));
            return parent::requestShippingRates();
        }

        /** @var $request Mage_Shipping_Model_Rate_Request */
        $request = Mage::getModel('shipping/rate_request');
        $request->setAllItems($item ? array($item) : $this->getAllItems());
        $request->setDestCountryId($this->getCountryId());
        $request->setDestRegionId($this->getRegionId());
        $request->setDestRegionCode($this->getRegionCode());
        /**
         * need to call getStreet with -1
         * to get data in string instead of array
         */
        $request->setDestStreet($this->getStreet(-1));
        $request->setDestCity($this->getCity());
        $request->setDestPostcode($this->getPostcode());
        $request->setPackageValue($item ? $item->getBaseRowTotal() : $this->getBaseSubtotal());
        $packageValueWithDiscount = $item
            ? $item->getBaseRowTotal() - $item->getBaseDiscountAmount()
            : $this->getBaseSubtotalWithDiscount();
        $request->setPackageValueWithDiscount($packageValueWithDiscount);
        $request->setPackageWeight($item ? $item->getRowWeight() : $this->getWeight());
        $request->setPackageQty($item ? $item->getQty() : $this->getItemQty());

        // start wsa changes
        $destType=$this->getData('dest_type');
        if ( $destType!="" && Mage::helper('desttype')->isDestTypeActive($this->getBaseSubtotalWithDiscount(),
            $this->getQuote()->getCustomerGroupId()) ) {
            if ($this->_debug) {
                Mage::helper('wsalogger/log')->postDebug('desttype','set dest type',$destType);
            }
            $request->setUpsDestType($destType);
        }

        $request->setLiftgateRequired($this->getLiftgateRequired());
        $request->setNotifyRequired($this->getNotifyRequired());
        $request->setInsideDelivery($this->getInsideDelivery());

        if ($destType!="") {
            $shipToType = $destType;
        } else {
            $shipToType= $this->getShiptoType();
        }

        if ($shipToType == '0' || $shipToType =="RES" ) {
            $request->setShiptoType('Residential'); //TODO tidy up
        } else {
            $request->setShiptoType($shipToType);
        }
        //finished wsa changes

        /**
         * Need for shipping methods that use insurance based on price of physical products
         */
        $packagePhysicalValue = $item
            ? $item->getBaseRowTotal()
            : $this->getBaseSubtotal() - $this->getBaseVirtualAmount();
        $request->setPackagePhysicalValue($packagePhysicalValue);

        $request->setFreeMethodWeight($item ? 0 : $this->getFreeMethodWeight());

        /**
         * Store and website identifiers need specify from quote
         */
        /*$request->setStoreId(Mage::app()->getStore()->getId());
        $request->setWebsiteId(Mage::app()->getStore()->getWebsiteId());*/

        $request->setStoreId($this->getQuote()->getStore()->getId());
        $request->setWebsiteId($this->getQuote()->getStore()->getWebsiteId());
        $request->setFreeShipping($this->getFreeShipping());
        /**
         * Currencies need to convert in free shipping
         */
        $request->setBaseCurrency($this->getQuote()->getStore()->getBaseCurrency());
        $request->setPackageCurrency($this->getQuote()->getStore()->getCurrentCurrency());
        $request->setLimitCarrier($this->getLimitCarrier());

        $request->setBaseSubtotalInclTax($this->getBaseSubtotalInclTax());

        $result = Mage::getModel('shipping/shipping')->collectRates($request)->getResult();

        $found = false;
        if ($result) {
            $shippingRates = $result->getAllRates();

            foreach ($shippingRates as $shippingRate) {
                $rate = Mage::getModel('sales/quote_address_rate')
                    ->importShippingRate($shippingRate);
                if (!$item) {
                    $this->addShippingRate($rate);
                }

                if ($this->getShippingMethod() == $rate->getCode()) {
                    if ($item) {
                        $item->setBaseShippingAmount($rate->getPrice());
                    } else {
                        /**
                         * possible bug: this should be setBaseShippingAmount(),
                         * see Mage_Sales_Model_Quote_Address_Total_Shipping::collect()
                         * where this value is set again from the current specified rate price
                         * (looks like a workaround for this bug)
                         */
                        $this->setShippingAmount($rate->getPrice());
                    }

                    $found = true;
                }
            }
        }
        return $found;
    }
}