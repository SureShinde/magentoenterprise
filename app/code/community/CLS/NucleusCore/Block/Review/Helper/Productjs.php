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

class CLS_NucleusCore_Block_Review_Helper_Productjs extends Mage_Review_Block_Helper
{
    /**
     * Find the product
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        if ($product = Mage::registry('product')) {
            $this->setProduct($product);
        }
    }

    protected function _toHtml()
    {
        // Provide event that allows for setting of disable_output if this block should be turned off
        Mage::dispatchEvent('cls_nucleus_review_js_output', array(
            'block' => $this,
        ));

        if ($this->getDisableOutput()) {
            return '';
        }
        return parent::_toHtml();
    }
}
