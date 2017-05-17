<?php
/**
 * @category   CLS
 * @package    Core
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

class CLS_Core_Model_Paypal_Config extends Mage_Paypal_Model_Config
{
    /**
     * BN code getter
     *
     * @param string $countryCode ISO 3166-1
     * @return string
     */
    public function getBuildNotationCode($countryCode = null)
    {
        $edition = Mage::helper('cls_core')->isEnterpriseEdition() == true ? 'EE' : 'CE';
        return sprintf('ClassyLlama_SI_Magento%s_PPA', $edition);
    }
}
