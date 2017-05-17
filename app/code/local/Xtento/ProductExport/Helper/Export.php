<?php

/**
 * Product:       Xtento_ProductExport (1.7.6)
 * ID:            dIaxXi5+TGYgezAgBRd+u1PmprrlxVXXQYb4E6yGO2Y=
 * Packaged:      2016-04-05T21:25:32+00:00
 * Last Modified: 2013-03-24T18:23:58+01:00
 * File:          app/code/local/Xtento/ProductExport/Helper/Export.php
 * Copyright:     Copyright (c) 2016 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Helper_Export extends Mage_Core_Helper_Abstract
{
    public function getExportEntity($entity)
    {
        if ($entity == Xtento_ProductExport_Model_Export::ENTITY_PRODUCT) {
            return 'catalog/product';
        } else if ($entity == Xtento_ProductExport_Model_Export::ENTITY_CATEGORY) {
            return 'catalog/category';
        }
        Mage::throwException(Mage::helper('xtento_productexport')->__('Could not find export entity "%s"', $entity));
    }

    public function getLastEntityId($entity)
    {
        $collection = Mage::getModel($this->getExportEntity($entity))->getCollection()
            ->addAttributeToSelect('entity_id');
        $collection->getSelect()->limit(1)->order('entity_id DESC');
        $object = $collection->getFirstItem();
        return $object->getId();
    }

    public function getExportBkpDir()
    {
        return Mage::getBaseDir('var') . DS . "product_export_bkp" . DS;
    }
}