<?php
/**
 * Crius
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt
 *
 * @category   Crius
 * @package    Crius_Rac
 * @copyright  Copyright (c) 2011 Crius (http://www.criuscommerce.com)
 * @license    http://www.criuscommerce.com/CRIUS-LICENSE.txt
 */
/**
 * Controller for AJAX customer registration
 */
class Crius_Rac_AjaxController extends Mage_Core_Controller_Front_Action
{
    /**
     * @return Crius_Rac_Model_Register
     */
    public function getRegistrationModel()
    {
        return Mage::getSingleton('rac/register');
    }
    
    /**
     * Register customer account from last order
     */
    public function registerAction()
    {
        $data = $this->getRequest()->getPost();
        if ($this->getRequest()->isPost() && isset($data['password']) && isset($data['confirmation'])) {
            // Call registration model and return result array as JSON
            $result = $this->getRegistrationModel()->register($data);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }
}
