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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Shipestimate
 * User         Genevieve Eddison
 * Date         25 June 2013
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */


class Webshopapps_Shipestimate_Block_Catalog_Shipresults extends Mage_Catalog_Block_Product_Abstract
{
    protected $_results;

    protected $_quoter;


    public function setResults($rates)
    {
        $this->_results = $rates;
    }

    public function getResults()
    {
        return $this->_results;
    }

    public function getCarrier($code)
    {
        if ($carrier = Mage::getSingleton('shipping/config')->getCarrierInstance($code)) {
            return Mage::getStoreConfig('carriers/'.$code.'/title');
        } else {
            return $code;
        }
    }

    public function getAdditionalDisplay($rates, $code)
    {
        if($block = $this->helper('shipestimate')->getAdditionalDisplay($rates, $code)) {
            $count = 1;
            foreach($block as $block => $template) {
                $addtionalDisplay = $this->getLayout()->createBlock(
                    $block,
                    sprintf('%s.additional'.$count++, $this->getNameInLayout())
                );
                $result=  $addtionalDisplay->setRates($rates)
                    ->setCarrierCode($code)
                    ->setTemplate($template)
                    ->toHtml();
            }
            return $result;
        }
    }

    public function getShippingPrice($ratePrice, $taxIncl)
    {
        return $this->formatDisplayPrice(
            $this->helper('tax')->getShippingPrice(
                $ratePrice,
                $taxIncl,
                $this->getQuoter()
                    ->getQuote()
                    ->getShippingAddress()
            )
        );
    }

    public function formatDisplayPrice($price)
    {
        return $this->getQuoter()
            ->getQuote()
            ->getStore()
            ->convertPrice($price, true);
    }

    public function getShippingSavings($savings)
    {
        if($savings > 0) {
            return Mage::helper('shipestimate')->__('save ') .$this->formatDisplayPrice($savings) ;
        }
        else {
            return '';
        }
    }

    protected function getQuoter()
    {
        if ($this->_quoter === null) {
            $this->_quoter = Mage::getSingleton('apishipping/quote_quoteshipping');
        }

        return $this->_quoter;
    }

}
