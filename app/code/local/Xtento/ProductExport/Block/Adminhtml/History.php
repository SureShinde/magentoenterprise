<?php

/**
 * Product:       Xtento_ProductExport (1.7.6)
 * ID:            dIaxXi5+TGYgezAgBRd+u1PmprrlxVXXQYb4E6yGO2Y=
 * Packaged:      2016-04-05T21:25:32+00:00
 * Last Modified: 2013-02-10T18:07:18+01:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/History.php
 * Copyright:     Copyright (c) 2016 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_History extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'xtento_productexport';
        $this->_controller = 'adminhtml_history';
        $this->_headerText = Mage::helper('xtento_productexport')->__('Product Export - Export History');
        parent::__construct();
        $this->_removeButton('add');
    }

    protected function _toHtml()
    {
        return $this->getLayout()->createBlock('xtento_productexport/adminhtml_widget_menu')->toHtml() . parent::_toHtml();
    }
}