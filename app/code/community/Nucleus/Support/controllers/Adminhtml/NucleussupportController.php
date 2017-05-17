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
 * @category   Nucleus
 * @package    Support
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

class Nucleus_Support_Adminhtml_NucleussupportController extends Mage_Adminhtml_Controller_Action
{    
    /**
     * Send nucleus support ticket
     *
     */
    public function indexAction()
    {
        $post = $this->getRequest()->getPost();
        if ( $post ) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            try {
                $postObject = new Varien_Object();
                $postObject->setData($post);
                 
                $user = Mage::getSingleton('admin/session')->getUser();
               
                $mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
               
                $mailTemplate->setDesignConfig(array('area' => 'backend'))
                    ->sendTransactional(
                        'nucleus_support_email_email_template',
                        array('email' => $user->getEmail(), 'name' => $user->getName()),
                        Mage::getStoreConfig('nucleus_support/email/recipient_email'),
                        null,
                        array(
                            'data' => $postObject,
                            'user' => $user)
                    );

                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }

                $translate->setTranslateInline(true);

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Your inquiry was submitted and will be responded to as soon as possible. Thanks for your request!'));
                $this->_redirectReferer();

                return;
            } catch (Exception $e) {
                 
                $translate->setTranslateInline(true);

                Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to submit your request. Please, try again later'));
                $this->_redirectReferer();
                return;
            }

        } else {
            $this->_redirectReferer();
        }
    }
}
