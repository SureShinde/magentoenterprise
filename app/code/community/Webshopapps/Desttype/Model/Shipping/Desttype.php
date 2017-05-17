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
 * @package    Mage_Shipping
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/*
 * Webshopapps Address Type Extension
 *
 * @author 	Karen Baker @ WebShopApps
 * @license www.webshopapps.com/license/license.txt
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * (c) Webshopapps.com Zowta Ltd 2012 - All rights reserved.
 */

class Webshopapps_Desttype_Model_Shipping_Desttype extends Mage_Core_Model_Abstract
{

    public function getCode($type, $code='')
    {
        $resLabel = Mage::helper('usa')->__(trim(Mage::getStoreConfig('shipping/desttype/residential_label')));
        $comLabel = Mage::helper('usa')->__(trim(Mage::getStoreConfig('shipping/desttype/commercial_label')));

        $resLabel = $resLabel=='' ? Mage::helper('usa')->__('Residential') : $resLabel;
        $comLabel = $comLabel=='' ? Mage::helper('usa')->__('Commercial') : $comLabel;

        $codes = array(
            'address_type_description'=>array(
                'RES'    	=> $resLabel,
                'COM'    	=> $comLabel,
            ),
            'address_type_description_reverse'=>array(
                'COM'    	=> $comLabel,
                'RES'    	=> $resLabel,
            ),
            'address_type_admin_descr'=>array(
                'RES'    	=> $resLabel,
                'COM'    	=> $comLabel,
            ),
            'address_type_classification'=>array(
                '0'    => 'Unknown',
                '1'    => 'COM',
                '2'    => 'RES',
            ),
        );

        if (!isset($codes[$type])) {
            return false;
        } elseif (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }

}