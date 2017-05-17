<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 * @category    Ced
 * @package     Ced_Jet
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ced_Jet_Model_Source_Selprice
{
    public function toOptionArray()
    {


        $_options = array(
            array(
                'label' => 'Default Magento Price',
                'value' => 'final_price'
            ),
            array(
                'label' =>'Increase By Fixed Price',
                'value' =>'plus_fixed'
            ),
            array(
                'label' => 'Increase By Fixed Percentage',
                'value' =>'plus_per'
            ),
            array(
                'label' => 'Decrease By Fixed Price',
                'value' =>'min_fixed'
            ),
            array(
                'label' => 'Decrease By Fixed Percentage',
                'value' =>'min_per'
            ),
            array(
                'label' => 'set individually for each product',
                'value' =>'differ'
            ),
        );
        return $_options;
    }

}