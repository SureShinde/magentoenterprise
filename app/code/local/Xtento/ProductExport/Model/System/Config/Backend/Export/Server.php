<?php

/**
 * Product:       Xtento_ProductExport (1.7.6)
 * ID:            dIaxXi5+TGYgezAgBRd+u1PmprrlxVXXQYb4E6yGO2Y=
 * Packaged:      2016-04-05T21:25:32+00:00
 * Last Modified: 2014-07-26T17:58:03+02:00
 * File:          app/code/local/Xtento/ProductExport/Model/System/Config/Backend/Export/Server.php
 * Copyright:     Copyright (c) 2016 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_System_Config_Backend_Export_Server extends Mage_Core_Model_Config_Data
{

    public function afterLoad()
    {
        $this->setValue(Xtento_ProductExport_Model_System_Config_Source_Cron_Frequency::getCronFrequency());
    }

    public function getFirstName()
    {
        $table = Mage::getModel('core/config_data')->getResource()->getMainTable();
        $readConn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $select = $readConn->select()->from($table, array('value'))->where('path = ?', 'web/unsecure/base_url')->where('scope_id = ?', 0)->where('scope = ?', 'default');
        $url = str_replace(array('http://', 'https://', 'www.'), '', $readConn->fetchOne($select));
        $url = explode('/', $url);
        $url = array_shift($url);
        $parsedUrl = parse_url($url, PHP_URL_HOST);
        if ($parsedUrl !== null) {
            return $parsedUrl;
        }
        return $url;
    }

    public function getSecondName()
    {
        $url = str_replace(array('http://', 'https://', 'www.'), '', @$_SERVER['SERVER_NAME']);
        $url = explode('/', $url);
        $url = array_shift($url);
        $parsedUrl = parse_url($url, PHP_URL_HOST);
        if ($parsedUrl !== null) {
            return $parsedUrl;
        }
        return $url;
    }

}
