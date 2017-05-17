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
 
class Magmodules_Snippets_Block_Blog_Json extends Mage_Core_Block_Template {
	
    protected function _construct() 
    {
        parent::_construct();	      			
		if($this->getSnippetsEnabled()) {
			$enable = Mage::getStoreConfig('snippets/blog/enable');
			$blog_enable = Mage::getStoreConfig('blog/blog/enabled');
			if($enable && $blog_enable) {
				if($_snippets = $this->getJsonBlogSnippets()) {
					$this->setSnippets($_snippets);
					$this->setTemplate('magmodules/snippets/blog/json.phtml');	    			
				}
			}
		}
    }
    
    public function getJsonBlogSnippets() 
    {
        return $this->helper('snippets')->getJsonBlogSnippets();
    }	

    public function getSnippetsEnabled() 
    {
        return Mage::getStoreConfig('snippets/general/enabled');
    }	
		
}