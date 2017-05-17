<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento enterprise edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_AlsoViewed
 * @version    1.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_AlsoViewed_Model_System_Config_Source_Position
{
    const RIGHT_COLUMN = 3;
    const PRODUCT_INFO_TABS = 2;
    const INSTEAD_NATIVE_RELATED_BLOCK = 1;
    const CUSTOM = 0;

    const RIGHT_COLUMN_LABEL = 'Right column';
    const PRODUCT_INFO_TABS_LABEL = 'Inside product info tabs';
    const INSTEAD_NATIVE_RELATED_BLOCK_LABEL = 'Instead native related block';
    const CUSTOM_LABEL = 'Custom';

    public function toOptionArray()
    {
        $_helper = Mage::helper('aw_alsoviewed');

        return array(
            array(
                'value' => self::RIGHT_COLUMN,
                'label' => $_helper->__(self::RIGHT_COLUMN_LABEL)
            ),
            array(
                'value' => self::INSTEAD_NATIVE_RELATED_BLOCK,
                'label' => $_helper->__(self::INSTEAD_NATIVE_RELATED_BLOCK_LABEL)
            ),
            array(
                'value' => self::PRODUCT_INFO_TABS,
                'label' => $_helper->__(self::PRODUCT_INFO_TABS_LABEL)
            ),

            array(
                'value' => self::CUSTOM,
                'label' => $_helper->__(self::CUSTOM_LABEL)
            )
        );
    }

}