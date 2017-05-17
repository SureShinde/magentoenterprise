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

class Ced_Jet_Block_Adminhtml_Return_Renderer_Labelaction extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	
	 
	public function render(Varien_Object $row)
	{
		$url='';
		$label="Edit";
		if($row->getData('status')=='completed'){
				$label="View";
		}
		$url=$this->getUrl('*/*/edit',array('id'=>$row->getId()));
		$html = "<a href='".$url."'>".$label."</a>";
		return $html;
	 
	}	
 		
}
?>