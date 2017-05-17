<?php

/**
 * Product:       Xtento_ProductExport (1.7.6)
 * ID:            dIaxXi5+TGYgezAgBRd+u1PmprrlxVXXQYb4E6yGO2Y=
 * Packaged:      2016-04-05T21:25:32+00:00
 * Last Modified: 2013-03-29T17:49:29+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export/Entity/Collection/Item.php
 * Copyright:     Copyright (c) 2016 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Export_Entity_Collection_Item extends Varien_Object
{
    private $_collectionItem;

    public function __construct($collectionItem, $entityType, $currItemNo, $collectionCount)
    {
        $this->_collectionItem = $collectionItem;
        $this->_collectionSize = $collectionCount;
        $this->_currItemNo = $currItemNo;
        if ($entityType == Xtento_ProductExport_Model_Export::ENTITY_PRODUCT) {
            $this->setProduct($collectionItem);
        }
        if ($entityType == Xtento_ProductExport_Model_Export::ENTITY_CATEGORY) {
            $this->setCategory($collectionItem);
        }
    }

    public function getObject() {
        return $this->_collectionItem;
    }
}