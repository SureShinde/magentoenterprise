<?php
/**
 * @category   CLS
 * @package    CustomerAdvanced
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

require_once Mage::getModuleDir('controllers', 'Mage_Captcha') . DS . 'RefreshController.php';

class CLS_CustomerAdvanced_Captcha_RefreshController extends Mage_Captcha_RefreshController
{
    /**
     * Refreshes captcha and returns JSON encoded URL to image (AJAX action)
     * Example: {'imgSrc': 'http://example.com/media/captcha/67842gh187612ngf8s.png'}
     *
     * @return null
     */
    public function indexAction()
    {
        $formId = $this->getRequest()->getPost('formId');
        $formId = preg_replace('/'.Mage::helper('cls_customeradvanced/quickaccess')->getHtmlIdPrefix().'_/', '', $formId);
        $this->getRequest()->setPost('formId', $formId);

        return parent::indexAction();
    }
}
