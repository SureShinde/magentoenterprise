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
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page checkout status
 *
 * @category   Mage
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */

/*
  @category   Webshopapps
* @package    Webshopapps_Desttype
* @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
* @license    http://www.webshopapps.com/license/license.txt
* @author     Karen Baker <sales@webshopapps.com
*/
class Webshopapps_Desttype_Block_Checkout_Onepage_Billing extends Mage_Checkout_Block_Onepage_Billing
{

    public function getDestTypeHtmlSelect($type)
    {
        return Mage::helper('desttype')->getDestTypeHtmlSelect($this->getLayout(), $this->getAddress()->getDestType(), $type);
    }

    public function autoCommercialAvailable()
    {
        if (Mage::helper('desttype')->extensionEnabled()) {
            return Mage::getStoreConfig('shipping/desttype/auto_company');
        } else return false;
    }
}