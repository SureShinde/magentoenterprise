<?php

/**
 * WDCA - Sweet Tooth
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS 
 * License, which extends the Open Software License (OSL 3.0).

 * The Open Software License is available at this URL: 
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * DISCLAIMER
 * 
 * By adding to, editing, or in any way modifying this code, WDCA is 
 * not held liable for any inconsistencies or abnormalities in the 
 * behaviour of this code. 
 * By adding to, editing, or in any way modifying this code, the Licensee
 * terminates any agreement of support offered by WDCA, outlined in the 
 * provided Sweet Tooth License. 
 * Upon discovery of modified code in the process of support, the Licensee 
 * is still held accountable for any and all billable time WDCA spent 
 * during the support process.
 * WDCA does not guarantee compatibility with any other framework extension. 
 * WDCA is not responsbile for any inconsistencies or abnormalities in the
 * behaviour of this code if caused by other framework extension.
 * If you did not receive a copy of the license, please send an email to 
 * support@sweettoothrewards.com or call 1.855.699.9322, so we can send you a copy 
 * immediately.
 * 
 * @category   [TBT]
 * @package    [TBT_Rewards]
 * @copyright  Copyright (c) 2015 Sweet Tooth Inc. (http://www.sweettoothrewards.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quick Launch Controller
 *
 * @category   TBT
 * @package    TBT_Rewards
 * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */

class TBT_Rewards_QuickLaunchController extends Mage_Adminhtml_Controller_Action 
{
    protected function _isAllowed()
    {
        return true;
    }
    
    public function indexAction() 
    {
        if (Mage::helper('rewards')->storeHasRewardRules()) {
            $this->_redirect('adminhtml/rewardsDashboard/index');
        }
        
        $helper = Mage::helper('rewards/quickLaunch');
        $data = $this->getRequest()->getParams();

        // Execute step actions
        $helper->executeAction($data);

        // Check for last step
        $step = (isset($data['step'])) ? $data['step'] : null;
        if ($helper->isLastStep($step)) {
            Mage::getConfig()->saveConfig('rewards/general/last_quick_launch', Mage::helper('rewards/datetime')->now(false, true));
            return $this->_redirect('adminhtml/quickLaunch/success');
        }

        $this->loadLayout();

        // Assign step to quick launch block
        $nextStep = $helper->getNextStep($step);
        Mage::app()->getLayout()->getBlock('quickLaunch')->setStep($nextStep);

        $this->renderLayout();
    }
    
    public function resetSettingsAction()
    {
        Mage::getConfig()->saveConfig('rewards/quickLaunch/loyaltyProgramData', null);
        Mage::getConfig()->cleanCache();
        
        $this->_redirect('adminhtml/quickLaunch/index');
    }
    
    public function successAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
