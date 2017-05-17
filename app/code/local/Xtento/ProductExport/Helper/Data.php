<?php

/**
 * Product:       Xtento_ProductExport (1.7.6)
 * ID:            dIaxXi5+TGYgezAgBRd+u1PmprrlxVXXQYb4E6yGO2Y=
 * Packaged:      2016-04-05T21:25:32+00:00
 * Last Modified: 2013-11-09T15:52:03+01:00
 * File:          app/code/local/Xtento/ProductExport/Helper/Data.php
 * Copyright:     Copyright (c) 2016 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Helper_Data extends Mage_Core_Helper_Abstract
{
    static $_isModuleProperlyInstalled = null;
    const EDITION = 'EE';

    public function getDebugEnabled()
    {
        return Mage::getStoreConfigFlag('productexport/general/debug');
    }

    public function isDebugEnabled()
    {
        return Mage::getStoreConfigFlag('productexport/general/debug') && ($debug_email = Mage::getStoreConfig('productexport/general/debug_email')) && !empty($debug_email);
    }

    public function getDebugEmail()
    {
        return Mage::getStoreConfig('productexport/general/debug_email');
    }

    public function getModuleEnabled()
    {
        if (!Mage::getStoreConfigFlag('productexport/general/enabled')) {
            return 0;
        }
        $moduleEnabled = Mage::getModel('core/config_data')->load('productexport/general/' . str_rot13('frevny'), 'path')->getValue();
        if (empty($moduleEnabled) || !$moduleEnabled || (0x28 !== strlen(trim($moduleEnabled)))) {
            return 0;
        }
        if (!Mage::registry('moduleString')) {
            Mage::register('moduleString', 'false');
        }
        return true;
    }

    public function getMsg()
    {
        return Mage::helper('xtento_productexport')->__(str_rot13('Gur Cebqhpg Rkcbeg Zbqhyr vf abg ranoyrq. Cyrnfr znxr fher lbh\'er hfvat n inyvq yvprafr xrl naq gung gur zbqhyr unf orra ranoyrq ng Flfgrz > KGRAGB Rkgrafvbaf > Fnyrf Rkcbeg pbasvthengvba.'));
    }

    public function isModuleProperlyInstalled()
    {
        // Check if DB table(s) have been created.
        if (self::$_isModuleProperlyInstalled !== null) {
            return self::$_isModuleProperlyInstalled;
        } else {
            self::$_isModuleProperlyInstalled = (Mage::getSingleton('core/resource')->getConnection('core_read')->showTableStatus(Mage::getSingleton('core/resource')->getTableName('xtento_productexport_profile')) !== false);
            return self::$_isModuleProperlyInstalled;
        }
    }
}