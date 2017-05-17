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
  
class Ced_Jet_Adminhtml_JetrefundsettlementController extends Mage_Adminhtml_Controller_Action{
	
	
	public function  updaterefundAction(){
		Mage::getModel('jet/observer')->updaterefund();
		$this->_redirect('*/*/refund');
	}

	public function exportRefundCsvAction()
    {
        $fileName   = 'jetrefundorders.csv';
        $grid       = $this->getLayout()->createBlock('jet/adminhtml_refund_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

	public function saveAction(){

            $merchantid=$this->getRequest()->getPost('refund_merchantid');
    		$refund_mer_orderid=$this->getRequest()->getPost('refund_orderid');
            $skudetails=$this->getRequest()->getParam('sku_details');
            try{
                $stat=Mage::getSingleton('core/session')->getData($refund_mer_orderid);

				Mage::getSingleton('core/session')->unsetData($refund_mer_orderid);
            }catch(Exception $e){}
            if($stat){
                Mage::getSingleton('adminhtml/session')->addError('Refund already generated or under process by jet.');
                $this->_redirect('*/*/new');
                return;
            }

            $helper=Mage::helper('jet');
			if($this->getRequest()->getParam('refund_orderid')==""){
						Mage::getSingleton('adminhtml/session')->addError('Please enter Refund Order Id.');
						$this->_redirect('*/*/new');
						return;
			}
			/*if($this->getRequest()->getParam('refund_merchantid')==""){
					Mage::getSingleton('adminhtml/session')->addError('Please enter Refund Merchant Id.');
					$this->_redirect('new');
					return;
			}*/
			if(!$this->getRequest()->getParam('sku_details')){
					Mage::getSingleton('adminhtml/session')->addError('Please select any Item of Order to refund.');
					$this->_redirect('*/*/new');
					return;
			}
			$orderid="";
			$merchantid="";
			$sku_details=array();
			$items=array();
			$orderid=$this->getRequest()->getPost('refund_orderid');
			$orderid=trim($orderid);
			$magento_order_id=0;
	   		$magento_order_id=$helper->getMagentoIncrementOrderId($orderid);

   		 	if($magento_order_id!=NULL){

                if (is_numeric($magento_order_id) &&  $magento_order_id == 0) {
                    Mage::getSingleton('adminhtml/session')->addError('Incomplete information of Order in Refund.');
					$this->_redirect('*/*/new');
                    return;  
                }

                if(is_numeric($magento_order_id)==false  &&  $magento_order_id == '') {
                    Mage::getSingleton('adminhtml/session')->addError('Incomplete information of Order in Refund.');
					$this->_redirect('*/*/new');
                    return;  
                }
            }else{
                Mage::getSingleton('adminhtml/session')->addError('Incomplete information of Order in Refund.');
					$this->_redirect('*/*/new');
                return;      

            }      


	   		//if($magento_order_id==0){
	   					//Mage::getSingleton('adminhtml/session')->addError('Incomplete information of Order in Refund.');
						// $this->_redirect('*/*/new');
						// return;
	   		//}

	   		$order ="";
			$order = Mage::getModel('sales/order')->loadByIncrementId($magento_order_id);
			if (!$order->getId()) {
						    Mage::getSingleton('adminhtml/session')->addError("Order not Exists for this refund.");
							$this->_redirect('*/*/new');
							return;
						                
			}
			if(count($skudetails)<=0){
					Mage::getSingleton('adminhtml/session')->addError('Please select any Item of Order to refund.');
					$this->_redirect('*/*/new');
					return;
			}
			$i=0;

			foreach($skudetails as $detail){
                        $refundQtyData=Mage::helper('jet/jet')->getUpdatedRefundQty($refund_mer_orderid);
                        if(($refundQtyData[$detail['merchant_sku']] + $detail['refund_quantity']) > $detail['qty_requested']){
                            if($refundQtyData[$detail['merchant_sku']] == '' ){
                                Mage::getSingleton('adminhtml/session')->addError("Refund quantity can't be greater than requested quantity for sku : ".$detail['merchant_sku']);
                            }else{
                                Mage::getSingleton('adminhtml/session')->addError("Refund quantity can't be greater than requested quantity for sku : ".$detail['merchant_sku']." . Quantity already processed for refund : ".$refundQtyData[$detail['merchant_sku']]);
                            }
                            $this->_redirect('*/*/new');
                            return;
                        }

                        if(($refundQtyData[$detail['merchant_sku']] + $detail['refund_quantity']) > $detail['qty_requested']){
                            if($refundQtyData[$detail['merchant_sku']] == '' ){
                                Mage::getSingleton('adminhtml/session')->addError("Refund quantity can't be greater than requested quantity for sku : ".$detail['merchant_sku']);
                            }else{
                                Mage::getSingleton('adminhtml/session')->addError("Refund quantity can't be greater than requested quantity for sku : ".$detail['merchant_sku']." . Quantity already processed for refund : ".$refundQtyData[$detail['merchant_sku']]);
                            }
                            $this->_redirect('*/*/new');
                            return;
                        }

                        $returnQtyData=Mage::helper('jet/jet')->getUpdatedReturnQty($refund_mer_orderid);
                        if(($returnQtyData[$detail['merchant_sku']] + $detail['return_quantity']) > $detail['qty_requested'] ){
                            if($returnQtyData[$detail['merchant_sku']] == '' ){
                                Mage::getSingleton('adminhtml/session')->addError("Return quantity can't be greater than requested quantity for sku : ".$detail['merchant_sku']);
                            }else{
                                Mage::getSingleton('adminhtml/session')->addError("Return quantity can't be greater than requested quantity for sku : ".$detail['merchant_sku']." . Quantity already processed for refund : ".$returnQtyData[$detail['merchant_sku']]);
                            }
                            $this->_redirect('*/*/new');
                            return;
                        }

						if($detail['return_quantity']=="" || $detail['return_quantity']<0){
								Mage::getSingleton('adminhtml/session')->addError("Please enter Qty Returned for sku : ".$detail['merchant_sku']);
								$this->_redirect('*/*/new');
								return;
						}
						if($detail['refund_quantity']==""){
								Mage::getSingleton('adminhtml/session')->addError("Please enter Qty Refunded for sku : ".$detail['merchant_sku']);
								$this->_redirect('*/*/new');
								return;
						}

						if($detail['return_principal']<0){
								Mage::getSingleton('adminhtml/session')->addError("Please enter  correct Refund Amount for sku : ".$detail['merchant_sku']);
								$this->_redirect('*/*/new');
								return;
						}
						if($detail['return_shipping_cost']<0){
								Mage::getSingleton('adminhtml/session')->addError("Please enter  correct Refund Shipping Cost for sku : ".$detail['merchant_sku']);
								$this->_redirect('*/*/new');
								return;
						}
						if($detail['return_shipping_tax']<0){
								Mage::getSingleton('adminhtml/session')->addError("Please enter  correct Refund Shipping Tax for sku : ".$detail['merchant_sku']);
								$this->_redirect('*/*/new');
								return;
						}
						if($detail['return_tax']<0){
								Mage::getSingleton('adminhtml/session')->addError("Please enter  correct Refund Tax for sku : ".$detail['merchant_sku']);
								$this->_redirect('*/*/new');
								return;
						}
						if($detail['return_quantity']>$detail['qty_requested']){
								Mage::getSingleton('adminhtml/session')->addError("Please enter Return Qty less than Qty Requested for sku : ".$detail['merchant_sku']);
								$this->_redirect('*/*/new');
								return;
						}
						if($detail['refund_quantity']<=0){
								continue;
						}
						if($detail['refund_quantity'] != $detail['return_quantity']){
								Mage::getSingleton('adminhtml/session')->addError("Refund Qty must be equal to Return Qty for sku : ".$detail['merchant_sku']);
								$this->_redirect('*/*/new');
								return;
						}
						if($detail['return_refundreason']==""){
								Mage::getSingleton('adminhtml/session')->addError("Please enter Refund reason for sku : ".$detail['merchant_sku']);
								$this->_redirect('*/*/new');
								return;
						}
						if($detail['return_refundfeedback']==""){
								Mage::getSingleton('adminhtml/session')->addError("Please enter Refund reason for sku : ".$detail['merchant_sku']);
								$this->_redirect('*/*/new');
								return;
						}

						$check=array();
				   		$check=$helper->getRefundedQtyInfo($order,$detail['merchant_sku']);
				   		if($check['error']=='1'){
				   						$error_msg="";
				   						$error_msg="Error for Order Item with sku : ".$detail['merchant_sku']." ";
				   						$error_msg=$error_msg.$check['error_msg'];
				   						Mage::getSingleton('adminhtml/session')->addError($error_msg);
										$this->_redirect('*/*/new');
										return;
				   		}
				   		$qty_already_refunded=0;
				   		$available_to_refund_qty=0;
				   		$qty_ordered=0;
				   		$qty_already_refunded=$check['qty_already_refunded'];
				   		$available_to_refund_qty=$check['available_to_refund_qty'];
				   		$qty_ordered=$check['qty_ordered'];
				   		if($detail['return_quantity']>$available_to_refund_qty){
	   									Mage::getSingleton('adminhtml/session')->addError("Error to generate return for sku : ".$detail['merchant_sku']." -> Qty Returned is greater than Qty Available for Refund.");
										$this->_redirect('*/*/new');
										return;
	   					}
	   					if($detail['refund_quantity']>$available_to_refund_qty){
	   									Mage::getSingleton('adminhtml/session')->addError("Error to generate return for sku : ".$detail['merchant_sku']." -> Qty Refunded is greater than Qty Available for Refund.");
										$this->_redirect('*/*/new');
										return;
	   					}
								

						$items['items'][$i]['order_item_id']=trim($detail['order_item_id']);
						$items['items'][$i]['total_quantity_returned']=(int)trim($detail['return_quantity']);
						$items['items'][$i]['order_return_refund_qty']=(int)trim($detail['refund_quantity']);
						$items['items'][$i]['refund_reason']=trim($detail['return_refundreason']);
						$items['items'][$i]['refund_feedback']=trim($detail['return_refundfeedback']);
						$items['items'][$i]['refund_amount']['principal']=(float)trim($detail['return_principal']);
						$items['items'][$i]['refund_amount']['shipping_tax']=(float)trim($detail['return_shipping_tax']);
						$items['items'][$i]['refund_amount']['shipping_cost']=(float)trim($detail['return_shipping_cost']);
						$items['items'][$i]['refund_amount']['tax']=(float)trim($detail['return_tax']);
						$i++;
			}
			if($i==0){
					Mage::getSingleton('adminhtml/session')->addError("Some information missing.Refund not generated.");
					$this->_redirect('*/*/new');
					return;
			}
			if(count($items)<=0){
					Mage::getSingleton('adminhtml/session')->addError("Items information missing.Refund not generated.");
					$this->_redirect('*/*/new');
					return;
			}
			$saved_data=array();
			$saved_data=$this->getRequest()->getParams();
			$saved_data=serialize($saved_data);
			$time=time();
			$response = Mage::helper('jet')->CPostRequest('/refunds/'.$orderid.'/'.$time.'',json_encode($items));
			$response=json_decode($response);

			$error_array=array();
			$error_array = $response->errors;
			
			if(!empty($error_array) && count($error_array)>0){ // Now Show error to same panel
				$err_msg = "";
				foreach ($error_array as $valerr){
					$err_msg = $valerr.' | ';
				}
				
				Mage::getSingleton('adminhtml/session')->addError('Invalid Values: '.$err_msg);
				// set form values
   			    //Mage::getSingleton('adminhtml/session')->setFormData($form_array);
   			    $this->_redirect('*/*/refund');
				
			}else{
				$refund_authorisation_id="";
				$refund_authorisation_id=$response->refund_authorization_id;
			
				if(!empty($refund_authorisation_id)){

					$status="";
					$status=$response->refund_status;
					$status=trim($status);
					
					$text = array('refund_orderid'=>$orderid,'refund_merchantid'=>$merchantid,'refund_merchantid'=>$refund_authorisation_id,'refund_status'=>'created');

					$model = Mage::getModel('jet/jetrefund');
					$model->setData('refund_orderid',$orderid);
					$model->setData('refund_merchantid',$merchantid);
					$model->setData('refund_status',$status);
					$model->setData('saved_data',$saved_data);
					$model->setData('refund_id',$refund_authorisation_id);
					$model->save();
					$saved_data=unserialize($saved_data);
					/*if($status=='created'){
							$flag=false;
							$flag=$helper->generateCreditMemoForRefund($saved_data);
					}*/
					Mage::getSingleton('adminhtml/session')->addSuccess('Your Refund has been created successfully');
					$this->_redirect('*/*/refund');
					return;
					
				}else{
					Mage::getSingleton('adminhtml/session')->addError('Value inserted by you is not correct');
					//Mage::getSingleton('adminhtml/session')->setFormData($form_array);
					$this->_redirect('*/*/refund');
					return;
				}
			}
			
			$this->_redirect('*/*/new');
	}
	
	public function refundAction(){
		$this->loadLayout();
		//$this->_setActiveMenu('jet/return');
		$this->renderLayout();
	}
	public function editAction()
      {  
      	$id = $this->getRequest()->getParam('id');
		
      	$refundModel = Mage::getModel('jet/jetrefund')->load($id);
		//print_r($refundModel->getData());die();
           if ($refundModel->getId() || $id == 0)
           {
	            
	            Mage::register('refund_data', $refundModel);
	             $this->loadLayout();
	             $this->_setActiveMenu('jet/set_time');
	             $this->_addBreadcrumb('Refund Manager', 'Refund Manager');
	             $this->_addBreadcrumb('Refund Description', 'Refund Description');
	             $this->getLayout()->getBlock('head')
	                  ->setCanLoadExtJs(true);
					  
	             $this->_addContent($this->getLayout()
	                  ->createBlock('jet/adminhtml_refund_edit'))
	                  ->_addLeft($this->getLayout()
	                  ->createBlock('jet/adminhtml_refund_edit_tabs')
	              );
				  
	             $this->renderLayout();
           }
           else
           {
                 Mage::getSingleton('adminhtml/session')->addError('Refund not created');
                 $this->_redirect('*/*/');
            }
       }
       public function newAction()
       {
          $this->_forward('edit');
       }
       public function getchildhtmlAction(){
       			if($this->getRequest()->getParam('mer_id')){
       						$helper=Mage::helper('jet');
       						$msg['success']="";
							$msg['error']="";
							$magento_order_id="";
       						$merchant_order_id="";
       						$order_data='';
       						$shipment_data='';
       						$merchant_order_id=$this->getRequest()->getParam('mer_id');
       						$merchant_order_id=trim($merchant_order_id);
       						$collection="";
       						try{
       								$collection=Mage::getModel('jet/jetorder')->getCollection();
									$collection->addFieldToFilter( 'merchant_order_id', $merchant_order_id );
									if($collection->count()>0){
											foreach($collection as $coll){
														$magento_order_id=$coll->getData('magento_order_id');
														$order_data=$coll->getData('order_data');
														$shipment_data=$coll->getData('shipment_data');
														break;
											}	
									}

                                    $updated_refundqty_data=Mage::helper('jet/jet')->getUpdatedRefundQty($merchant_order_id);

                                    /*$refundcollection=Mage::getModel('jet/jetrefund')->getCollection()->addFieldToFilter('refund_orderid', $merchant_order_id );
                                    $refund_qty=array();
                                    if($refundcollection->count()>0){
                                        foreach($refundcollection as $coll){
                                            $refund_data = unserialize($coll->getData('saved_data'));
                                            foreach($refund_data['sku_details'] as $sku=>$data){
                                                //echo 'sku='.$sku.'  rfd_qty='.$data['refund_quantity'].'<br/>';
                                            }
                                            //$refund_qty += unserialize($refund_data);
                                        }
                                    }*/

									if($magento_order_id == "" || $order_data == ''){
											$msg['error']="Order not found.Please enter correct Order Id.";echo json_encode($msg);return;
									}
									$order_decoded_data="";
									$items_data=array();
									$order_decoded_data=unserialize($order_data);
									if(is_object($order_decoded_data) && count($order_decoded_data->order_items)>0){
												foreach ($order_decoded_data->order_items as $value) {
													$items_data[]=$value;
												}
									}else{
										$msg['error']="Items Not found in Selected Order.Please enter correct Order Id.";echo json_encode($msg);return;
											
									}
									if(count($items_data)<=0){
											$msg['error']="Items Data not found for selected Order.Please enter correct Order Id.";echo json_encode($msg);return;
									}
									$order ="";
									$order = Mage::getModel('sales/order')->loadByIncrementId($magento_order_id);
									if (!$order->getId()) {
											$msg['error']="Order data not found.Please enter correct Order Id.";echo json_encode($msg);return;
									}
									if($order->getStatus()!='complete'){
											$msg['error']="Can't generate refunds for incompleted orders.This order is incomplete.";echo json_encode($msg);return;
									}
									$return_flag=false;
									//$return_flag=$helper->checkOrderForReturn($merchant_order_id);
									//if($return_flag){
												//$msg['error']="This Order's Return already generated.Can't generate Refund for this.Please enter some other Merchant Order Id.";echo json_encode($msg);return;
									//}
									$error_msg='';
									$j=0;
									foreach($items_data as $item){
												$merchant_sku="";
												$merchant_sku=$item->merchant_sku;
												$check=array();
												$check=$helper->getRefundedQtyInfo($order,$merchant_sku);
												if($check['error']=='1'){
								   						$error_msg=$error_msg."Error for Order Item with sku : ".$merchant_sku."-> ";
								   						$error_msg=$error_msg.$check['error_msg'];
								   						continue;
								   				}
								   				$j++;
									}
									if($j==0){
													$msg['error']=$error_msg;echo json_encode($msg);return;
									}
									$html=$this->getLayout()
										->createBlock('core/template')->setTemplate("ced/jet/refundhtml.phtml")
										->setData('items_data',$items_data)
										->setData('order',$order)
                                        ->setData('refundtotalqty',$updated_refundqty_data)
										->toHtml();
									$msg['success']=$html;
									$this->getResponse()->setBody(
										json_encode($msg)
									);
									return;
       						}catch(Exception $e){
       								$msg['error']=$e->getMessage();echo json_encode($msg);return;
       								
       						}
							
       						
       			}else{
       				$msg['error']="Merchant Order Id not found.Please enter again.";echo json_encode($msg);return;
       				
       			}
       			
       }
	
}
