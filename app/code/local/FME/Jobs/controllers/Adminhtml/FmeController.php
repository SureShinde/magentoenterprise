<?php

class FME_Jobs_Adminhtml_FmeController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
       $this->loadLayout()
            ->_setActiveMenu('fme_extensions/jobs/jobs')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Jobs Manager'), Mage::helper('adminhtml')->__('Jobs Manager'));
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return true;
    }
}
