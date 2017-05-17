<?php

class TBT_RewardsReferral_Block_Field_Checkout extends TBT_RewardsReferral_Block_Field_Abstract
{
    public function getLabel()
    {
        if ($this->showReferralEmail() && !($this->showReferralCode())) {
            return $this->__('Referral E-mail');
        } elseif (!$this->showReferralEmail() && ($this->showReferralCode())) {
            return $this->__('Referral Code');
        } else {
            return $this->__('Referral E-mail or Code');
        }
    }
    
    public function appendFieldToMagestoreOneStepCheckout($ifConfigPath = null)
    {
        $this->setTemplate('rewardsref/onepage/magestore_onestepcheckout/field.phtml');
        
        Mage::getModel('rewards/helper_layout_action_append')
            ->setParentBlock($this->getParentBlock())
            ->setIfConfig($ifConfigPath)
            ->add($this, 'after')
            ->append();
        
        return $this;
    }
}