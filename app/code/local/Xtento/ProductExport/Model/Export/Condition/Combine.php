<?php

/**
 * Product:       Xtento_ProductExport (1.7.6)
 * ID:            dIaxXi5+TGYgezAgBRd+u1PmprrlxVXXQYb4E6yGO2Y=
 * Packaged:      2016-04-05T21:25:32+00:00
 * Last Modified: 2013-07-30T23:49:17+02:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export/Condition/Combine.php
 * Copyright:     Copyright (c) 2016 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Export_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('xtento_productexport/export_condition_combine');
    }

    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value' => 'xtento_productexport/export_condition_product_found', 'label' => Mage::helper('salesrule')->__('Product / Item attribute combination')),
            #array('value' => 'xtento_productexport/export_condition_product_subselect', 'label' => Mage::helper('salesrule')->__('Products subselection')),
            #array('value' => 'salesrule/rule_condition_combine', 'label' => Mage::helper('salesrule')->__('Conditions combination')),
        ));

        $additional = new Varien_Object();
        Mage::dispatchEvent('xtento_productexport_rule_condition_combine', array('additional' => $additional));
        if ($additionalConditions = $additional->getConditions()) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }
}
