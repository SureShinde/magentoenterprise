<?php

/**
 * Banner admin controller
 *
 */
class SmartOsc_CategoryCarousel_Adminhtml_Categorycarousel_BannerController extends SmartOsc_CategoryCarousel_Controller_Adminhtml_CategoryCarousel
{
    /**
     * init the banner
     *
     * @access protected
     * @return SmartOsc_CategoryCarousel_Model_Banner
     */
    protected function _initBanner()
    {
        $bannerId  = (int) $this->getRequest()->getParam('id');
        $banner    = Mage::getModel('smartosc_categorycarousel/banner');
        if ($bannerId) {
            $banner->load($bannerId);
        }
        Mage::register('current_banner', $banner);
        return $banner;
    }

    /**
     * default action
     *
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('smartosc_categorycarousel')->__('Category Carousel'))
             ->_title(Mage::helper('smartosc_categorycarousel')->__('Category'));
        $this->renderLayout();
    }

    /**
     * grid action
     *
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * edit banner - action
     */
    public function editAction()
    {
        $bannerId    = $this->getRequest()->getParam('id');
        $banner      = $this->_initBanner();
        if ($bannerId && !$banner->getId()) {
            $this->_getSession()->addError(
                Mage::helper('smartosc_categorycarousel')->__('This banner no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getBannerData(true);
        if (!empty($data)) {
            $banner->setData($data);
        }
        Mage::register('banner_data', $banner);
        $this->loadLayout();
        $this->_title(Mage::helper('smartosc_categorycarousel')->__('Category Carousel'))
             ->_title(Mage::helper('smartosc_categorycarousel')->__('Category'));
        if ($banner->getId()) {
            $this->_title($banner->getTitle());
        } else {
            $this->_title(Mage::helper('smartosc_categorycarousel')->__('Add banner'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new banner action
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save banner - action
     *
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('banner')) {
            try {
                $banner = $this->_initBanner();
                if ($this->getRequest()->getParam('id') <= 0)
                {
                    // if add new
                    $data['ordering'] = $this->getNextorder();
                }
                $banner->addData($data);
                $imageName = $this->_uploadAndGetName(
                    'image',
                    Mage::helper('smartosc_categorycarousel/banner_image')->getImageBaseDir(),
                    $data
                );
                $banner->setData('image', $imageName);
                $banner->save();
                //Mage::helper('smartosc_categorycarousel')->reorder();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('smartosc_categorycarousel')->__('Category was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $banner->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                if (isset($data['image']['value'])) {
                    $data['image'] = $data['image']['value'];
                }
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setBannerData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {

                Mage::logException($e);
                if (isset($data['image']['value'])) {
                    $data['image'] = $data['image']['value'];
                }
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('smartosc_categorycarousel')->__('There was a problem saving the banner.')
                );
                Mage::getSingleton('adminhtml/session')->setBannerData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('smartosc_categorycarousel')->__('Unable to find banner to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete banner - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $banner = Mage::getModel('smartosc_categorycarousel/banner');
                $banner->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('smartosc_categorycarousel')->__('Category was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('smartosc_categorycarousel')->__('There was an error deleting banner.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('smartosc_categorycarousel')->__('Could not find banner to delete.')
        );
        $this->_redirect('*/*/');
    }
    protected function getNextorder($where)
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $menutable = $resource->getTableName('smartosc_categorycarousel_banner');
        $sqlquery = 'SELECT MAX(ordering) From ' . $menutable;
        $rows = $read->fetchAll($sqlquery);
        if ($rows) {
            return $rows[0]['MAX(ordering)'] + 1;
        }

    }
    /**
     * mass delete banner - action
     */
    public function massDeleteAction()
    {
        $bannerIds = $this->getRequest()->getParam('banner');
        if (!is_array($bannerIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('smartosc_categorycarousel')->__('Please select banners to delete.')
            );
        } else {
            try {
                foreach ($bannerIds as $bannerId) {
                    $banner = Mage::getModel('smartosc_categorycarousel/banner');
                    $banner->setId($bannerId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('smartosc_categorycarousel')->__('Total of %d banners were successfully deleted.', count($bannerIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('smartosc_categorycarousel')->__('There was an error deleting banners.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass status change - action
     *
     */
    public function massStatusAction()
    {
        $bannerIds = $this->getRequest()->getParam('banner');
        if (!is_array($bannerIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('smartosc_categorycarousel')->__('Please select banners.')
            );
        } else {
            try {
                foreach ($bannerIds as $bannerId) {
                $banner = Mage::getSingleton('smartosc_categorycarousel/banner')->load($bannerId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d banners were successfully updated.', count($bannerIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('smartosc_categorycarousel')->__('There was an error updating banners.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    
    /**
     * Check if admin has permissions to visit related pages
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('smartosc_categorycarousel/banner');
    }
}
