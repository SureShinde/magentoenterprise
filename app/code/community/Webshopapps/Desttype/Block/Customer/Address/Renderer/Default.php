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
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Address format renderer default
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
 /*
  * Webshopapps Residential Delivery Extension
  * 
  * @author 	Webshopapps
  * @license www.webshopapps.com/license/license.txt
  * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
  * (c) Webshopapps.com Zowta Ltd 2010 - All rights reserved.
 */
class Webshopapps_Desttype_Block_Customer_Address_Renderer_Default extends Mage_Customer_Block_Address_Renderer_Default implements Mage_Customer_Block_Address_Renderer_Interface
{

 	public function render(Mage_Customer_Model_Address_Abstract $address, $format=null)
    {
        $address->getRegion();
        $address->getCountry();
        $address->explodeStreetAddress();

        $formater = new Varien_Filter_Template();
        $data = $address->getData();
        if ($this->getType()->getHtmlEscape()) {
            foreach ($data as $key => $value) {
                if (is_object($value)) {
                    unset($data[$key]);
                } else {
                    $data[$key] = $this->htmlEscape($value);
                }
            }
        }
        $formater->setVariables(array_merge($data, array('country'=>$address->getCountryModel()->getName())));
        $formater->setVariables(array_merge($data, array('dest_string'=>Mage::getModel('usa/shipping_carrier_ups_source_desttype')->getDestString($address->getDestType()))));
   
        $format = !is_null($format) ? $format : $this->getFormat($address);

        return $formater->filter($format);
   	}

}
