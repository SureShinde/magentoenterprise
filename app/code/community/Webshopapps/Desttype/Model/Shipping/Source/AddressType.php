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
/*
 * Webshopapps UPS Address Validator Extension
 *
 * @author 	Webshopapps
 * @license www.webshopapps.com/license/license.txt
 *  @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * (c) Webshopapps.com Zowta Ltd 2012 - All rights reserved.
 */

class Webshopapps_Desttype_Model_Shipping_Source_AddressType
{
    public function toOptionArray() {

        $desttype = Mage::getSingleton('desttype/shipping_desttype');
        $arr = array();

        if(Mage::getStoreConfig('shipping/desttype/default_address')) {
            $code = 'address_type_description_reverse';
        } else {
            $code = 'address_type_description';
        }

        $types = $desttype->getCode($code);

        if(is_array($types)) {
            foreach ($types as $k=>$v) {
                $arr[] = array('value'=>$k, 'label'=>Mage::helper('usa')->__($v));
            }
        }

        return $arr;
    }
}