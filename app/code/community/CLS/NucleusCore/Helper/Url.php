<?php
/**
 * Nucleus
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Nucleus License
 * that is bundled with this package in the file LICENSE_NUCLEUS.txt.
 * It is also available through the World Wide Web at this URL:
 * http://www.nucleuscommerce.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the World Wide Web, please send an email
 * to support@nucleuscommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Nucleus
 * to newer versions in the future.
 *
 * @category   CLS
 * @package    NucleusCore
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

class CLS_NucleusCore_Helper_Url extends Mage_Core_Helper_Abstract
{
    /**
     * Get URL to Nucleus site
     *
     * @return string
     */
    public function getNucleusUrl()
    {
        return 'http://www.nucleuscommerce.com';
    }

    /**
     * Get URL to the Magento User Guide
     *
     * @return string
     */
    public function getMagentoGuideUrl()
    {
        return 'http://www.magentocommerce.com/resources/user-guide-download';
    }

    /**
     * Get URL to the Nucleus User Guide0
     *
     * @return string
     */
    public function getNucleusGuideUrl()
    {
        return 'http://www.nucleuscommerce.com/userguide';
    }

    /**
     * Get URL to user login on magentocommerce.com
     *
     * @return string
     */
    public function getMagentoLoginUrl()
    {
        return 'https://www.magentocommerce.com/products/customer/account/login/';
    }
}