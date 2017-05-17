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
 * @method setDirection($dir)
 * @method setSwitcherModel($model)
 * @method getSwitcherModel()
 *
 * @category   Nucleus
 * @package    Catalog
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

class Nucleus_Catalog_Block_Changer_Button extends Nucleus_Catalog_Block_Changer_Abstract {
    
    const DIR_NEXT = 'next';
    const DIR_PREV = 'prev';
    
    protected function _construct() {
        parent::_construct();
        
        $this->setTemplate('nucleus/catalog/changer/button.phtml');
    }
    
    public function getCssClassSufix() {
        return $this->getDirection();
    }
    
    public function hasArrow() {
        switch($this->getDirection()) {
            case self::DIR_PREV:
                return $this->_getSwitcher()->hasPrev();
            case self::DIR_NEXT:
                return $this->_getSwitcher()->hasNext();
        }
        return false;
    }
    
    public function getArrowUrl() {
        switch($this->getDirection()) {
            case self::DIR_PREV:
                return $this->_getSwitcher()->getPrevUrl();
            case self::DIR_NEXT:
                return $this->_getSwitcher()->getNextUrl();
        }
        return null;
    }
    
    public function getArrowTitle() {
        switch($this->getDirection()) {
            case self::DIR_PREV:
                return $this->_getSwitcher()->getPrevTitle();
            case self::DIR_NEXT:
                return $this->_getSwitcher()->getNextTitle();
        }
        return null;
    }


    public function getDirection() {
        if (!$this->hasData('direction')) {
            Mage::throwException('Please setDirection() first');
        }
        
        $dir = strtolower((string)$this->getData('direction'));
        
        if ($dir !== self::DIR_PREV && $dir !== self::DIR_NEXT) {
            Mage::throwException("'{$dir}' is an invalid direction");
        }
        
        return $dir;
    }
    
}
