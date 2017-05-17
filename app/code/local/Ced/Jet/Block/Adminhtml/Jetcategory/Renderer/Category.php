<?php
/**
* CedCommerce
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
*
* @category    Ced
* @package     Ced_Jet
* @author      CedCommerce Core Team <connect@cedcommerce.com>
* @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

  
class Ced_Jet_Block_Adminhtml_Jetcategory_Renderer_Category extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
	{
	 
		public function render(Varien_Object $row)
		{
			$Magento_cat_id =  $row->getData($this->getColumn()->getIndex());
			$category_name = Mage::getModel('catalog/category')->load($Magento_cat_id)->getName();
			return $category_name;
		}
	 
	}
?>