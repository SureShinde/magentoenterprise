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

class CLS_NucleusCore_Adminhtml_Nucleus_ElementsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Install Nucleus Elements content
     */
    public function installAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        try {
            $installer = Mage::getSingleton('cls_nucleuscore/elements_installer');
            $installer->doFullInstall();

            $messages = $installer->getMessages(true);
            foreach ($messages as $message) {
                $session->addSuccess($message);
            }

            $session->addSuccess(Mage::helper('cls_nucleuscore')->__('Install complete.'));
        } catch (Exception $e) {
            $session->addError($e->getMessage());
        }

        $this->_redirect('adminhtml/system_config/edit', array('section' => 'nucleus_elements'));
    }

    /**
     * Uninstall Nucleus Elements content
     */
    public function uninstallAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        try {
            $installer = Mage::getSingleton('cls_nucleuscore/elements_installer');
            $installer->doFullDelete();

            $messages = $installer->getMessages(true);
            foreach ($messages as $message) {
                $session->addSuccess($message);
            }
            $session->addSuccess(Mage::helper('cls_nucleuscore')->__('Uninstall complete.'));
        } catch (Exception $e) {
            $session->addError($e->getMessage());
        }

        $this->_redirect('adminhtml/system_config/edit', array('section' => 'nucleus_elements'));
    }
}
