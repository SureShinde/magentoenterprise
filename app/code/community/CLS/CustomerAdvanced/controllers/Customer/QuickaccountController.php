<?php
/**
 * @category   CLS
 * @package    CustomerAdvanced
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

require_once Mage::getModuleDir('controllers', 'Mage_Customer') . DS . 'AccountController.php';

class CLS_CustomerAdvanced_Customer_QuickaccountController extends Mage_Customer_AccountController
{
    /**
     * Define target URL and redirect customer after logging in
     */
    protected function _loginPostRedirect()
    {
        $session = $this->_getSession();

        if (count($session->getMessages()->getErrors()) > 0) {
            $this->_redirectError(Mage::getUrl('customer/account/login'));
            return $this;
        }

        parent::_loginPostRedirect();
    }

    /**
     * Action predispatch
     */
    public function preDispatch()
    {
        if (($referer = $this->getRequest()->getParam('unencoded_referer'))
            && !empty($referer)) {
            $this->getRequest()->setParam('referer', Mage::helper('core')->urlEncode($referer));
        }
        parent::preDispatch();
    }

    public function postDispatch()
    {
        parent::postDispatch();

        // Add any customer session messages to the core session instead, because we don't know what page we'll end up on
        $customerSession = $this->_getSession();
        $coreSession = Mage::getSingleton('core/session');
        $messages = $customerSession->getMessages(true);
        foreach ($messages->getItems() as $message) {
            $coreSession->addMessage($message);
        }
    }

    /**
     * Get Url method
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    protected function _getUrl($url, $params = array())
    {
        $url = preg_replace('#^\*/\*#', 'customer/account', $url);
        return parent::_getUrl($url, $params);
    }
}