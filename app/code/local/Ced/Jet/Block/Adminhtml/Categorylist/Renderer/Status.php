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

class Ced_Jet_Block_Adminhtml_Categorylist_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	
	 
	public function render(Varien_Object $row)
	{
		$html='Not Created';
		$csv_cat_id="";
		$csv_cat_id=$row->getData('csv_cat_id');
		$csv_cat_id=trim($csv_cat_id);
		if($csv_cat_id !=""){
				$collection="";
				$magento_id=0;
				$collection=Mage::getModel('jet/jetcategory')->getCollection()->addFieldToFilter('jet_cate_id',$csv_cat_id);
				if(count($collection)>0){
					foreach($collection as $coll){
						$magento_id=$coll->getMagentoCatId();
						break;
					}
				}
				if($magento_id!=0){
					$html='Created';
				}else{
					$html='Not Created';
				}
				
		}
		
		return $html;
	 
	}	
 		
}
?>