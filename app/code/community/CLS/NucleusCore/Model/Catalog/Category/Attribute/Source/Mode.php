<?php
/**
 * Nucleus
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Nucleus License
 * that is bundled with this package in the file LICENSE_NUCLEUS.txt.
 * It is also available through the World Wide Web at this URL:
 * http://www.nucleuscommerce.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the World Wide Web, please send an email
 * to support@nucleuscommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Nucleus
 * to newer versions in the future.
 *
 * @category   CLS
 * @package    NucleusCore
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */
class CLS_NucleusCore_Model_Catalog_Category_Attribute_Source_Mode extends Mage_Catalog_Model_Category_Attribute_Source_Mode
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            parent::getAllOptions();

            $this->_options[] = array(
                'value' => CLS_NucleusCore_Helper_Data::CATEGORY_DM_SUBCAT,
                'label' => Mage::helper('cls_nucleuscore')->__('Sub-categories only'),
            );
            $this->_options[] = array(
                'value' => CLS_NucleusCore_Helper_Data::CATEGORY_DM_SUBCAT_PAGE,
                'label' => Mage::helper('cls_nucleuscore')->__('Static block and sub-categories'),
            );
            $this->_options[] = array(
                'value' => CLS_NucleusCore_Helper_Data::CATEGORY_DM_SUBCAT_PRODUCT,
                'label' => Mage::helper('cls_nucleuscore')->__('Sub-categories and products'),
            );
            $this->_options[] = array(
                'value' => CLS_NucleusCore_Helper_Data::CATEGORY_DM_SUBCAT_PAGE_PRODUCT,
                'label' => Mage::helper('cls_nucleuscore')->__('Static block, sub-categories and products'),
            );

            // Allow other processes to modify options
            $optionsWrapper = new Varien_Object(array('options' => $this->_options));
            Mage::dispatchEvent('nucleus_get_category_display_modes', array('options_wrapper'=>$optionsWrapper));
            $this->_options = $optionsWrapper->getOptions();
        }
        return $this->_options;
    }
}
