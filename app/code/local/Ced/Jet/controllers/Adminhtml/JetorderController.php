<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Cedcommerce.com license and  can be accessed
 * through the world-wide-web at this URL:
 * http://www.cedcommerce.com/license-agreement.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to the file if you wish to upgrade this extension to newer
 * version in the future.
 */
class Ced_Jet_Adminhtml_JetorderController extends Mage_Adminhtml_Controller_Action
{
	public function clearallAction(){
	
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$query = "TRUNCATE TABLE ". $resource->getTableName('jet/orderimport') ."";
		$writeConnection->query($query);
		
		Mage::getSingleton('adminhtml/session')->addSuccess('Failed Jet.com Order Log cleared.');
		
		$this->_redirect('*/*/failedorders');
	} 

    public function returnAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('jet/return');
        $this->renderLayout();
    }

    public function newAction()
    {
        Mage::getModel('jet/observer')->jetreturn();
        $this->_redirect('adminhtml/adminhtml_jetorder/return');
    }

    public function fetchAction()
    {
        Mage::getModel('jet/observer')->createOrder();
        $this->_redirect('adminhtml/adminhtml_jetorder/jetorder');
    }

    public function directedcancelAction()
    {
        Mage::getModel('jet/observer')->directCancel();
        $this->_redirect('adminhtml/adminhtml_jetorder/jetorder');
    }

    public function saveAction()
    {
        $helper = Mage::helper('jet');
        $details_saved_after = "";
        $order_id = "";
        $id = "";
        $status = "";
        $returnid = "";
        $agreeto_return = false;
        if ($this->getRequest()->getParam('id') && $this->getRequest()->getParam('returnid')) {
            $returnid = $this->getRequest()->getParam('returnid');
            $id = $this->getRequest()->getParam('id');
            $id = trim($id);
            $data_ship = array();
            $items = array();
            $details_saved_after = $this->getRequest()->getParams();
            $details_saved_after = $helper->prepareDataAfterSubmitReturn($details_saved_after, $id);
            $order_id = $this->getRequest()->getParam('merchant_order_id');
            $order_id = trim($order_id);
            $magento_order_id = 0;
            $magento_order_id = $helper->getMagentoIncrementOrderId($order_id);

            if($magento_order_id!=NULL){

                    if (is_numeric($magento_order_id) &&  $magento_order_id == 0) {
                         Mage::getSingleton('adminhtml/session')->addError('Incomplete information of Order in Return.');
                         $this->_redirect('adminhtml/adminhtml_jetorder/edit', array('id' => $id));
                        return; 
                    }

                    if(is_numeric($magento_order_id)==false  &&  $magento_order_id == '') {
                         Mage::getSingleton('adminhtml/session')->addError('Incomplete information of Order in Return.');
                        $this->_redirect('adminhtml/adminhtml_jetorder/edit', array('id' => $id));
                        return; 
                    }
            }else{
                 Mage::getSingleton('adminhtml/session')->addError('Incomplete information of Order in Return.');
                $this->_redirect('adminhtml/adminhtml_jetorder/edit', array('id' => $id));
                return;

            }   
            /*
            if ($magento_order_id == 0) {
                Mage::getSingleton('adminhtml/session')->addError('Incomplete information of Order in Return.');
                $this->_redirect('adminhtml/adminhtml_jetorder/edit', array('id' => $id));
                return;
            } */

            $order = "";

            $order = Mage::getModel('sales/order')->loadByIncrementId($magento_order_id);
            if (!$order->getId()) {
                Mage::getSingleton('adminhtml/session')->addError("Order not Exists for this return.");
                $this->_redirect('adminhtml/adminhtml_jetorder/edit', array('id' => $id));
                return;

            }
            $refund_flag = false;
            $refund_flag = $helper->checkOrderInRefund($order_id);
            if ($refund_flag) {
                Mage::getSingleton('adminhtml/session')->addError("Order Refund is already exists.Can't generate return.");
                $this->_redirect('adminhtml/adminhtml_jetorder/return');
                return;
            }
            $data_ship['merchant_order_id'] = $order_id;
            $status = $this->getRequest()->getParam('agreeto_return');
            if ($status == 0) {
                $agreeto_return = false;
            } else {
                $agreeto_return = true;
            }
            $data_ship['agree_to_return_charge'] = $agreeto_return;
            $sku_detail = $this->getRequest()->getParam('sku_details');
            if (count($sku_detail) > 0) {
                $i = 0;
                foreach ($sku_detail as $key => $detail) {
                    if ($detail['want_to_return'] == '0') {
                        continue;
                    }
                    if ($detail['changes_made'] == '1') {
                        continue;
                    }
                    if ($detail['return_quantity'] == "") {
                        Mage::getSingleton('adminhtml/session')->addError("Please enter Qty Returned.");
                        $this->_redirect('adminhtml/adminhtml_jetorder/edit', array('id' => $id));
                        return;
                    }
                    if ($detail['refund_quantity'] == "") {
                        Mage::getSingleton('adminhtml/session')->addError("Please enter Qty Refunded.");
                        $this->_redirect('adminhtml/adminhtml_jetorder/edit', array('id' => $id));
                        return;
                    }
                    $detail['return_quantity'] = (int)trim($detail['return_quantity']);
                    $detail['refund_quantity'] = (int)trim($detail['refund_quantity']);
                    if (is_numeric($detail['refund_quantity']) && $detail['refund_quantity'] >= 0 && $detail['refund_quantity'] <= $detail['return_quantity']) {

                    } else {
                        Mage::getSingleton('adminhtml/session')->addError("Please enter correct value to Qty Refunded.");
                        $this->_redirect('adminhtml/adminhtml_jetorder/edit', array('id' => $id));
                        return;
                    }

                    $check = array();
                    $check = $helper->getRefundedQtyInfo($order, $detail['merchant_sku']);
                    if ($check['error'] == '1') {
                        $error_msg = "";
                        $error_msg = "Error for Order Item with sku : " . $detail['merchant_sku'] . " ";
                        $error_msg = $error_msg . $check['error_msg'];
                        Mage::getSingleton('adminhtml/session')->addError($error_msg);
                        $this->_redirect('adminhtml/adminhtml_jetorder/edit', array('id' => $id));
                        return;
                    }
                    $qty_already_refunded = 0;
                    $available_to_refund_qty = 0;
                    $qty_ordered = 0;
                    $qty_already_refunded = $check['qty_already_refunded'];
                    $available_to_refund_qty = $check['available_to_refund_qty'];
                    $qty_ordered = $check['qty_ordered'];
                    if ($detail['refund_quantity'] > $available_to_refund_qty) {
                        Mage::getSingleton('adminhtml/session')->addError("Error to generate return for sku : " . $detail['merchant_sku'] . " -> Qty Refunded is greater than Qty Available for Refund.");
                        $this->_redirect('adminhtml/adminhtml_jetorder/edit', array('id' => $id));
                        return;
                    }
                    if ($detail['refund_quantity'] < 0) {
                        continue;
                    }
                    $arr = array();
                    $arr['order_item_id'] = $detail['order_item_id'];
                    $arr['total_quantity_returned'] = (int)$detail['return_quantity'];
                    $arr['order_return_refund_qty'] = (int)$detail['refund_quantity'];
                    $arr['return_refund_feedback'] = "";
                    if ($detail['return_refundfeedback'] != "") {
                        $arr['return_refund_feedback'] = $detail['return_refundfeedback'];
                    }
                    $return_principal = "";
                    $return_shipping_tax = "";
                    $return_shipping_cost = "";
                    $return_tax = "";
                    $return_principal = (float)trim($detail['return_principal']);
                    $return_shipping_tax = (float)trim($detail['return_shipping_tax']);
                    $return_shipping_cost = (float)trim($detail['return_shipping_cost']);
                    $return_tax = (float)trim($detail['return_tax']);
                    if ($return_principal === "" || $return_principal < 0 || $return_shipping_tax === "" || $return_shipping_tax < 0 || $return_tax === "" || $return_tax < 0 || $return_shipping_cost === "" || $return_shipping_cost < 0) {
                        Mage::getSingleton('adminhtml/session')->addError("Please enter correct values in Amount,Shipping cost,Shipping tax or Tax.");
                        $this->_redirect('adminhtml/adminhtml_jetorder/edit', array('id' => $id));
                        return;
                    }
                    $arr['refund_amount'] = array('principal' => $return_principal, 'tax' => $return_tax, 'shipping_tax' => $return_shipping_tax, 'shipping_cost' => $return_shipping_tax);
                    $data_ship['items'][] = $arr;

                    $i++;
                }
                if ($i == 0) {
                    Mage::getSingleton('adminhtml/session')->addError("No item's 'Want to Send' is selected Yes to send its data to Jet.com.");
                    $this->_redirect('adminhtml/adminhtml_jetorder/edit', array('id' => $id));
                    return;
                }

                $data = Mage::helper('jet')->CPutRequest('/returns/' . $returnid . '/complete', json_encode($data_ship));
                $responsedata = json_decode($data);

                if ($responsedata->errors && count($responsedata->errors) > 0) {
                    $error_data = $responsedata->errors;
                    $str = "";
                    foreach ($error_data as $val) {
                        if ($str == "") {
                            $str = $val;
                        } else {
                            $str = $str . "<br/>" . $val;
                        }
                    }
                    Mage::getSingleton('adminhtml/session')->addError($str);
                    $this->_redirect('adminhtml/adminhtml_jetorder/edit', array('id' => $id));
                    return;
                }
                if (empty($responsedata) || $responsedata == "") {
                    $model = "";
                    $details_saved_after = $helper->saveChangesMadeValue($details_saved_after);
                    $details_saved_after = serialize($details_saved_after);
                    $model = Mage::getModel('jet/jetreturn')->load($id)->setData('details_saved_after', $details_saved_after)->save();
                    //$checkstatus=false;
                    //$checkstatus=Mage::helper('jet')->getStatusOfReturn($id);
                    $model = "";
                    $model = Mage::getModel('jet/jetreturn')->load($id)->setData('status', 'inprogress')->save();
                    $res = "/returns/state/" . $returnid;
                    $returndetails = "";
                    $returndetails = Mage::helper('jet')->CGetRequest(rawurlencode($res));
                    
                    $return = "";
                    if ($returndetails) {
                        $return = json_decode($returndetails);
                        $serialized_details = "";
                        $serialized_details = serialize($return);
                        $return_status = '';
                        $return_status = $return->return_status;
                        if ($return->return_status == "completed by merchant") {
                            $model = "";
                            $model = Mage::getModel('jet/jetreturn')->load($id)->setData('status', 'completed')->save();
                            $flag = false;
                            $flag = Mage::helper('jet')->generateCreditMemoForReturn($id);
                        }
                    }
                    Mage::getSingleton('adminhtml/session')->addSuccess('Your return has been posted to jet successfully.');
                    if ($agreeto_return) {
                        //$flag=false;
                        //$flag=Mage::helper('jet')->generateCreditMemo($id);
                    }
                    $this->_redirect('adminhtml/adminhtml_jetorder/return');
                    return;
                } else {
                    Mage::getSingleton('adminhtml/session')->addSuccess('Return data not submitted.Please try again');
                    $this->_redirect('adminhtml/adminhtml_jetorder/edit', array('id' => $id));
                    return;
                }

            } else {
                Mage::getSingleton('adminhtml/session')->addError("Return Data Missing.Please try again.");
                $this->_redirect('adminhtml/adminhtml_jetorder/edit', array('id' => $id));
                return;
            }


        } else {
            Mage::getSingleton('adminhtml/session')->addError("Return Id not found.");
            $this->_redirect('adminhtml/adminhtml_jetorder/return');
            return;
        }
        /*$this->loadLayout();
        $this->_setActiveMenu('jet/return');
        $this->renderLayout();*/
    }

    public function editAction()
    {

        $helper = Mage::helper('jet');
        $id = $this->getRequest()->getParam('id');

        $returnModel = Mage::getModel('jet/jetreturn')->load($id);
        $refund_flag = false;

        if ($returnModel->getId() || $id == 0) { 
            $return_data = array();
            $resulting_data = array();

            if ($returnModel->getData('details_saved_after') != "") {
                $details_saved_after = "";
                $details_saved_after = $returnModel->getData('details_saved_after');
                $return_data = "";
                $return_data = unserialize($details_saved_after);
                $magento_order_id = 0;
                
                $magento_order_id = $helper->getMagentoIncrementOrderId($return_data['merchant_order_id']);
                
                if($magento_order_id!=NULL){

                    if (is_numeric($magento_order_id) &&  $magento_order_id == 0) {
                        Mage::getSingleton('adminhtml/session')->addError('Incomplete information of Order in Return.');
                        $this->_redirect('*/*/return');
                        return;  
                    }

                    if(is_numeric($magento_order_id)==false  &&  $magento_order_id == '') {
                        Mage::getSingleton('adminhtml/session')->addError('Incomplete information of Order in Return.');
                        $this->_redirect('*/*/return');
                        return;  
                    }
                }else{
                    Mage::getSingleton('adminhtml/session')->addError('Incomplete information of Order in Return.');
                    $this->_redirect('*/*/return');
                    return;      

                }      

                //$magento_order_id = $helper->getMagentoIncrementOrderId($return_data['merchant_order_id']);
                //if ($magento_order_id == 0) {
                    //Mage::getSingleton('adminhtml/session')->addError('Incomplete information of Order in Return.');
                    //$this->_redirect('*/*/return');
                    //return;
                //}

                $order = "";
                $order = Mage::getModel('sales/order')->loadByIncrementId($magento_order_id);
                if (!$order->getId()) {
                    Mage::getSingleton('adminhtml/session')->addError("Order not Exists for this return.");
                    $this->_redirect('*/*/return');
                    return;

                }
                $resulting_data = $return_data;
                $resulting_data['status'] = $returnModel->getData('status');
                $view_case_for_return = false;
                $view_case_for_return = $helper->checkViewCaseForReturn($return_data);
                if (!$view_case_for_return) {
                    $skus = "";
                    $skus = $return_data['sku_details'];
                    foreach ($skus as $key => $detail) {
                        $orderItem = "";
                        $qty_refunded = 0;
                        $orderItem = $order->getItemsCollection()->getItemByColumnValue('sku', $detail['merchant_sku']);
                        $qty_refunded = (int)$orderItem->getData('qty_refunded');
                        if ($qty_refunded > 0) {
                            Mage::getSingleton('adminhtml/session')->addError("Order Item with sku : " . $detail['merchant_sku'] . " is refunded without using Return Functionality.");
                            $this->_redirect('*/*/return');
                            return;
                        }
                    }
                }


            } 

            elseif ($returnModel->getData('return_details') != "") {

                $return_ser_data = "";
                $return_ser_data = $returnModel->getData('return_details');

                $return_data = "";
                $return_data = unserialize($return_ser_data);
                
                $resulting_data['status'] = $returnModel->getData('status');
                $resulting_data['id'] = $returnModel->getData('id');
                $resulting_data['returnid'] = $returnModel->getData('returnid');
                $resulting_data['merchant_order_id'] = $return_data->merchant_order_id;

                $magento_order_id = 0;
                $magento_order_id = $helper->getMagentoIncrementOrderId($return_data->merchant_order_id);

                
               if($magento_order_id!=NULL){
                    
                    if (is_numeric($magento_order_id) &&  $magento_order_id == 0) {
                        Mage::getSingleton('adminhtml/session')->addError('Incomplete information of Order in Return.');
                        $this->_redirect('*/*/return');
                        return;  
                    }

                    if(is_numeric($magento_order_id)==false  &&  $magento_order_id == '') {
                        Mage::getSingleton('adminhtml/session')->addError('Incomplete information of Order in Return.');
                        $this->_redirect('*/*/return');
                        return;  
                    }
                }else{
                    Mage::getSingleton('adminhtml/session')->addError('Incomplete information of Order in Return.');
                    $this->_redirect('*/*/return');
                    return;      

                }      


                //if ($magento_order_id == 0) {
                   // Mage::getSingleton('adminhtml/session')->addError('Incomplete information of Order in Return.');
                   // $this->_redirect('*/*/return');
                   // return;
                //}

                $order = "";
                $order = Mage::getModel('sales/order')->loadByIncrementId($magento_order_id);
                if (!$order->getId()) {
                    Mage::getSingleton('adminhtml/session')->addError("Order not Exists for this return.");
                    $this->_redirect('*/*/return');
                    return;

                }
                $refund_flag = $helper->checkOrderInRefund($return_data->merchant_order_id);
                if ($refund_flag) {
                    Mage::getSingleton('adminhtml/session')->addError("Refund of this order already exists.Return can't be generated.");
                    $this->_redirect('*/*/return');
                    return;
                }
                $resulting_data['merchant_return_authorization_id'] = $return_data->merchant_return_authorization_id;
                $resulting_data['merchant_return_charge'] = $return_data->merchant_return_charge;
                $resulting_data['reference_order_id'] = $return_data->reference_order_id;
                $resulting_data['reference_return_authorization_id'] = $return_data->reference_return_authorization_id;
                $resulting_data['refund_without_return'] = $return_data->refund_without_return;
                $resulting_data['return_date'] = $return_data->return_date;
                $resulting_data['return_status'] = $return_data->return_status;
                $resulting_data['shipping_carrier'] = $return_data->shipping_carrier;
                $resulting_data['tracking_number'] = $return_data->tracking_number;
                $i = 0;
                $error_msg = "";
                foreach ($return_data->return_merchant_SKUs as $sku_detail) {
                    $check = array();
                    $check = $helper->getRefundedQtyInfo($order, $sku_detail->merchant_sku);
                   
                    if ($check['error'] == '1') {
                        $error_msg = $error_msg . "<br/>Error for Order Item with sku : " . $sku_detail->merchant_sku . " ";
                        $error_msg = $error_msg . $check['error_msg'];
                        //continue;
                        Mage::getSingleton('adminhtml/session')->addError($error_msg);
                        $this->_redirect('*/*/return');
                        return;
                    }
                    //$resulting_data['sku_details']["sku$i"]['created']=0;
                    $resulting_data['sku_details']["sku$i"]['changes_made'] = 0;
                    $resulting_data['sku_details']["sku$i"]['qty_already_refunded'] = $check['qty_already_refunded'];
                    $resulting_data['sku_details']["sku$i"]['available_to_refund_qty'] = $check['available_to_refund_qty'];
                    $resulting_data['sku_details']["sku$i"]['qty_ordered'] = $check['qty_ordered'];
                    $resulting_data['sku_details']["sku$i"]['order_item_id'] = $sku_detail->order_item_id;
                    $resulting_data['sku_details']["sku$i"]['return_quantity'] = $sku_detail->return_quantity;
                    $resulting_data['sku_details']["sku$i"]['merchant_sku'] = $sku_detail->merchant_sku;
                    $resulting_data['sku_details']["sku$i"]['reason'] = $sku_detail->reason;
                    $resulting_data['sku_details']["sku$i"]['return_principal'] = $sku_detail->requested_refund_amount->principal;
                    $resulting_data['sku_details']["sku$i"]['return_tax'] = $sku_detail->requested_refund_amount->tax;
                    $resulting_data['sku_details']["sku$i"]['return_shipping_cost'] = $sku_detail->requested_refund_amount->shipping_cost;
                    $resulting_data['sku_details']["sku$i"]['return_shipping_tax'] = $sku_detail->requested_refund_amount->shipping_tax;
                    $i++;
                }
                if ($i == 0) {
                    Mage::getSingleton('adminhtml/session')->addError("No items found in return order.");
                    $this->_redirect('*/*/return');
                    return;
                }
            }
            Mage::register('return_data', $resulting_data);

            $this->loadLayout();

            $this->_setActiveMenu('jet/set_time');
            $this->_addBreadcrumb('Return Manager', 'Return Manager');
            $this->_addBreadcrumb('Return Description', 'Return Description');

            $this->getLayout()->getBlock('head')
                ->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()
                ->createBlock('jet/adminhtml_return_edit'))
                ->_addLeft($this->getLayout()
                        ->createBlock('jet/adminhtml_return_edit_tabs')
                );
            $this->renderLayout();
        } else {
            Mage::register('return_data', '');

            Mage::getSingleton('adminhtml/session')->addError('Incomplete information for Return');

            $this->_redirect('*/*/jetorder');
        }
    }

    public function detailAction()
    {
        $this->loadLayout();
        $this->renderLayout();

    }

    public function acknowledgeAction()
    {

        $orderid = $this->getRequest()->getParam('order_id');
        $Incrementid = $this->getRequest()->getParam('increment_id');


        $resultdata = Mage::getModel('jet/jetorder')->getCollection()
            ->addFieldToFilter('magento_order_id', $Incrementid)
            ->addFieldToSelect('order_data')
            ->addFieldToSelect('id')
            ->addFieldToSelect('merchant_order_id')
            ->getData();


        if (empty($resultdata) || count($resultdata) == 0) {
            Mage::getSingleton('adminhtml/session')->addError('Order can not be acknowledged no information found.');

            $this->_redirect("adminhtml/sales_order/view", array('order_id' => $orderid));
            return;
        }

        $serialize_data = unserialize($resultdata[0]['order_data']);

        // check datat exist in the table or not
        if (empty($serialize_data) || count($serialize_data) == 0) {
            // data not exist so insert the data into "jet_orderdetail" table
            // call API jet
            $result = Mage::helper('jet')->CGetRequest('/orders/withoutShipmentDetail/' . $resultdata[0]['merchant_order_id']);
            $Ord_result = json_decode($result);

            if (empty($result) || count($result) == 0) {
                Mage::getSingleton('adminhtml/session')->addError('unable to get order information from Jet.com for acknowledge.');
                $this->_redirect("adminhtml/sales_order/view", array('order_id' => $orderid));
                return;
            } else {
                //serialize
                $jobj = Mage::getModel('jet/jetorder')->load($resultdata[0]['id']);
                $jobj->setOrderData(serialize($Ord_result));
                $jobj->save();

                $serialize_data = $Ord_result;
            }
        }

        if (empty($serialize_data)) {
            Mage::getSingleton('adminhtml/session')->addError('unable to get order information from Jet.com for acknowledge.');
            $this->_redirect("adminhtml/sales_order/view", array('order_id' => $orderid));
            return;
        }

        $fullfill_array = array();
        foreach ($serialize_data->order_items as $k => $valdata) {
            $fullfill_array[] = array('order_item_acknowledgement_status' => 'fulfillable',
                'order_item_id' => $valdata->order_item_id);
        }


        $order_id = $resultdata[0]['merchant_order_id'];
        $data_var = array();
        $data_var['acknowledgement_status'] = "accepted";


        $data_var['order_items'] = $fullfill_array;


        $data = Mage::helper('jet')->CPutRequest('/orders/' . $order_id . '/acknowledge', json_encode($data_var));

        $response = json_decode($data);

        if (!empty($response) && $response != null && count($response->errors) > 0 && $response->errors[0] != "") {
            Mage::getSingleton('adminhtml/session')->addError('Order not Acknowledged error: ' . $response->errors[0] . ' on Jet.com');

            $this->_redirect("adminhtml/sales_order/view", array('order_id' => $orderid));
            return;
        } else {
            $modeldata = Mage::getModel('jet/jetorder')->getCollection()
                ->addFieldToFilter('magento_order_id', $Incrementid)->getData();

            if (count($modeldata) > 0) {
                $id = $modeldata[0]['id'];
                $model = Mage::getModel('jet/jetorder')->load($id);//->addData($update);
                $model->setStatus('acknowledged');
                $model->save();
                /*
                                $order = Mage::getModel('sales/order')->load($orderid);
                                if($order->canInvoice()) {
                                    $invoiceId = Mage::getModel('sales/order_invoice_api')->create($order->getIncrementId(), array());
                                    $invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceId);
                                    $invoice->save();
                                }*/

                Mage::getSingleton('adminhtml/session')->addSuccess('Your Jet Order ' . $Incrementid . ' has been acknowledged successfully');
            } else {
                Mage::getSingleton('adminhtml/session')->addError('Your Jet Order ' . $Incrementid . ' has been acknowledged but on updated on Jet.com.');
            }

        }

        $this->_redirect("adminhtml/sales_order/view", array('order_id' => $orderid));

        return;

    }

    public function rejectreasonAction()
    {

        $orderid = $this->getRequest()->getParam('order_id');
        $Incrementid = $this->getRequest()->getParam('increment_id');

        $resultdata = Mage::getModel('jet/jetorder')->getCollection()
            ->addFieldToFilter('magento_order_id', $Incrementid)
            ->getData();


        if (count($resultdata) > 0) {
            $JorderData = unserialize($resultdata[0]['order_data']);

            if (empty($JorderData) || count($JorderData) == 0) {
                // now call api to update order data
                $result = Mage::helper('jet')->CGetRequest('/orders/withoutShipmentDetail/' . $resultdata[0]['merchant_order_id']);
                $Ord_result = json_decode($result);

                if (empty($result) || count($result) == 0) {
                    Mage::getSingleton('adminhtml/session')->addError('unable to get order information from Jet.com for rejection.');
                    $this->_redirect("adminhtml/sales_order/view", array('order_id' => $orderid));
                } else {
                    //serialize
                    $jobj = Mage::getModel('jet/jetorder')->load($resultdata[0]['id']);
                    $jobj->setOrderData(serialize($Ord_result));
                    $jobj->save();

                    //data saved now set into registry
                    Mage::register('current_jetorder', $Ord_result);
                }
            } else {
                // We have order data form api now save it into registry
                Mage::register('current_jetorder', $JorderData);
            }

        } else {
            Mage::getSingleton('adminhtml/session')->addSuccess('No information found for this jet order.');

            $this->_redirect("adminhtml/sales_order/view", array('order_id' => $orderid));
        }

        $this->loadLayout();
        $this->renderLayout();

    }

    public function rejectAction()
    {

        $rej_Order_reasonArr = array(
            'rejected_item_error' => 'rejected - item level error',
            'rejected_shiploc_error' => 'rejected - ship from location not available',
            'rejected_shipmeth_error' => 'rejected - shipping method not supported',
            'rejected_addr_error' => 'rejected - unfulfillable address',
            'accepted' => 'accepted',
        );


        $rej_item_reasonArr = array(
            'nonfulfillable_skuerr' => 'nonfulfillable - invalid merchant SKU',
            'nonfulfillable_inven_err' => 'nonfulfillable - no inventory',
            'fulfillable' => 'fulfillable'
        );


        $Incrementid = $this->getRequest()->getParam('increment_id');
        $merchant_order_id = $this->getRequest()->getParam('merchant_order_id');
        $order_id = $this->getRequest()->getParam('order_id');
        $items_order_idsArr = $this->getRequest()->getParam('order_item_id'); // order items ids array

        $order_Ack_status = $this->getRequest()->getParam('acknowledgement_status');
        $order_item_level_status = $this->getRequest()->getParam('order_item_acknowledgement_status');

        $reject_items_arr = array();

        if (isset($Incrementid) && $Incrementid != null) { // proceed

            // if order ack status is fulfill accepted
            if ($order_Ack_status == 'accepted') {
                Mage::getSingleton('adminhtml/session')->addError('You have to acknowledge order if order Acknowledgement Status is "accepted" othervise select other reasons from acknowledgement status.');

                $this->_redirect('adminhtml/adminhtml_jetorder/rejectreason/',
                    array('increment_id' => $Incrementid, 'order_id' => $order_id));
            }

            // if item level error exist
            if ($order_Ack_status == 'rejected_item_error') {
                //check option for
                $item_issue = true;
                $count_total = count($order_item_level_status);
                $fullfillsel_count = 0;

                foreach ($order_item_level_status as $k => $valdata) {
                    if ($valdata == 'fulfillable') {
                        $fullfillsel_count++;
                    }

                    $reject_items_arr[] = array(
                        'order_item_acknowledgement_status' => $rej_item_reasonArr[$order_item_level_status[$k]],
                        'order_item_id' => $items_order_idsArr[$k]);

                }

                if ($fullfillsel_count == $count_total) {

                    Mage::getSingleton('adminhtml/session')->addError('You can not select all orders item level "fullfillable" if you have selected "Acknowledgement status" rejected - item level error.');

                    $this->_redirect('adminhtml/adminhtml_jetorder/rejectreason',
                        array('increment_id' => $Incrementid, 'order_id' => $order_id));
                    return;
                }

            } else { // in normal case
                foreach ($order_item_level_status as $k => $valdata) {

                    $reject_items_arr[] = array(
                        'order_item_acknowledgement_status' => $rej_item_reasonArr[$order_item_level_status[$k]],
                        'order_item_id' => $items_order_idsArr[$k]);
                }
            }
            // All thinsg fine now go for acknowledgement

            $data_var = array();
            $data_var['acknowledgement_status'] = $rej_Order_reasonArr[$order_Ack_status];
            $data_var['order_items'] = $reject_items_arr;


            // Call Jet.com APi to reject error

            $modeldata = Mage::getModel('jet/jetorder')->getCollection()->addFieldToFilter('magento_order_id', $Incrementid)->getData();

            // var_dump($data_var);echo '<br/><br/>';

            $data = Mage::helper('jet')->CPutRequest('/orders/' . $merchant_order_id . '/acknowledge', json_encode($data_var));


            $response = json_decode($data);

            //var_dump($response);die;

            if (!empty($response) && $response != null && count($response->errors) > 0 && $response->errors[0] != "") {
                // api call failed
                Mage::getSingleton('adminhtml/session')->addError('Order not Rejected error: ' . $response->errors[0] . ' on Jet.com');

                $this->_redirect("adminhtml/sales_order/view", array('order_id' => $order_id));
                return;

            } else {
                // api called successfull
                $modeldata = Mage::getModel('jet/jetorder')->getCollection()->addFieldToFilter('magento_order_id', $Incrementid)->getData();

                if (count($modeldata) > 0) {
                    try {
                        $id = $modeldata[0]['id'];
                        $model = Mage::getModel('jet/jetorder')->load($id); //->addData($update);
                        $model->setStatus('rejected');
                        $model->save();
                        // now cancel the Order order state
                        $order = Mage::getModel('sales/order')->loadByIncrementID($Incrementid);
                        if ($order->canCancel()) {
                            try {
                                $order->cancel();
                                $order->getStatusHistoryCollection(true);
                                $order->save();
                                // now convert to jet rejected order state
                                /*$order->setState("jet_rejected", true);
                                $order->save();*/
                                Mage::getSingleton('adminhtml/session')->addSuccess('Your Jet Order ' . $Incrementid . ' has been rejected');
                            } catch (Exception $e) {
                                Mage::getSingleton('adminhtml/session')->addError('Your Jet Order rejected on Jet.com but not updated in magento because of this error: ' . $e->getMessage());

                            }
                        }
                    } catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError('Your Jet Order rejected on Jet.com but not updated in magento because of this error: ' . $e->getMessage());
                    }
                }

            }


        } else {
            Mage::getSingleton('adminhtml/session')->addError('Sorry!! No information found for this order.');
        }
        $this->_redirect("adminhtml/sales_order/view", array('order_id' => $order_id));
    }


    public function shippAction()
    {
        Mage::getSingleton('core/session')->setShip_by_jet(true);

        $offset_end = Mage::helper('jet/jet')->getStandardOffsetUTC();
        if (empty($offset_end) || trim($offset_end) == '') {
            $offset = '.0000000-00:00';
        } else {
            $offset = '.0000000' . trim($offset_end);
        }
        $shipTodatetime = strtotime($this->getRequest()->getPost('ship_todate'));
        $exptime = strtotime($this->getRequest()->getPost('exp_deliver'));
        $carrtime = strtotime($this->getRequest()->getPost('carre_pickdate'));
        // get time values
        $Ship_todate = date("Y-m-d", $shipTodatetime) . 'T' . date("H:i:s", $shipTodatetime) . $offset;
        $Exp_delivery = date("Y-m-d", $exptime) . 'T' . date("H:i:s", $exptime) . $offset;
        $Carr_pickdate = date("Y-m-d", $carrtime) . 'T' . date("H:i:s", $carrtime) . $offset;

        // jet_order_detail table row id
        $jet_order_row = $this->getRequest()->getPost('order_table_row');

        $id = $this->getRequest()->getPost('key1');
        $orderid = $this->getRequest()->getPost('order');
        $carrier = $this->getRequest()->getPost('carrier');
        $service = $this->getRequest()->getPost('request_service_level');
        $order_id = $this->getRequest()->getPost('orderid');
        $tracking = $this->getRequest()->getPost('tracking');


        $items_data = $this->getRequest()->getPost('items');
        $items_data = json_decode($items_data);

        

        if (count($items_data) == 0) {
            echo "You have no any item in your Order.";
            return;
        }

        $address1 = Mage::getStoreConfig('jet_options/ced_jetaddress/jet_address1');
        $address2 = Mage::getStoreConfig('jet_options/ced_jetaddress/jet_address2');
        $city = trim(Mage::getStoreConfig('jet_options/ced_jetaddress/jet_city'));
        $state = trim(Mage::getStoreConfig('jet_options/ced_jetaddress/jet_state'));
        $zip = trim(Mage::getStoreConfig('jet_options/ced_jetaddress/jet_zip'));
		


        if (trim($zip) == "") {
            echo "kindly set zip code from system configuration";
            return;
        }
        if (trim($state) == "") {
            echo "kindly set state from system configuration";
            return;
        }
        if (trim($city) == "") {
            echo "kindly set city from system configuration";
            return;
        }
        if (trim($address1) == "") {
            echo "kindly set address from system configuration";
            return;
        }

        // create Retrun location Array
        $Arry_returnLoc = array('address1' => $address1,
            'address2' => $address2,
            'city' => $city,
            'state' => $state,
            'zip_code' => $zip
        );

        $shipment_arr = array();
        $error = false;
        $errormsg = "";
        $update_shipp_qty = array();
        $ship_qty_for_order = array();
        $cancel_qty_for_order = array();

        /* interchange logic once confirmed by jet for shipment related query */
        $cancel_jet_order = false;
        //$cancel_jet_order=0;
        //$can_cancel_jetorder = 0;
        /* change logic end */

        $jetHelper = Mage::helper('jet/jet');

        /* test case started  */
        $orderModel = Mage::getModel('jet/jetorder')->load($jet_order_row);
        $prev_ship_items_info = $jetHelper->getShipped_Cancelled_Qty($orderModel);
        $base_order_detail = $jetHelper->getOrdered_Cancelled_Qty($orderModel);
        /* test case end */

        $total_order_qty = 0;
        $prev_shipped_qty = 0;
        $total_prev_shipped_qty = 0;
        $prev_cancelled_qty = 0;
        $total_jet_orderqty = 0;
        $total_jet_cancelqty = 0;
        $order_is_complete = false ;
        $cancel_rest_order = false ;
        $cancel_qty=0;
        $real_cancel_qty=0;
        $total_avail_qty = 0;



        foreach ($items_data as $k => $valdata) {

            /* uncomment logic once confirmed by jet for shipment related query */
            //$can_cancel_jetorder++ ;
            /* change logic end */

           /* if($valdata[10] == 'complete'){
                continue;
            }*/

            if($valdata[7] <= 0){continue;}
            $total_avail_qty += $valdata[7];

             if($base_order_detail){
                $ordered_qty = $base_order_detail[$valdata[0]]['request_sku_quantity'];
                $cancel_qty = $base_order_detail[$valdata[0]]['request_cancel_qty'];
                $total_order_qty += $ordered_qty;
             }

            $can_qunt = isset($valdata[2]) ? (int)$valdata[2]  : 0;

            $total_jet_orderqty += (int)$valdata[6];
            if($valdata[2] != 0){$total_jet_cancelqty+=(int)$valdata[2];}

            if ($prev_ship_items_info) {
                $prev_shipped_qty = $prev_ship_items_info[$valdata[0]]['response_shipment_sku_quantity'];
                $prev_cancelled_qty = $prev_ship_items_info[$valdata[0]]['response_shipment_cancel_qty'];
                $validate = $jetHelper->validateShipment($ordered_qty , $cancel_qty , $prev_shipped_qty , $prev_cancelled_qty , (int)$valdata[6] , (int)$valdata[2] , $valdata[0]);
                $total_prev_shipped_qty += $prev_shipped_qty;

                if($validate != "clear"){
                    Mage::getSingleton('adminhtml/session')->addError($validate);
                    break;
                }
            }

            $real_cancel_qty += $prev_cancelled_qty ;
            $product_id = Mage::getModel("catalog/product")->getIdBySku($valdata[0]);
            $update_shipp_qty[$product_id] = (int)$valdata[6];

            // Auto generate Shipment Id of every item
            $time = time() + ($k + 1);
            $shp_id = implode("-", str_split($time, 3));

            if ($valdata[3] == 1) {
                $rma = isset($valdata[4]) ? $valdata[4] : "";
                $day_return = isset($valdata[5]) ? (int)$valdata[5] : 0;

                if (($can_qunt <= (int)$valdata[7]) && ($can_qunt != 0)) {

                    $updated_qty = (int)$valdata[6];
                    /*$cancel_order = ($updated_qty == 0 ? true : false);*/
                    $shipment_arr[] = array('shipment_item_id' => "$shp_id",
                        'merchant_sku' => $valdata[0],
                        'response_shipment_sku_quantity' => $updated_qty,
                        'response_shipment_cancel_qty' => $can_qunt,
                        'RMA_number' => "$rma",
                        'days_to_return' => $day_return,
                        'return_location' => $Arry_returnLoc
                    );
                    if ($updated_qty != 0) {
                        $ship_qty_for_order[$valdata[0]] = $updated_qty;
                    }
                    if($can_qunt != 0){
                        $cancel_qty_for_order[$valdata[0]] = $can_qunt;
                    }
                } else {
                    $updated_qty = (int)$valdata[6];
                    /*$cancel_order = ($updated_qty == 0 ? true : false);*/
                    $shipment_arr[] = array('shipment_item_id' => "$shp_id",
                        'merchant_sku' => $valdata[0],
                        'response_shipment_sku_quantity' => $updated_qty,
                        'response_shipment_cancel_qty' => $can_qunt,
                        'RMA_number' => "$rma",
                        'days_to_return' => $day_return,
                        'return_location' => $Arry_returnLoc
                    );
                    if ($updated_qty != 0) {
                        $ship_qty_for_order[$valdata[0]] = $updated_qty;
                    }
                    if($can_qunt != 0){
                        $cancel_qty_for_order[$valdata[0]] = $can_qunt;
                    }
                }
            } else {
                $shipment_arr[] = array('shipment_item_id' => "$shp_id",
                    'merchant_sku' => $valdata[0],
                    'response_shipment_sku_quantity' => (int)$valdata[1],
                    'response_shipment_cancel_qty' => (int)$can_qunt
                );
                if ($updated_qty != 0) {
                    $ship_qty_for_order[$valdata[0]] = $updated_qty;
                }
                if($can_qunt != 0){
                    $cancel_qty_for_order[$valdata[0]] = $can_qunt;
                }
            }
        }
        if ($error) {
            echo $errormsg;
            return;
        }


        $unique_random_number = $id.mt_rand(10,10000);
        $data_ship = array();
        $data_ship['shipments'][] = array(
            'alt_shipment_id' => $unique_random_number,
            'shipment_tracking_number' => "$tracking",
            'response_shipment_date' => $Ship_todate,
            'response_shipment_method' => '',
            'expected_delivery_date' => $Exp_delivery,
            'ship_from_zip_code' => "$zip",
            'carrier_pick_up_date' => $Carr_pickdate,
            'carrier' => $carrier,
            'shipment_items' => $shipment_arr
        );

        if ($data_ship) {
            if(($total_jet_orderqty + $total_jet_cancelqty + $real_cancel_qty + $total_prev_shipped_qty) == $total_order_qty){$order_is_complete = true;}
            if(($total_jet_cancelqty == $total_order_qty) && (!$prev_ship_items_info)){ $cancel_order = true;}
            if($prev_ship_items_info && ($total_jet_cancelqty == $total_avail_qty)){ $cancel_rest_order = true;}

            $data = Mage::helper('jet')->CPutRequest('/orders/' . $order_id . '/shipped', json_encode($data_ship));
            $responsedata = json_decode($data);

            $jetmodel = Mage::getModel('jet/jetorder')->load($jet_order_row);
            $jet_reference_id = $jetmodel->getId();            

            if ((($responsedata == NULL) || ($responsedata == "")) && ($jet_reference_id)) {
                $order = Mage::getModel('sales/order')->loadByIncrementId($id);
                $itemQty = array();
                $itemQtytoCancel = array();
                foreach ($order->getAllVisibleItems() as $item) {
                    $ship_sku = $item->getSku();
                    if($ship_qty_for_order[$ship_sku] > 0){
                        $itemQty[$item->getId()] = $ship_qty_for_order[$ship_sku];
                    }
                    if($cancel_qty_for_order[$ship_sku] > 0){
                        $itemQtytoCancel[$item->getId()] = $cancel_qty_for_order[$ship_sku];
                    }
                }
                try {
                    if($cancel_rest_order && $order_is_complete){
                        if(count($itemQtytoCancel)>0){
                            $this->generateCreditMemo($order ,$itemQtytoCancel);
                        }
                        $this->markOrderComplete($order);
                        $this->saveJetShipData($jetmodel , $data_ship , $cancel_order , $order_is_complete);
                        Mage::getSingleton('adminhtml/session')->addSuccess('Your Jet Order ' . $id . ' has been Completed.');
                        echo "Success";
                        return;
                    }elseif($cancel_order && $order_is_complete){
                        if(count($itemQtytoCancel)>0){
                            $this->generateCreditMemo($order ,$itemQtytoCancel);
                        }
                        $this->cancelOrderinMagento($order);
                        $this->saveJetShipData($jetmodel , $data_ship , $cancel_order , $order_is_complete);
                        $this->markOrderComplete($order);
                        Mage::getSingleton('adminhtml/session')->addError('Your Jet Order ' . $id . ' has been Cancelled.' );
                        echo "Success";
                        return;
                    }
                    elseif($order_is_complete){
                        if(count($itemQty)>0){
                            if ($order->canInvoice()) $this->generateInvoice($order, $itemQty);
                            if ($order->canShip()) $this->generateShipment($order, $itemQty);
                        }
                        if(count($itemQtytoCancel)>0){
                            $this->generateCreditMemo($order ,$itemQtytoCancel);
                        }
                        $this->markOrderComplete($order);
                        $this->saveJetShipData($jetmodel , $data_ship , $cancel_order , $order_is_complete);
                        Mage::getSingleton('adminhtml/session')->addSuccess('Your Jet Order ' . $id . ' has been Completed.');
                        echo "Success";
                        return;
                    }elseif($cancel_order){
                        if(count($itemQtytoCancel)>0){
                            $this->generateCreditMemo($order ,$itemQtytoCancel);
                        }
                        $this->cancelOrderinMagento($order);
                        $this->saveJetShipData($jetmodel , $data_ship , $cancel_order , $order_is_complete);
                        Mage::getSingleton('adminhtml/session')->addError('Order Cancellation request for cancelled item(s) has been sent for Jet Order ' . $id );
                        echo "Success";
                        return;
                    }
                    else{
                        if(count($itemQty)>0) {
                            if ($order->canInvoice()) $this->generateInvoice($order, $itemQty);
                            if ($order->canShip()) $this->generateShipment($order, $itemQty);
                        }
                        if(count($itemQtytoCancel)>0){
                            $this->generateCreditMemo($order ,$itemQtytoCancel);
                        }
                        $this->saveJetShipData($jetmodel , $data_ship , $cancel_order , $order_is_complete);
                        Mage::getSingleton('adminhtml/session')->addSuccess('Your Jet Order ' . $id . ' is under progress on Jet .');
                        echo "Success";
                        return;
                    }

                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            } else {
                echo $responsedata->errors[0];
                return;
            }
        } else {
            echo "You have no information to Ship on Jet.com";
            return;
        }

    }

    public function generateInvoice($order, $itemQty)
    {
        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($itemQty);
        $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
        $invoice->register();
        $invoice->getOrder()->setCustomerNoteNotify(false);
        $invoice->getOrder()->setIsInProcess(true);

        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder());

        $transactionSave->save();
    }

    public function generateShipment($order, $itemQty)
    {
        $shipment = $order->prepareShipment($itemQty);
        if ($shipment) {
            $shipment->register();
            $shipment->getOrder()->setIsInProcess(true);
            try {
                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($shipment)
                    ->addObject($shipment->getOrder())
                    ->save();
            } catch (Mage_Core_Exception $e) {
                Mage::log("Errror While Creating Shipment..." . $e->getMessage());
            }
        }
    }

    public function generateCreditMemo($order ,$itemQtytoCancel){
        $qtys = array('qtys' => $itemQtytoCancel);
        $service = Mage::getModel('sales/service_order', $order);
        $service->prepareCreditmemo($qtys)->register()->save();
    }

    public function markOrderComplete($order)
    {
        $order->setData('state', "complete");
        $order->setStatus("complete");
        $history = $order->addStatusHistoryComment(' Order was set to Complete by jet.com ', false);
        $history->setIsCustomerNotified(false);
        $order->save();
        $id = $order->getIncrementId();
        $model_data = Mage::getModel('jet/autoship')->getCollection()
            ->addFieldToFilter('order_id', $id)
            ->getData();
            if($model_data)
            {
           foreach ($model_data as $key => $value) {
            $model = Mage::getModel('jet/autoship')->load($value['id'])->setData('jet_shipment_status', 'shipped')->save();
            }  
           }
            
    }

    public function saveJetShipData($jetmodel, $data_ship, $cancel_jet_order, $order_is_complete)
    {
        $ship_dbdata = $jetmodel->getShipmentData();
        if(isset($ship_dbdata)){
            $temp_arr = unserialize($ship_dbdata);
            $temp_arr["shipments"][]=$data_ship["shipments"][0];
        }else{$temp_arr = $data_ship;}

        if ($cancel_jet_order) {
            $jetmodel->setStatus('cancelled');
            $jetmodel->setShipmentData(serialize($temp_arr));
            $jetmodel->save();
        } elseif($order_is_complete) {
            $jetmodel->setStatus('complete');
            $jetmodel->setShipmentData(serialize($temp_arr));
            $jetmodel->save();
        } else{
            $jetmodel->setStatus('inprogress');
            $jetmodel->setShipmentData(serialize($temp_arr));
            $jetmodel->save();
        }
    }

    public function cancelOrderinMagento($orderModel){
        if ($orderModel->canCancel()) {
            $orderModel->cancel();
            $orderModel->setStatus('canceled');
            $orderModel->save();
        }
    }

    public function shippingexceptionAction()
    {

        $fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
        $sku = $this->getRequest()->getPost('sku');
        $chargeamount = $this->getRequest()->getPost('chargeamount');
        $exceptiontype = $this->getRequest()->getPost('exceptiontype');
        $shippinglevel = $this->getRequest()->getPost('shippinglevel');
        $shippingmethod = $this->getRequest()->getPost('shippingmethod');
        $overridetype = $this->getRequest()->getPost('override');

        if ($shippinglevel) {
            $shipping = array();
            $shipping['fulfillment_nodes'][] = array('fulfillment_node_id' => "$fullfillmentnodeid", 'shipping_exceptions' => array(array('service_level' => $shippinglevel, 'override_type' => $overridetype, 'shipping_charge_amount' => (int)$chargeamount, 'shipping_exception_type' => $exceptiontype)));
        } else {
            $shipping = array();
            $shipping['fulfillment_nodes'][] = array('fulfillment_node_id' => "$fullfillmentnodeid", 'shipping_exceptions' => array(array('shipping_method' => $shippingmethod, 'override_type' => $overridetype, 'shipping_charge_amount' => (int)$chargeamount, 'shipping_exception_type' => $exceptiontype)));

        }
        $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $sku . '/shippingexception', json_encode($shipping));

    }

    public function failedordersAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('jet/failedorders');
        $this->renderLayout();
    }

    public function gridAction()

    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('jet/adminhtml_failedorders_grid')->toHtml()
        );

    }

    public function jetOrderAction()
    {

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'jetorders.csv';
        $grid = $this->getLayout()->createBlock('jet/adminhtml_jetorder_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /*
     * @acknowledge Orders
     */
    public function massAcknowledgeOrderAction()
    {
        $successcount = 0;
        $error = array();
        if (sizeof($this->getRequest()->getParam('order_ids')) > 0) {
            $OrderIds = $this->getRequest()->getParam('order_ids');
            foreach ($OrderIds as $orderid) {
                $resultdata = Mage::getModel('jet/jetorder')->load($orderid);
                if (sizeof($resultdata) > 0 && $resultdata->getStatus() == "ready") {
                    $serialize_data = unserialize($resultdata->getOrderData());
                    /* check data not exist into the table or not */
                    if (empty($serialize_data) || count($serialize_data) == 0) {
                        /* data not exist so insert the data into "jet_orderdetail" table
                           call API jet
                        */
                        $result = Mage::helper('jet')->CGetRequest('/orders/withoutShipmentDetail/' . $resultdata->getMerchantOrderId());
                        $Ord_result = json_decode($result);

                        if (empty($result) || count($result) == 0) {
                            $error[] = 'Unable to get order information from Jet.com for acknowledge';
                            continue;
                        } else {
                            $resultdata->setOrderData(serialize($Ord_result));
                            $resultdata->save();
                            $serialize_data = $Ord_result;
                        }
                    }
                    if (empty($serialize_data)) {
                        $error[] = 'unable to get order information from Jet.com for acknowledge.';
                        continue;
                    }
                    $fullfill_array = array();
                    foreach ($serialize_data->order_items as $k => $valdata) {
                        $fullfill_array[] = array('order_item_acknowledgement_status' => 'fulfillable',
                            'order_item_id' => $valdata->order_item_id);
                    }

                    $order_id = $resultdata->getMerchantOrderId();
                    $data_var = array();
                    $data_var['acknowledgement_status'] = "accepted";

                    $data_var['order_items'] = $fullfill_array;

                    $data = Mage::helper('jet')->CPutRequest('/orders/' . $order_id . '/acknowledge', json_encode($data_var));
                    $response = json_decode($data);

                    if (count($response->errors) > 0 && $response->errors[0] != "") {
                        $error[] = 'Order not Acknowledged error: ' . $response->errors[0] . ' on Jet.com';
                        continue;

                    } else {
                        $resultdata->setStatus('acknowledged');
                        $resultdata->save();
                        ++$successcount;
                    }

                } else {
                    $error[] = 'Order can not be acknowledged.';
                }
            }
        } else {
            $error[] = 'Order can not be acknowledged no information found.';
        }

        if ($successcount) {
            Mage::getSingleton('adminhtml/session')->addSuccess($successcount . ' Jet Order has been Acknowledged Successfully!');
        }
        if (sizeof($error) > 0) {
            foreach ($error as $message) {
                Mage::getSingleton('adminhtml/session')->addError($message);
            }
        }

        $this->_redirect('*/*/jetorder');
    }

    /*
     * @Delete Failed Jet Orders Log
     */
    public function deletejetorderlogAction()
    {
        $successcount = 0;
        if (sizeof($this->getRequest()->getParam('order_ids')) > 0) {

            $logjetorders = $this->getRequest()->getParam('order_ids');
            foreach ($logjetorders as $orderid) {
                $OrderErrorLog = Mage::getModel('jet/orderimport')->load($orderid);
                try {
                    if (sizeof($OrderErrorLog) > 0) {
                        $OrderErrorLog->delete();
                        ++$successcount;
                    }
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
        }
        if ($successcount > 0) {
            Mage::getSingleton('adminhtml/session')->addSuccess($successcount . ' Jet Order Log Deleted Successfully!');
        }
        $this->_redirect('adminhtml/adminhtml_jetorder/failedorders');
    }

    public function exportReturnCsvAction()
    {
        $fileName = 'jetreturnorders.csv';
        $grid = $this->getLayout()->createBlock('jet/adminhtml_return_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
    public function autoshipAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    public function deleteautoshiplogAction()
    {
        $successcount = 0;
        if (sizeof($this->getRequest()->getParam('order_ids')) > 0) {

            $logjetorders = $this->getRequest()->getParam('order_ids');
            foreach ($logjetorders as $orderid) {
                $OrderErrorLog = Mage::getModel('jet/autoship')->load($orderid);
                try {
                    if (sizeof($OrderErrorLog) > 0) {
                        $OrderErrorLog->delete();
                        ++$successcount;
                    }
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
        }
        if ($successcount > 0) {
            Mage::getSingleton('adminhtml/session')->addSuccess($successcount . ' Jet Order shipment Log Deleted Successfully!');
        }
        $this->_redirect('adminhtml/adminhtml_jetorder/autoship');
    }
}
