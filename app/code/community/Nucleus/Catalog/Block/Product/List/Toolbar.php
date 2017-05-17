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
class Nucleus_Catalog_Block_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar {
    
    protected function _construct() {
        parent::_construct();
        
        $this->addData(array(
            'render_enabled' => true,
            'limit' => 'original',
            'current_order' => 'original',
            'current_direction' => 'original',
            'current_page' => 'original',
            'current_mode' => 'original',
        ));
    }
    
    
    protected function _toHtml() {
        if ($this->getRenderEnabled()) {
            return parent::_toHtml();
        }
        return '';
    }
    
    public function getLimit() {
        $limit = $this->getData('limit');
        if ($limit == 'original') {
            return parent::getLimit();
        }
        return $limit;
    }
    
    public function getCurrentOrder() {
        $order = $this->getData('current_order');
        if ($order == 'original') {
            return parent::getCurrentOrder();
        }
        return $order;
    }
    
    public function getCurrentDirection() {
        $dir = $this->getData('current_direction');
        if ($dir == 'original') {
            return parent::getCurrentDirection();
        }
        return $dir;
    }
    
    public function getCurrentPage() {
        $page = $this->getData('current_page');
        if ($page == 'original') {
            return parent::getCurrentPage();
        }
        return $page;
    }
    
    public function getCurrentMode() {
        $page = $this->getData('current_mode');
        if ($page == 'original') {
            return parent::getCurrentMode();
        }
        return $page;
    }
    
}
