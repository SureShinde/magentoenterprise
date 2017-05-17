<?php
/**
 * @category   CLS
 * @package    CustomerAdvanced
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

class CLS_CustomerAdvanced_Model_Observer
{
    /**
     * Check Captcha On User Login Page
     *
     * COPIED FROM Mage_Captcha_Model_Observer (different form ID)
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function captchaCheckUserLogin($observer)
    {
        $formId = 'quick_user_login';
        $captchaModel = Mage::helper('captcha')->getCaptcha('user_login');
        $controller = $observer->getControllerAction();
        $loginParams = $controller->getRequest()->getPost('login');
        $login = isset($loginParams['username']) ? $loginParams['username'] : null;
        if ($captchaModel->isRequired($login)) {
            $captchaParams = $controller->getRequest()->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
            $word = $captchaParams[$formId];
            if (!$captchaModel->isCorrect($word)) {
                Mage::getSingleton('customer/session')->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('customer/session')->setUsername($login);
                $beforeUrl = Mage::getSingleton('customer/session')->getBeforeAuthUrl();
                $url =  $beforeUrl ? $beforeUrl : Mage::helper('customer')->getLoginUrl();
                $controller->getResponse()->setRedirect($url);
            }
        }
        $captchaModel->logAttempt($login);
        return $this;
    }

    /**
     * Check Captcha On Register User Page
     *
     * COPIED FROM Mage_Captcha_Model_Observer (different form ID)
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function captchaCheckUserCreate($observer)
    {
        $formId = 'quick_user_create';
        $captchaModel = Mage::helper('captcha')->getCaptcha('user_create');
        if ($captchaModel->isRequired()) {
            $controller = $observer->getControllerAction();
            $captchaParams = $controller->getRequest()->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
            if (!$captchaModel->isCorrect($captchaParams[$formId])) {
                Mage::getSingleton('customer/session')->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('customer/session')->setCustomerFormData($controller->getRequest()->getPost());
                $controller->getResponse()->setRedirect(Mage::getUrl('customer/account/create'));
            }
        }
        return $this;
    }
}