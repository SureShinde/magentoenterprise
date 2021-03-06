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

class CLS_NucleusCore_Block_Checkout_Success_Policy extends Mage_Core_Block_Abstract
{

    /**
     * Return policy CMS block ID
     *
     * @return bool
     */
    public function getCmsBlockId()
    {
        return (int)Mage::getStoreConfig(CLS_NucleusCore_Helper_Data::CONFIG_PATH_CHECKOUT_POLICY_BLOCK);
    }

    /**
     * Return block output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $output = '';

        $blockId = $this->getCmsBlockId();

        if ($blockId > 0) {
            $output .= $this->getLayout()->createBlock('cms/block')->setBlockId($blockId)->toHtml() ;
        }

        return $output;
    }

}