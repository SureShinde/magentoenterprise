<?php
/**
 * MageryThemes
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://magery-themes.com/MAGERY-LICENSE.txt
 *
 *
 * MAGENTO EDITION USAGE NOTICE
 *
 * This package designed for Magento COMMUNITY edition
 * MageryThemes does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * MageryThemes does not provide extension support in case of
 * incorrect edition usage.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *
 * @category   Wfs
 * @package    Wfs_DisableEmails
 * @copyright  Copyright (c) 2012 MageryThemes (http://magery-themes.com)
 * @license    http://magery-themes.com/MAGERY-LICENSE.txt
 */
class Wfs_DisableEmails_Model_Email_Template_Mailer extends Mage_Core_Model_Email_Template_Mailer
{
    /**
     * Send all emails from email list
     * @see self::$_emailInfos
     *
     * @return Mage_Core_Model_Email_Template_Mailer
     */
    public function send()
    {
    	if (Mage::helper('wfs_disable_emails')->isDisabled($this->getTemplateId())) {
    		return $this;
    	}

        $emailTemplate = Mage::getModel('core/email_template');
        // Send all emails from corresponding list
        while (!empty($this->_emailInfos)) {
            $emailInfo = array_pop($this->_emailInfos);
            // Handle "Bcc" recepients of the current email
            $emailTemplate->addBcc($emailInfo->getBccEmails());
            // Set required design parameters and delegate email sending to Mage_Core_Model_Email_Template
            $emailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $this->getStoreId()))
                ->sendTransactional(
                    $this->getTemplateId(),
                    $this->getSender(),
                    $emailInfo->getToEmails(),
                    $emailInfo->getToNames(),
                    $this->getTemplateParams(),
                    $this->getStoreId()
                );
        }
        return $this;
    }
}
