<?php

class TBT_RewardsReferral_Manage_ReferralsController extends Mage_Adminhtml_Controller_Action
{
    const EXPORT_FILE_NAME = 'rewards_referrals';
    
    protected function _isAllowed()
    {
        return true;
    }
    
    protected function _initAction()
    {
        $this->loadLayout()
                ->_setActiveMenu('rewards/referrals')
                ->_addBreadcrumb(Mage::helper('rewardsref')->__('Referrals'), Mage::helper('rewardsref')->__('Referrals'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
                ->_addContent($this->getLayout()->createBlock('rewardsref/manage_referrals'))
                ->renderLayout();

        return $this;
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');

        $model = Mage::getModel('rewardsref/referral')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('stats_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('rewardsref/referrals');



            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('rewardsref/manage_referrals_edit'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rewardsref')->__('No referral'));
            $this->_redirect('*/*/');
        }

        return $this;
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $model = Mage::getModel('rewardsref/referral');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('rewardsref')->__('Points were successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rewardsref')->__('Unable to save'));

        $this->_redirect('*/*/');

        return $this;
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('rewardsref/referral');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('rewardsref')->__('Referral was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }

        $this->_redirect('*/*/');

        return $this;
    }

    public function massDeleteAction()
    {
        $ruleIds = $this->getRequest()->getParam('rewardsref_referral_ids');
        if (!is_array($ruleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rewardsref')->__('Please select referrals'));
        } else {
            try {
                foreach ($ruleIds as $ruleId) {
                    $rule = Mage::getModel('rewardsref/referral')->load($ruleId);
                    $rule->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('rewardsref')->__('%d referral(s) successfully deleted', count($ruleIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');

        return $this;
    }


    /**
     * Export referrals grid to CSV format
     */
    public function exportCsvAction()
    {
        $time = Mage::getModel('core/date')->date('Y-m-d_H:i:s');
        $fileName = self::EXPORT_FILE_NAME . '-' . $time . '.csv';
        $content    = $this->getLayout()->createBlock('rewardsref/manage_referrals_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export referrals grid to XML format
     */
    public function exportXmlAction()
    {
        $time = Mage::getModel('core/date')->date('Y-m-d_H:i:s');
        $fileName = self::EXPORT_FILE_NAME . '-' . $time . '.xml';
        $content    = $this->getLayout()->createBlock('rewardsref/manage_referrals_grid')
            ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);

        return $this;
    }

}