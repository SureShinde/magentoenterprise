<?php
/**
 * @category   CLS
 * @package    Core
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

class CLS_Core_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Determines if we are running Magento Enterprise
     *
     * @return bool
     */
    public function isEnterpriseEdition() {
        return (Mage::getEdition() == Mage::EDITION_ENTERPRISE);
    }
}
