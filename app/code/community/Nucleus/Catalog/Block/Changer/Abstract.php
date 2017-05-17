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
 * @category   Nucleus
 * @package    Catalog
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

abstract class Nucleus_Catalog_Block_Changer_Abstract extends Mage_Core_Block_Template {
    
    protected function _toHtml() {
        if (!$this->_getSwitcher()->isEnabled()) {
            return '';
        }
        return parent::_toHtml();
    }

    
    /**
     * @return Nucleus_Catalog_Model_Switcher_Abstract
     */
    protected function _getSwitcher() {
        if ($this->getParentBlock() && $this->getParentBlock()->hasSwitcherModelInstance()) {
            return $this->getParentBlock()->getSwitcherModelInstance();
        }
            
        if (!$this->hasSwitcherModelInstance()) {
            
            if (!$this->hasSwitcherModel()) {
                Mage::throwException('Please setSwitcherModel() first');
            }
            
            $instance = Mage::getModel($this->getSwitcherModel());
            if (!$instance instanceof Nucleus_Catalog_Model_Switcher_Abstract) {
                Mage::throwException("'{$this->getSwitcherModel()}' is not a valid switcher model");
            }
            
            $this->setSwitcherModelInstance($instance);
        }
        return $this->getSwitcherModelInstance();
    }
    
    
    
}
