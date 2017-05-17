<?php

/**
 * Product:       Xtento_ProductExport (1.7.6)
 * ID:            dIaxXi5+TGYgezAgBRd+u1PmprrlxVXXQYb4E6yGO2Y=
 * Packaged:      2016-04-05T21:25:32+00:00
 * Last Modified: 2013-07-29T15:37:16+02:00
 * File:          app/code/local/Xtento/ProductExport/Helper/Entity.php
 * Copyright:     Copyright (c) 2016 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Helper_Entity extends Mage_Core_Helper_Abstract
{
    public function getPluralEntityName($entity) {
        if ($entity == Xtento_ProductExport_Model_Export::ENTITY_CATEGORY) {
            return "categories";
        }
        if ($entity == Xtento_ProductExport_Model_Export::ENTITY_PRODUCT) {
            return "products";
        }
        return $entity;
    }
}