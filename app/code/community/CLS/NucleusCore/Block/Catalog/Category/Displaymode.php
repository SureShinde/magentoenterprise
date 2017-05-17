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
class CLS_NucleusCore_Block_Catalog_Category_Displaymode extends Mage_Core_Block_Template
{
    /**
     * Retrieve current category model object
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCurrentCategory()
    {
        if (!$this->hasData('current_category')) {
            $this->setData('current_category', Mage::registry('current_category'));
        }
        return $this->getData('current_category');
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        // Don't output if we don't have a valid parent
        if (!($this->getParentBlock() instanceof Mage_Catalog_Block_Category_View)) {
            return '';
        }

        // Don't output if this is an anchor category being filtered, if this display mode doesn't support that
        if ($this->getIgnoreWhenFiltered() && $this->getCurrentCategory()->getIsAnchor()) {
            $state = Mage::getSingleton('catalog/layer')->getState();
            if ($state && $state->getFilters()) {
                return '';
            }
        }

        return parent::_toHtml();
    }
}