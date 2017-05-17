<?php
/**
 * @category   CLS
 * @package    Core
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

Class CLS_Core_Model_Observer
{
    /**
     * Record that this was the system variable grid page
     *
     * @event controller_action_predispatch_adminhtml_system_index
     */
    public function systemVariablePredispatch() {
        if (!Mage::registry('cls_core_system_variable')) {
            Mage::register('cls_core_system_variable', 1);
        }
    }

    /**
     * @param $observer
     * @event core_collection_abstract_load_before
     */
    public function removeUserInvisibleFromCollection($observer) {
        if (Mage::registry('cls_core_system_variable') && $observer->getCollection() instanceof Mage_Core_Model_Resource_Variable_Collection) {
            $observer->getCollection()->addFieldToFilter("user_visible", true);
        }
    }
}