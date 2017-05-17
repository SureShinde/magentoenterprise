<?php
/**
 * Magmodules.eu - http://www.magmodules.eu - info@magmodules.eu
 * =============================================================
 * NOTICE OF LICENSE [Single domain license]
 * This source file is subject to the EULA that is
 * available through the world-wide-web at:
 * http://www.magmodules.eu/license-agreement/
 * =============================================================
 * @category    Magmodules
 * @package     Magmodules_Snippets
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2016 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */
 
class Magmodules_Snippets_Block_Default_Breadcrumbs extends Mage_Catalog_Block_Breadcrumbs {
	
    protected function _prepareLayout() 
    {
		if($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
			$enabled = $this->getSnippetsEnabled();
			$breadcrumbs = $this->getBreadcrumbsEnabled();			
			if($enabled && $breadcrumbs) {
				$home_title = Mage::helper('snippets')->getFirstBreadcrumbTitle(Mage::helper('catalog')->__('Home'));
				$breadcrumbsBlock->addCrumb('home', array(
					'label' => $home_title,
					'title' => Mage::helper('catalog')->__('Go to Home Page'),
					'link' => Mage::getBaseUrl()
				));
				$title = array();
				$path  = Mage::helper('catalog')->getBreadcrumbPath();
				foreach ($path as $name => $breadcrumb) {
					$breadcrumbsBlock->addCrumb($name, $breadcrumb);
					$title[] = $breadcrumb['label'];
				}
				if ($headBlock = $this->getLayout()->getBlock('head')) {
					$headBlock->setTitle(join($this->getTitleSeparator(), array_reverse($title)));
				} 
			} else {
		        return parent::_prepareLayout();
			}	
		}         
    }
       
    public function getSnippetsEnabled() 
    {
        return Mage::getStoreConfig('snippets/general/enabled');
    }	

    public function getBreadcrumbsEnabled() 
    {
        $enabled = Mage::getStoreConfig('snippets/system/breadcrumbs');
        $markup = Mage::getStoreConfig('snippets/system/breadcrumbs_markup');		
		if($enabled && ($markup == 'json')) {
			return true;
		}
    }	
        
}