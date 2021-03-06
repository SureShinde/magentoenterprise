<?php

/**
 * Product:       Xtento_ProductExport (1.7.6)
 * ID:            dIaxXi5+TGYgezAgBRd+u1PmprrlxVXXQYb4E6yGO2Y=
 * Packaged:      2016-04-05T21:25:32+00:00
 * Last Modified: 2015-07-08T14:36:36+02:00
 * File:          app/code/local/Xtento/ProductExport/controllers/Adminhtml/Productexport/IndexController.php
 * Copyright:     Copyright (c) 2016 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Adminhtml_ProductExport_IndexController extends Xtento_ProductExport_Controller_Abstract
{
    public function redirectAction()
    {
        $redirectController = Mage::getStoreConfig('productexport/general/default_page');
        if (!$redirectController) {
            $redirectController = 'productexport_manual';
        }
        $this->_redirect('*/'.$redirectController);
    }

    public function installationAction() {
        Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('xtento_productexport')->__('The extension has not been installed properly. The required database tables have not been created yet. Please check out our <a href="http://support.xtento.com/wiki/Troubleshooting:_Database_tables_have_not_been_initialized" target="_blank">wiki</a> for instructions. After following these instructions access the module at Catalog > Product Export again.'));
        $this->loadLayout();
        $this->renderLayout();
    }

    public function disabledAction() {
        Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('xtento_productexport')->__('The extension is currently disabled. Please go to System > XTENTO Extensions > Product Export Configuration to enable it. After that access the module at Catalog > Product Export again.'));
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return true;
    }
}