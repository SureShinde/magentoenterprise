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

class Ced_Jet_Block_Adminhtml_Prod_Renderer_Showerror extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action { 
	public function render(Varien_Object $row) {
					$id=$row->getId();
					
					$view_url = $this->getUrl('adminhtml/adminhtml_jetrequest/productDetails',array('id'=>$id));
					$editurl = $this->getUrl('adminhtml/catalog_product/edit',array('id'=>$id));
					$html='<a href="'.$view_url.'">View</a>';
					$html= $html.' |&nbsp;&nbsp;<a href="'.$editurl.'">Edit</a>';
					
					$batch_id=Mage::helper('jet')->getBatchIdFromProductId($id);
					$error="";
					$date="";
					$date1="";
					if($batch_id){
						$batchmod="";
						$batchmod=Mage::getModel('jet/batcherror')->load($batch_id);
						$error=$batchmod->getData('error');
						$date=$batchmod->getData('date_added');
						$date1=Mage::app()->getLocale()->date($date,
				              Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
				              null, false
				           );
					}
					if(strlen($error)>0){
								$errorhtml="";
								$url=$this->getUrl('adminhtml/adminhtml_jetajax/errordetails',array('id'=>$id));
								//$errorhtml='<a title="" onclick="showerror'.$id.'('."'".$url."'".')" href="#">Last Upload Error: '.$date1.'</a>';
								$errorhtml='<a title="" onclick="showerror'.$id.'('."'".$url."'".')" href="#">Log</a>';
								$newhtml='<script type="text/javascript">function showerror'.$id.'(sUrl) {
										    oPopup = new Window({
																							id:"popup_window",
																							className: "magento",
																							windowClassName: "popup-window",
																							title: "Last Occurred Error Details",
																							url: sUrl,
																							width: 750,
																							height: 200,
																							minimizable: false,
																							maximizable: false,
																							showEffectOptions: {
																								duration: 0.4
																							},
																							hideEffectOptions:{
																								duration: 0.4
																							},
																							destroyOnClose: true
																						});
																						oPopup.setZIndex(100);
																						oPopup.showCenter(true);
										}
								</script>';
								$errorhtml=$errorhtml.$newhtml;
								$html=$html." | ".$errorhtml;
						}
						return $html;
	 
	 	 }
}