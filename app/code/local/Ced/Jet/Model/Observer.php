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

class Ced_Jet_Model_Observer
{

    /**
     * Predispath admin action controller
     *
     * @param Varien_Event_Observer $observer
     */
    public function preDispatch(Varien_Event_Observer $observer)
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $feedModel = Mage::getModel('jet/feed');
            $feedModel->checkUpdate();
        }
    }

        public function adminhtmlWidgetContainerHtmlBefore($event) {
            $block = $event->getBlock();

            if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {
				$id=Mage::app()->getRequest()->getParam('order_id');
				$increment_id = Mage::getModel('sales/order')->load($id)->getIncrementId();

				$ifexist=count(Mage::getModel('jet/jetorder')->getCollection()->addFieldToFilter('magento_order_id',$increment_id));

				if($ifexist){
					$block->removeButton('order_ship');   // Remove tab by id
					$block->removeButton('order_invoice');
					$block->removeButton('order_creditmemo');
				}
            }
        }

        public function shipbyjet($observer)
        {

        $conName = Mage::app()->getRequest()->getControllerName();       

         if(( $conName != 'auctane' ) && ($conName != '') ) {
             $shipment = $observer->getEvent()->getShipment();
             $flag = false;
             $magento_order_id = $shipment->getOrder()->getIncrementId();
             $magento_order_data = Mage::getModel('jet/jetorder')->getCollection()->getData(); 
             $ses_var = Mage::getSingleton('core/session')->getShip_by_jet();

             foreach ($magento_order_data as $key => $value) {
                    if($value['magento_order_id'] == $magento_order_id)$flag = true;
                }
                if($flag == true && $ses_var == true)Mage::getSingleton('core/session')->unsShip_by_jet();                
                elseif($flag == true && !$ses_var)
                {
                     Mage::getSingleton('core/session')->unsShip_by_jet();
                     Mage::getSingleton('core/session')->addError('This Order is Jet Order create shipment by jet');
                    Mage::app()->getResponse()->setRedirect($_SERVER['HTTP_REFERER']);
                    Mage::app()->getResponse()->sendResponse();
                     exit;
                }
            }            
        }
	public function checkEnabled()
    {
        $helper = Mage::helper('jet');
        if (!$helper->isEnabled()) {
            Mage::app()->getFrontController()->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
            Mage::app()->getFrontController()->getResponse()->setHeader('Status', '404 File not found');
            $request = Mage::app()->getRequest();
            $request->initForward()
                ->setControllerName('indexController')
                ->setModuleName('Mage_Cms')
                ->setActionName('defaultNoRoute')
                ->setDispatched(false);
            return;
        }
    }
	/*
	*   this observer created for getting listing direct cancel orders
	*/
	public function directCancel(){
        $orderdata = Mage::helper('jet')->CGetRequest('/orders/directedCancel');
        //$response = json_decode($orderdata, true);

		$this->createOrder();


		/*
        //Mage::log($response, null, 'mylogfile.log');

        $autoReject=false;

        if (isset($response['order_urls']) && count($response['order_urls']) > 0) {
            foreach ($response['order_urls'] as $jetorderurl) {
                $result = Mage::helper('jet')->CGetRequest($jetorderurl);
                $resultObject = json_decode($result);
                $resultarray = json_decode($result, true);
                $orderitems_count = count($resultarray['order_items']);
                $recursive_itemcount = 0;
                $reject_items_arr = array();
                if (sizeof($resultarray) > 0 && isset($resultarray['merchant_order_id']) && ($resultarray['status']=='acknowledged')) {
                    foreach($resultarray['order_items'] as $arr){
                        $updatedQty=$arr['request_order_quantity'] - $arr['request_order_cancel_qty'];
                        $uniqui_id = mt_rand(10, 10000124);
                        $shipment_arr[] = array('shipment_item_id' => "$uniqui_id",
                            'merchant_sku' => $arr['merchant_sku'],
                            'response_shipment_sku_quantity' => (int)$updatedQty,
                            'response_shipment_cancel_qty' => (int)$arr['request_order_cancel_qty']
                        );

                        if($arr['request_order_quantity']==$arr['request_order_cancel_qty']){
                            $recursive_itemcount++;
                            $autoReject=true;
                            $reject_items_arr[] = array('order_item_acknowledgement_status' => 'nonfulfillable - invalid merchant SKU',
                                'order_item_id' => $arr['order_item_id']);
                        }
                    }

                    $merchantOrderid = $resultarray['merchant_order_id'];
                    $jetOrder = Mage::getModel('jet/jetorder')->getCollection()->addFieldToFilter('merchant_order_id', $merchantOrderid)->getFirstItem();
                    $id=$jetOrder->getId();
                    $mage_id=$jetOrder->getData('magento_order_id');
                    if ($autoReject && ($recursive_itemcount == $orderitems_count)) {
                        $order_id =$merchantOrderid;
                        $offset_end = Mage::helper('jet/jet')->getStandardOffsetUTC();
                        if (empty($offset_end) || trim($offset_end) == '') {
                            $offset = '.0000000-00:00';
                        } else {
                            $offset = '.0000000' . trim($offset_end);
                        }
                        $shipTodate = date("Y-m-d");
                        $shipTotime = date("h:i:s");
                        $storeId = Mage::getStoreConfig('jet_options/ced_jet/jet_storeid');
                        $website = Mage::getModel('core/store')->load($storeId);
                        $websiteId = $website->getWebsiteId();
                        $zip = "01001";
                        $tommorow = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 1, date("Y"));
                        $exptime = date("Y-m-d", $tommorow);
                        $Ship_todate = $shipTodate . 'T' . $shipTotime . $offset;
                        $Exp_delivery = date("Y-m-d", $exptime) . 'T' . $shipTotime . $offset;
                        $Carr_pickdate = date("Y-m-d", $exptime) . 'T' . $shipTotime . $offset;
                        $carrier = "FedEx";
                        $inc_id = mt_rand(10, 100001244);
                        $data_ship = array();
                        $data_ship['shipments'][] = array(
                            'alt_shipment_id' => $inc_id,
                            'shipment_tracking_number' => "$inc_id",
                            'response_shipment_date' => $Ship_todate,
                            'response_shipment_method' => '',
                            'expected_delivery_date' => $Exp_delivery,
                            'ship_from_zip_code' => "$zip",
                            'carrier_pick_up_date' => $Carr_pickdate,
                            'carrier' => "$carrier",
                            'shipment_items' => $shipment_arr
                        );

                        if ($data_ship) {
                            $data = Mage::helper('jet')->CPutRequest('/orders/' . $order_id . '/shipped', json_encode($data_ship));
                            $responsedata = json_decode($data);

                            if($responsedata == NULL){
                                $saveJetorder=Mage::getModel('jet/jetorder')->load($id);
                                $saveJetorder->setData('status','cancelled');
                                $saveJetorder->save();

                                $orderModel = Mage::getModel('sales/order')->loadByIncrementId($mage_id);
                                if($orderModel->canCancel()){
                                    $orderModel->cancel();
                                    $orderModel->setStatus('canceled');
                                    $orderModel->save();
                                }
                            }
                        }
                    }

                    echo $id.'<br/>';
                    if(strlen($id)){
                    $updateJetOrderData=Mage::getModel('jet/jetorder')->load($id);
                    $updateJetOrderData->setData('order_data',serialize($resultObject));
                    $updateJetOrderData->setData('customer_cancelled',1);
                    $updateJetOrderData->save();
                    }
                }
            }
        } */
	}

    public function cancelJetorder($observer)
    {
        $order = $observer->getOrder();
        $url=Mage::helper('core/url')->getCurrentUrl();
        $inc = $order->getData('increment_id');
        $reject=Mage::getModel('jet/jetorder')->getCollection()->addFieldToFilter('magento_order_id', $inc)->getFirstItem();

        if (($order->getState() == Mage_Sales_Model_Order::STATE_CANCELED) && (preg_match("/\bcancel\b/i", $url)) && ($reject->getId()!='')) {
            $order_id =$reject->getData('merchant_order_id');
            $offset_end = Mage::helper('jet/jet')->getStandardOffsetUTC();
            if (empty($offset_end) || trim($offset_end) == '') {
                $offset = '.0000000-00:00';
            } else {
                $offset = '.0000000' . trim($offset_end);
            }
            $shipTodate = date("Y-m-d");
            $shipTotime = date("h:i:s");
            $storeId = Mage::getStoreConfig('jet_options/ced_jet/jet_storeid');
            $website = Mage::getModel('core/store')->load($storeId);
            $websiteId = $website->getWebsiteId();
            $zip = "01001";
            $tommorow = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 1, date("Y"));
            $exptime = date("Y-m-d", $tommorow);
            $Ship_todate = $shipTodate . 'T' . $shipTotime . $offset;
            $Exp_delivery = date("Y-m-d", $exptime) . 'T' . $shipTotime . $offset;
            $Carr_pickdate = date("Y-m-d", $exptime) . 'T' . $shipTotime . $offset;

            $inc_id = $order->getData("increment_id");

            $temp_carrier = unserialize($reject->getData('order_data'));
            $carrier = $temp_carrier->order_detail->request_shipping_carrier;
            if(($carrier == '') || ($carrier == null)){$carrier = 'Fedex';}

            $shipment_arr = array();
            $items = $order->getAllItems();
            foreach ($items as $i) {

                $uniqui_id = mt_rand(10, 10000124);
                $shipment_arr[] = array('shipment_item_id' => "$uniqui_id",
                    'merchant_sku' => $i->getSku(),
                    'response_shipment_sku_quantity' => 0,
                    'response_shipment_cancel_qty' => (int)$i->getData('qty_ordered')
                );
            }

            $data_ship = array();
            $data_ship['shipments'][] = array(
                'alt_shipment_id' => $inc_id,
                'shipment_tracking_number' => "$inc_id",
                'response_shipment_date' => $Ship_todate,
                'response_shipment_method' => '',
                'expected_delivery_date' => $Exp_delivery,
                'ship_from_zip_code' => "$zip",
                'carrier_pick_up_date' => $Carr_pickdate,
                'carrier' => "$carrier",
                'shipment_items' => $shipment_arr
            );

            if ($data_ship) {
                $data = Mage::helper('jet')->CPutRequest('/orders/' . $order_id . '/shipped', json_encode($data_ship));
                $responsedata = json_decode($data);

                if($responsedata == NULL){
                    $this->saveJetOrder($reject->getId());
                    /*$jetId=$reject->getId();
                    $saveJetorder=Mage::getModel('jet/jetorder')->load($jetId);
                    $saveJetorder->setData('status','rejected');
                    $saveJetorder->save();*/
                }

             }
        }
    }

    public function updateProduct()
    {
        $success_count = 0;
        $error_count = 0;
        $collection = Mage::getModel('jet/fileinfo')->getCollection()->addFieldToFilter('Status', 'Acknowledged')->addFieldToSelect('jet_file_id')->addFieldToSelect('id')->getData();

        $error_file_count = 0;
        foreach ($collection as $jdata) {

            if (isset($jdata['jet_file_id']) && $jdata['jet_file_id'] != null) {
                $response = Mage::helper('jet')->CGetRequest('/files/' . $jdata['jet_file_id']);
                $resvalue = json_decode($response);
                if (trim($resvalue->status) == 'Processed with errors') {
                    $error_count++;
                    $jetfileid = trim($resvalue->jet_file_id);
                    $filename = $resvalue->file_name;
                    $filetype = $resvalue->file_type;
                    $status = trim($resvalue->status);
                    $error = $resvalue->error_excerpt;
                    $comma_separatederrors = implode(",", $error);

                    $update = array('status' => trim($resvalue->status));
                    $model = Mage::getModel('jet/fileinfo')->load($jdata['id'])->addData($update);
                    $model->save();

                    $collectiond = Mage::getModel('jet/errorfile')->getCollection()->addFieldToFilter('jetinfofile_id', $jdata['id']);

                    if (count($collectiond) == (int)0) {

                        $data = array(
                            'jet_file_id' => $jetfileid,
                            'file_name' => $filename,
                            'file_type' => $filetype,
                            'status' => $status,
                            'error' => $comma_separatederrors,
                            'date' => date('Y-m-d H:i:s'),
                            'jetinfofile_id' => $jdata['id'],
                        );

                        $model1 = Mage::getModel('jet/errorfile')->addData($data);
                        $model1->save();

                        $error_file_count++;
                    }

                } else {

                    $update = array('status' => trim($resvalue->status));
                    $model = Mage::getModel('jet/fileinfo')->load($jdata['id'])->addData($update);
                    $model->save();
                    $success_count++;

                }
            }
        }

    }

    /**
     * @Jet Orders Synchronisation
     * Create Available Jet Orders in Magento
     */
    public function createOrder()
    {
		$storeId = Mage::getStoreConfig('jet_options/ced_jet/jet_storeid');
        $website = Mage::getModel('core/store')->load($storeId);
        $websiteId = $website->getWebsiteId();
        $store = Mage::app()->getStore($website->getCode());

        $orderdata = Mage::helper('jet')->CGetRequest('/orders/ready');
        $response = json_decode($orderdata, true);


        if (isset($response['order_urls']) && count($response['order_urls']) > 0) {
            $count = 0;

            foreach ($response['order_urls'] as $jetorderurl) {
				$result = Mage::helper('jet')->CGetRequest($jetorderurl);
                $resultObject = json_decode($result);
                $result = json_decode($result, true);

				$email=$resultObject->hash_email;

				if($email=='' && $email ==NULL){
					$email ='customer@jet.com';
				}

				$customer = Mage::getModel('customer/customer')
							->setWebsiteId($websiteId)
							->loadByEmail($email);

                if (sizeof($result) > 0 && isset($result['merchant_order_id'])) {

                    $merchantOrderid = $result['merchant_order_id'];
                    $resultdata = Mage::getModel('jet/jetorder')->getCollection()->addFieldToFilter('merchant_order_id', $merchantOrderid);

                    if (count($resultdata) <= 0) {
                        $customer = $this->_assignCustomer($result, $customer, $websiteId, $store, $email);
                        if (!$customer) {
                            return;
                        } else {
                            $this->prepareQuote($result, $customer, $websiteId, $store, $email, $resultObject);
                        }
                    }
                }
                Mage::unregister('attributeClear');
            }

        }
    }
//delete files once in a day code start
    public function jetfilesDelete()
    {
        $url = Mage::getBaseDir();
        $path = $url.'/var/jetupload/';
        $handle = opendir($path);
         if ($handle = opendir($path))
         { 
            while (false !== ($file = readdir($handle)))
              { 
                 $filelastmodified = filemtime($path . $file);
                 //24 hours in a day * 3600 seconds per hour
                if((time() - $filelastmodified) > 24*3600 && is_file($file))
                 {
                    unlink($path . $file);
                }

             }
            closedir($handle); 
        }
    }
//delete files once in a day code end


    public function _assignCustomer($result, $customer, $websiteId, $store, $email)
    {
        if (is_object($customer) && $customer->getId() == NULL && $customer->getId() == '') {
            try {
                $Cname = $result['buyer']['name'];
                if (trim($Cname) == '' || $Cname == null) {
                    $Cname = $result['shipping_to']['recipient']['name'];
                }
                $Cname = preg_replace('/\s+/', ' ', $Cname);
                $customer_name = explode(' ', $Cname);

                if (!isset($customer_name[1]) || $customer_name[1] == '') {
                    $customer_name[1] = $customer_name[0];
                }

                $customer = Mage::getModel('customer/customer');
                $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($customer_name[0])
                    ->setLastname($customer_name[1])
                    ->setEmail($email)
                    ->setPassword("password");
                $customer->save();


                return $customer;

            } catch (Exception $e) {
                //$message = "please check the customer Email Id either email format or enter email properly into jet setting!";
                $message = $e->getMessage();
                $jetOrderError = Mage::getModel('jet/orderimport');
                $jetOrderError->setMerchantOrderId($result['merchant_order_id']);
				$jetOrderError->setReferenceNumber($result['reference_order_id']);
                $jetOrderError->setReason($message);
                $jetOrderError->save();
                return false;
            }
        } else {
            return $customer;
        }

     }

    public function prepareQuote($result, $customer, $websiteId, $store, $email, $resultObject)
    {
        /*$productexist_config = Mage::getStoreConfig('jet_options/acknowledge_options_options/exist');
        $productoutofstock_config = Mage::getStoreConfig('jet_options/acknowledge_options/outofstock');
        $productdisabled_config = Mage::getStoreConfig('jet_options/acknowledge_options/pdisabled');
        $Quote_execute = false;
        */

        $shippingMethod = 'shipjetcom';
        $paymentMethod= Mage::getStoreConfig('jet_options/ced_jet/jet_default_payment');
        if($paymentMethod== NULL || $paymentMethod==""){
            $paymentMethod = 'payjetcom';
        }
        $productArray = array();
        $baseCurrency = $store->getBaseCurrencyCode();
        $items_array = $result['order_items'];
        $baseprice = 0;
        $shippingcost = 0;
        $tax = 0;
        $storeId = $store->getId();
        $autoReject = false;
        $reject_items_arr = array();
        $order = Mage::getModel('sales/order')
            ->setStoreId($storeId)
            ->setQuoteId(0)
            ->setGlobal_currency_code($baseCurrency)
            ->setBase_currency_code($baseCurrency)
            ->setStore_currency_code($baseCurrency)
            ->setOrder_currency_code($baseCurrency);

        foreach ($items_array as $item) {
            $message = '';
            $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $item['merchant_sku']);
            if ($product) {
                $product = Mage::getModel('catalog/product')->load($product->getEntityId());
                if ($product->getStatus() == '1') {
                    $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                    if (($stock->getQty() > 0) && ($stock->getIsInStock() == '1') && ($stock->getQty() >= $item['request_order_quantity']) && ($item['request_order_quantity'] != $item['request_order_cancel_qty'])) {
                        $productArray[] = array('id' => $product->getEntityId(), 'qty' => $item['request_order_quantity']);
                        $price = $item['item_price']['base_price'];
                        $qty = $item['request_order_quantity'];
                        $cancelqty= $item['request_order_cancel_qty'];
                        //if($cancelqty!=0)$qty=$qty - $cancelqty;
                        $baseprice += $qty * $price;
                        $shippingcost += ($item['item_price']['item_shipping_cost'] * $qty) + ($item['item_price']['item_shipping_tax'] * $qty);
                        $tax += $item['item_price']['item_tax'];

                        $rowTotal = $price * $qty;
                        $orderItem = Mage::getModel('sales/order_item')
                            ->setStoreId($storeId)
                            ->setQuoteItemId(0)
                            ->setQuoteParentItemId(NULL)
                            ->setProductId($product->getEntityId())
                            ->setProductType($product->getTypeId())
                            ->setQtyBackordered(NULL)
                            ->setTotalQtyOrdered($qty)
                            ->setQtyOrdered($qty)
                            ->setName($product->getName())
                            ->setSku($product->getSku())
                            ->setPrice($price)
                            ->setBasePrice($price)
                            ->setOriginalPrice($price)
                            ->setRowTotal($rowTotal)
                            ->setBaseRowTotal($rowTotal);

                        //$subTotal += $rowTotal;
                        $order->addItem($orderItem);
                        $Quote_execute = True;

                    } else {

                        /*if ($productoutofstock_config) {*/
                            $autoReject = true;
                            $reject_items_arr[] = array('order_item_acknowledgement_status' => 'nonfulfillable - invalid merchant SKU',
                                'order_item_id' => $item['order_item_id']);
                        /*}*/
                        $message = "Product " . $item['merchant_sku'] . " is Out Of Stock";
                        $jetOrderError = Mage::getModel('jet/orderimport');
                        $jetOrderError->setMerchantOrderId($result['merchant_order_id']);
						$jetOrderError->setReferenceNumber($result['reference_order_id']);
                        $jetOrderError->setReason($message);
                        $jetOrderError->setOrderItemId($item['order_item_id']);
                        $jetOrderError->save();
                        $Quote_execute = False;
                    }
                } else {


                    /*if ($productdisabled_config) {*/
                        $autoReject = true;
                        $reject_items_arr[] = array('order_item_acknowledgement_status' => 'nonfulfillable - invalid merchant SKU',
                            'order_item_id' => $item['order_item_id']);
                    /*}*/
                    $message = "Product " . $item['merchant_sku'] . " is Not Enabled!";
                    $jetOrderError = Mage::getModel('jet/orderimport');
                    $jetOrderError->setMerchantOrderId($result['merchant_order_id']);
					$jetOrderError->setReferenceNumber($result['reference_order_id']);
                    $jetOrderError->setReason($message);
                    $jetOrderError->setOrderItemId($item['order_item_id']);
                    $jetOrderError->save();
                    $Quote_execute = False;
                }
            }else{


                /*if ($productexist_config) {*/
                    $autoReject = true;
                    $reject_items_arr[] = array('order_item_acknowledgement_status' => 'nonfulfillable - invalid merchant SKU',
                        'order_item_id' => $item['order_item_id']);
                /*}*/
                $message = "Product SKU " . $item['merchant_sku'] . " Not Found on the site!";
                $jetOrderError = Mage::getModel('jet/orderimport');
                $jetOrderError->setMerchantOrderId($result['merchant_order_id']);
				$jetOrderError->setReferenceNumber($result['reference_order_id']);
                $jetOrderError->setReason($message);
                $jetOrderError->setOrderItemId($item['order_item_id']);
                $jetOrderError->save();
                $Quote_execute = False;
            }
        }

        if ($autoReject) {
            $data_var = array();
            $data_var['acknowledgement_status'] = "rejected - item level error";
            $data_var['order_items'] = $reject_items_arr;
            $data = Mage::helper('jet')->CPutRequest('/orders/' . $result['merchant_order_id'] . '/acknowledge', json_encode($data_var));
            $response = json_decode($data);

            $reject=Mage::getModel('jet/jetorder')->getCollection()->addFieldToFilter('merchant_order_id', $result['merchant_order_id'])->getFirstItem();
            if($response == NULL){
                //$this->saveJetOrder($reject->getId());
            }
        }

        if (count($productArray) > 0 && count($items_array) == count($productArray) && !$autoReject) {
            $transaction = Mage::getModel('core/resource_transaction');
            $order->setCustomer_email($customer->getEmail())
                ->setCustomerFirstname($customer->getFirstname())
                ->setCustomerLastname($customer->getLastname())
                ->setCustomerGroupId($customer->getGroupId())
                ->setCustomer_is_guest(0)
                ->setCustomer($customer);

            $CshippingInfo = $result['shipping_to']['recipient']['name'];
            $CshippingInfo = preg_replace('/\s+/', ' ', $CshippingInfo);
            $customer_shippingInfo = explode(' ', $CshippingInfo);

            if (!isset($customer_shippingInfo[1]) || $customer_shippingInfo[1] == '') {
                $customer_shippingInfo[1] = $customer_shippingInfo[0];
            }
            // set Billing Address
            try {
                $billing = $customer->getDefaultBillingAddress();
                $complete_address1 = $result['shipping_to']['address']['address1'];
                $complete_address2 = $result['shipping_to']['address']['address2'];

                if ($complete_address2 != null && trim($complete_address2) != '') {
                    $complete_address1 = $complete_address1 . ' ' . $complete_address2;
                }
                $billingAddress = Mage::getModel('sales/order_address')
                    ->setStoreId($storeId)
                    ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
                    ->setCustomerId($customer->getId())
                    ->setCustomerAddressId($customer->getDefaultBilling())
                    ->setCustomer_address_id('')
                    ->setPrefix('')
                    ->setFirstname($customer_shippingInfo[0])
                    ->setMiddlename('')
                    ->setLastname($customer_shippingInfo[1])
                    ->setSuffix('')
                    ->setCompany('')
                    //->setStreet($result['shipping_to']['address']['address1'])
                    ->setStreet($complete_address1)
                    ->setCity($result['shipping_to']['address']['city'])
                    ->setCountry_id('US')
                    ->setRegion($result['shipping_to']['address']['state'])
                    ->setRegion_id('')
                    ->setPostcode($result['shipping_to']['address']['zip_code'])
                    ->setTelephone($result['shipping_to']['recipient']['phone_number'])
                    ->setFax('');
                $order->setBillingAddress($billingAddress);

                $shipping = $customer->getDefaultShippingAddress();
                $shippingAddress = Mage::getModel('sales/order_address')
                    ->setStoreId($storeId)
                    ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
                    ->setCustomerId($customer->getId())
                    ->setCustomerAddressId($customer->getDefaultShipping())
                    ->setCustomer_address_id('')
                    ->setPrefix('')
                    ->setFirstname($customer_shippingInfo[0])
                    ->setMiddlename('')
                    ->setLastname($customer_shippingInfo[1])
                    ->setSuffix('')
                    ->setCompany('')
                    //->setStreet($result['shipping_to']['address']['address1'])
                    ->setStreet($complete_address1)
                    ->setCity($result['shipping_to']['address']['city'])
                    ->setCountry_id('US')
                    ->setRegion($result['shipping_to']['address']['state'])
                    ->setRegion_id('')
                    ->setPostcode($result['shipping_to']['address']['zip_code'])
                    ->setTelephone($result['shipping_to']['recipient']['phone_number'])
                    ->setFax('');
                $order->setShippingAddress($shippingAddress)
                    ->setShippingMethod($shippingMethod)
                    ->setShippingDescription(Mage::getStoreConfig("carriers/shipjetcom/title") . "-" . $shippingMethod);
                $orderPayment = Mage::getModel('sales/order_payment')
                    ->setStoreId($storeId)
                    ->setCustomerPaymentId(0)
                    ->setMethod($paymentMethod);
                //->setPo_number(' - ');
                $order->setPayment($orderPayment);


                $order->setSubtotal($baseprice)
                    ->setBaseSubtotal($baseprice)
                    ->setShippingAmount($shippingcost)
                    ->setBaseShippingAmount($shippingcost)
                    ->setTaxAmount($tax)
                    ->setBaseTaxAmount($tax)
                    ->setGrandTotal($baseprice + $shippingcost + $tax)
                    ->setBaseGrandTotal($baseprice + $shippingcost + $tax);

                $transaction->addObject($order);
                $transaction->addCommitCallback(array($order, 'place'));
                $transaction->addCommitCallback(array($order, 'save'));
                if ($transaction->save() && $order->getId() > 0) {
                    $OrderData = array('order_item_id' => $result['order_items'][0]['order_item_id'],
                        'merchant_order_id' => $result['merchant_order_id'],
                        'merchant_sku' => $result['order_items'][0]['merchant_sku'],
                        'deliver_by' => $result['order_detail']['request_delivery_by'],
                        'magento_order_id' => $order->getIncrementId(),
                        'status' => $result['status'],
                        'order_data' => serialize($resultObject),
                        'reference_order_id' => $result['reference_order_id']
                    );

                    $model = Mage::getModel('jet/jetorder')->addData($OrderData);
                    $model->save();
                    //if (Mage::getStoreConfig('jet_options/jet_order/active') == 1) {
                     	 $this->autoOrderacknowledge($order->getIncrementId());
                    //}

                    if($order->canInvoice()) {
                        /*$invoiceId = Mage::getModel('sales/order_invoice_api')
                            ->create($order->getIncrementId(), array());
                        $invoice = Mage::getModel('sales/order_invoice')
                            ->loadByIncrementId($invoiceId);
                        $invoice->capture()->save();*/

                        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
                        $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
                        $invoice->register();
                        $invoice->getOrder()->setCustomerNoteNotify(false);
                        $invoice->getOrder()->setIsInProcess(true);

                        $transactionSave = Mage::getModel('core/resource_transaction')
                            ->addObject($invoice)
                            ->addObject($invoice->getOrder());

                        $transactionSave->save();
                    }
                }

            } catch (Exception $e) {
                $message = "Fail To Create Order Due to Error " . $e->getMessage();
                $jetOrderError = Mage::getModel('jet/orderimport');
                $jetOrderError->setMerchantOrderId($result['merchant_order_id']);
				        $jetOrderError->setReferenceNumber($result['reference_order_id']);
                $jetOrderError->setOrderItemId($item['order_item_id']);
                $jetOrderError->setReason($message);
                $jetOrderError->save();

            }
        }
    }

    /*
     * @Auto Order Acknowledgement Process
     */
    public function autoOrderacknowledge($Incrementid)
    {
        $resultdata = Mage::getModel('jet/jetorder')->getCollection()
            ->addFieldToFilter('magento_order_id', $Incrementid)
            ->addFieldToSelect('order_data')
            ->addFieldToSelect('id')
            ->addFieldToSelect('merchant_order_id')
            ->getData();


        if (empty($resultdata) || count($resultdata) == 0) {
            return 0;
        }

        $serialize_data = unserialize($resultdata[0]['order_data']);

        if (empty($serialize_data) || count($serialize_data) == 0) {

            $result = Mage::helper('jet')->CGetRequest('/orders/withoutShipmentDetail/' . $resultdata[0]['merchant_order_id']);
            $Ord_result = json_decode($result);

            if (empty($result) || count($result) == 0) {
                return 0;
            } else {

                $jobj = Mage::getModel('jet/jetorder')->load($resultdata[0]['id']);
                $jobj->setOrderData(serialize($Ord_result));
                $jobj->save();

                $serialize_data = $Ord_result;
            }
        }

        if (empty($serialize_data)) {
            return 0;
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
            return 0;
        } else {
            $modeldata = Mage::getModel('jet/jetorder')->getCollection()
                ->addFieldToFilter('magento_order_id', $Incrementid)->getData();

            if (count($modeldata) > 0) {
                $id = $modeldata[0]['id'];
                $model = Mage::getModel('jet/jetorder')->load($id);
                $model->setStatus('acknowledged');
                $model->save();
            }
        }
        return 0;
    }

    public function addButton(Varien_Event_Observer $event)
    {

        $block = $event->getBlock();
        if ($block->getId() != 'sales_order_view') {
            return $this;
        }

        $order = $block->getOrder();
        if (!$order->getId()) {
            return $this;
        }

        $orderid = $order->getId();
        $Incrementid = $order->getIncrementId();
        $resultdata = Mage::registry('current_jet_order');

        if (count($resultdata) == 0) {
            $resultdata = Mage::getModel('jet/jetorder')->getCollection()
                ->addFieldToFilter('magento_order_id', $Incrementid)
                //->addFieldToSelect('status')
                ->getData();
        }
        $status = $resultdata[0]['status'];
        if ($status == 'ready') {
            $block->addButton('delete', array(
                'label' => 'Acknowledge',
                'class' => 'Acknowledge',
                'onclick' => 'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure you want to send acknowledge for this order?')
                    . '\', \'' . $block->getUrl('adminhtml/adminhtml_jetorder/acknowledge', array('increment_id' => $Incrementid)) . '\')',
            ));
            $block->addButton('delete1', array(
                'label' => 'Reject',
                'class' => 'Reject',
                'onclick' => 'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure you want to send rejection for this order?')
                    . '\', \'' . $block->getUrl('adminhtml/adminhtml_jetorder/rejectreason', array('increment_id' => $Incrementid)) . '\')',
            ));
        } else if ($status == 'acknowledged') {

            $block->addButton('delete', array(
                'label' => 'Acknowledge',
                'class' => 'Acknowledge',
                'disabled' => true,
                'onclick' => 'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure you want to send acknowledge for this order?')
                    . '\', \'' . $block->getUrl('adminhtml/adminhtml_jetorder/acknowledge', array('increment_id' => $Incrementid)) . '\')',


            ));

            $block->addButton('delete1', array(
                'label' => 'Reject',
                'class' => 'Reject',
                'disabled' => true,
                'onclick' => 'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure you want to send rejection for this order?')
                    . '\', \'' . $block->getUrl('adminhtml/adminhtml_jetorder/reject', array('increment_id' => $Incrementid)) . '\')',


            ));
        }
    }

    public function saveAttribute(Varien_Event_Observer $event)
    {
        $attribute = $event->getDataObject();
        $mcode = $attribute->getAttributeCode();
        $jetId = Mage::app()->getRequest()->getParam('jet_attribute_id');
        $mjetattr_id = trim($jetId);
        $map_attribute = false;
		$exist=false;

        if (isset($mjetattr_id) && $mjetattr_id != null && $mjetattr_id != '') {
           $exist =true;

        } else {
           $exist =false;
        }

        $resource = Mage::getSingleton('core/resource');
        $table = $resource->getTableName('jet/jetattribute');

		$readconnection = $resource->getConnection('core_read');
		$query = 'SELECT * FROM ' . $table . ' WHERE `magento_attr_id`=' . $attribute->getId().'';
		$row = $readconnection->fetchRow($query);

		if($exist){
            if (count($row) > 0 && isset($row['id']) && $row['id'] != '') {
				if($mjetattr_id == $row['jet_attr_id']){
					$map_attribute = false;
				}else{
					$query = 'DELETE FROM ' . $table . ' WHERE `id` =' . $row['id'] . '';
	                $writeConnection = $resource->getConnection('core_write');
    	            $writeConnection->query($query);

					$map_attribute = true;
				}
			}else{
				$map_attribute = true;
			}
        }else{
			if (count($row) > 0 && isset($row['id']) && $row['id'] != '') {
				$query = 'DELETE FROM ' . $table . ' WHERE `id` =' . $row['id'].'';
                $writeConnection = $resource->getConnection('core_write');
                $writeConnection->query($query);
            }
			$map_attribute = false;
		}

        if ($map_attribute) {

            $units_or_options = array();
            $csv = new Varien_File_Csv();
            $file = Mage::getBaseDir("var") . DS . "jetcsv" . DS . "Jet_Taxonomy_attribute.csv";

            if (!file_exists($file)) {
                Mage::getSingleton('adminhtml/session')->addError('Jet Extension Csv missing please check "Jet_Taxonomy_attribute.csv" exist at "var/jetcsv" location.');
                return;
            }

            $taxonomy = $csv->getData($file);
            unset($taxonomy[0]);

            $save_attr_id = false;
            $save_attr_unitType = false;

            foreach ($taxonomy as $txt) {
                if (number_format($txt[0], 0, '', '') == $mjetattr_id) {

                    $save_attr_id = number_format($txt[0], 0, '', '');
                    $save_attr_unitType = $txt[3];
                    break;
                }
            }

            if ($save_attr_id == false) {
                Mage::getSingleton('adminhtml/session')->addError('Requested Jet Attribute id:  ' . $jetId . ' does not exist in CSV.');
                return;
            }

            $csv = new Varien_File_Csv();
            $file = Mage::getBaseDir("var") . DS . "jetcsv" . DS . "Jet_Taxonomy_attribute_value.csv";


            if (!file_exists($file)) {
                Mage::getSingleton('adminhtml/session')->addError('Jet Extension Csv missing please check "Jet_Taxonomy_attribute_value.csv" exist at "var/jetcsv" location.');
                return;
            }
            $taxonomy = $csv->getData($file);


            unset($taxonomy[0]);
            try {
                if ($save_attr_unitType == 2) {
                    foreach ($taxonomy as $txt) {
                        $numberfomat_id = number_format($txt[0], 0, '', '');

                        if ($save_attr_id == $numberfomat_id) {

                            $units_or_options[] = $txt[2];
                        }
                    }
                } else if ($save_attr_unitType == 0) {

                    foreach ($taxonomy as $txt) {
                        if ($save_attr_id == number_format($txt[0], 0, '', '')) {
                            $units_or_options[] = $txt[1];
                        }
                    }

                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }

            unset($query);

            $resource = Mage::getSingleton('core/resource');
            $table = $resource->getTableName('jet/jetattribute');
            $readconnection = $resource->getConnection('core_read');
            $query = 'SELECT `id` FROM ' . $table . ' WHERE `magento_attr_id`=' . $attribute->getId() . '';
            $row = $readconnection->fetchCol($query);

            $query = '';
            $excute_qry = false;
            $unit_or_option_exists = false;
            $allunits = '';
            $alloptions = '';

            if ($save_attr_unitType == 2) {

                if (count($units_or_options) > 0) {
                    $unit_or_option_exists = true;
                    $allunits = implode(',', $units_or_options);

                    if (count($row) > 0 && isset($row[0]) && $row[0] != '') {
                        $query = 'UPDATE ' . $table . ' SET `jet_attr_id` = ' . $save_attr_id . ', `magento_attr_code` = "' . $mcode . '" , `unit` = "' . addslashes($allunits) . '" ,`list_option` = Null , `freetext` = 2 WHERE `magento_attr_id` = ' . $attribute->getId() . '';
                        $excute_qry = true;
                    } else {

                        $query = 'INSERT INTO ' . $table . ' (jet_attr_id, magento_attr_id,magento_attr_code, freetext,unit,list_option) VALUES (' . $save_attr_id . ', ' . $attribute->getId() . ',"' . $mcode . '", 2 ,"' . addslashes($allunits) . '",Null)';
                        $excute_qry = true;
                    }
                } else {
                    if (count($row) > 0 && isset($row[0]) && $row[0] != '') {
                        $query = 'UPDATE ' . $table . ' SET `jet_attr_id` = ' . $save_attr_id . ' ,`magento_attr_code` = "' . $mcode . '", `unit`= NULL , `freetext` = 2  WHERE `magento_attr_id` = ' . $attribute->getId() . '';
                        $excute_qry = true;
                    } else {
                        $query = 'INSERT INTO ' . $table . ' (jet_attr_id, magento_attr_id, magento_attr_code,freetext)VALUES (' . $save_attr_id . ', ' . $attribute->getId() . ',"' . $mcode . '",2)';
                        $excute_qry = true;
                    }
                }
            } else if ($save_attr_unitType == 0) {

                if (count($units_or_options) > 0) {
                    $unit_or_option_exists = true;
                    $alloptions = implode(',', $units_or_options);

                    if (count($row) > 0 && isset($row[0]) && $row[0] != '') {

                        $query = 'UPDATE ' . $table . ' SET `jet_attr_id` = ' . $save_attr_id . ', `magento_attr_code` = "' . $mcode . '" , `unit` = Null, `list_option` = "' . $alloptions . '" , `freetext` = 0  WHERE `magento_attr_id` = ' . $attribute->getId() . '';
                        $excute_qry = true;
                    } else {
                        $query = 'INSERT INTO ' . $table . ' (jet_attr_id, magento_attr_id,magento_attr_code, freetext,unit,list_option) VALUES (' . $save_attr_id . ', ' . $attribute->getId() . ',"' . $mcode . '",0 ,Null,"' . $alloptions . '")';
                        $excute_qry = true;
                    }

                } else {
                    if (count($row) > 0 && isset($row[0]) && $row[0] != '') {

                        $query = 'UPDATE ' . $table . ' SET `jet_attr_id` = ' . $save_attr_id . ',`magento_attr_code` = "' . $mcode . '", `freetext` = 0 WHERE `magento_attr_id` = ' . $attribute->getId() . '';
                        $excute_qry = true;
                    } else {

                        $query = 'INSERT INTO ' . $table . ' (jet_attr_id, magento_attr_id,magento_attr_code, freetext) VALUES (' . $save_attr_id . ', ' . $attribute->getId() . ',"' . $mcode . '",0)';
                        $excute_qry = true;
                    }
                }
            } else {

                if (count($row) > 0 && isset($row[0]) && $row[0] != '') {
                    $query = 'UPDATE ' . $table . ' SET `jet_attr_id` = ' . $save_attr_id . ' ,`magento_attr_code` = "' . $mcode . '" , `freetext` = 1 WHERE `magento_attr_id` = ' . $attribute->getId() . '';
                    $excute_qry = true;
                } else {
                    $query = 'INSERT INTO ' . $table . ' (jet_attr_id, magento_attr_id,magento_attr_code, freetext) VALUES (' . $save_attr_id . ', ' . $attribute->getId() . ',"' . $mcode . '",1)';
                    $excute_qry = true;
                }

            }

            if ($excute_qry) {
					$resource = Mage::getSingleton('core/resource');
                try {

                    $writeConnection = $resource->getConnection('core_write');
                    $writeConnection->query($query);

                    if ($unit_or_option_exists == true) {
                        if ($save_attr_unitType == 2) {

                            $message = $save_attr_id . ' is a "UNIT" type attribute in Jet.com so you need to add options values for these units: ' . $allunits . ' Please view details in "Jet Attribute" tab inside of "Attribute Information"';
                            Mage::getSingleton('adminhtml/session')->addNotice($message);

                        } else {
                            if ($save_attr_unitType == 0) {
                                $message = $save_attr_id . ' is a "List" type attribute in Jet.com so you need to add these options values : ' . $alloptions . ' Please view details in "Jet Attribute" tab inside of "Attribute Information"';
                                Mage::getSingleton('adminhtml/session')->addNotice($message);

                            }
                        }
                    }
			    } catch (Exception $e) {
					$readconnection = $resource->getConnection('core_read');
					$query = 'SELECT * FROM ' . $resource->getTableName('jet/jetattribute') . ' WHERE `jet_attr_id`=' . $mjetattr_id.'';
					$row = $readconnection->fetchRow($query);
					if(count($mjetattr_id)>0){
						Mage::getSingleton('adminhtml/session')->addError("$mjetattr_id Jet Attribute id already mapped with Magento '".$row['magento_attr_code']."' Attribute please remove this mapping if you want to map $mjetattr_id Jet Attribute with $mcode.");
					}else{
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					}

                }
            }

        }


    }

    public function jetreturn()
    {
        $false_return = "";
        $success_return = "";
        $success_count = 0;
        $false_count = 0;
        $data = Mage::helper('jet')->CGetRequest('/returns/created');

        $response = json_decode($data);
        $response = $response->return_urls;


        if (!empty($response) && count($response) > 0) {

            foreach ($response as $res) {
                $arr = explode("/", $res);
                $returnid = "";
                $returnid = $arr[3];
                $resultdata = Mage::getModel('jet/jetreturn')->getCollection()->addFieldToFilter('returnid', $returnid)->getData();
               
                if (empty($resultdata)) {

                    $returndetails = Mage::helper('jet')->CGetRequest($res);
                    //print_r($returndetails);die();
                    if ($returndetails) {
                        $return = json_decode($returndetails);
                        $serialized_details = serialize($return);

						try{
							$text = array(
								 'merchant_order_id' => $return->merchant_order_id,
								 'status' => 'created',
								 'returnid' => "$returnid",
								 'return_details' => $serialized_details);

							$model = Mage::getModel('jet/jetreturn')->addData($text);
							$model->save();
						}catch(Exception $e){
							//echo $e->getMessage(); die;
						}

                        if ($success_return == "") {
                            $success_return = $returnid;
                            $success_count++;
                        } else {
                            $success_return = $success_return . " , " . $returnid;
                            $success_count++;
                        }
                    } else {
                        if ($false_return == "") {
                            $false_return = $returnid;
                            $false_count++;
                        } else {
                            $false_return = $false_return . " , " . $returnid;
                            $false_count++;
                        }
                    }


                }

            }
        }
    }

    public function updaterefund()
    {
        $res_arr=array('created','processing');
        $result = Mage::getModel('jet/jetrefund')->getCollection()->addFieldToFilter('refund_status', array(array('in' => $res_arr)))->addFieldToSelect('refund_id')->getData();
        $count = count($result);
        $success_count = 0;
        $success_ids = "";

        if ($count > 0) {

            foreach ($result as $res) {

                $refundid = "";
                $refundid = $res['refund_id'];
                $data = Mage::helper('jet')->CGetRequest('/refunds/state/' . $refundid . '');
                $responsedata = json_decode($data);
                $success_count++;
                if ($responsedata->refund_status != 'created') {
                    $modeldata = Mage::getModel('jet/jetrefund')->getCollection()->addFieldToFilter('refund_id', $refundid);
                    foreach ($modeldata as $models) {
                        $id = $models['id'];
                        $update = array('refund_status' => $responsedata->refund_status);
                        $model = "";
                        $model = Mage::getModel('jet/jetrefund')->load($id);
                        $model->addData($update);
                        $model->save();
                        $status = "";
                        $status = $responsedata->refund_status;
                        if (trim($status) == 'accepted') {
                            $saved_data = "";
                            $saved_data = $model->getData('saved_data');
                            if ($saved_data != "") {
                                $saved_data = unserialize($saved_data);
                                $flag = false;
                                $flag = Mage::helper('jet')->generateCreditMemoForRefund($saved_data);
                            }

                        }
                    }
                }
            }
        }
    }

    public function deleteAttribute(Varien_Event_Observer $event)
    {
        $attribute = $event->getDataObject();
        $jetattribute = Mage::getModel('jet/jetattribute');
        $collection = $jetattribute->getCollection()->addFieldToFilter('magento_attr_id', $attribute->getAttributeId());
        if (count($collection) > 0) {
            $jetattribute->load($collection->getFirstItem()->getId());
            $jetattribute->delete();
        }

    }

    public function deleteCategory(Varien_Event_Observer $event)
   	{
		$category = $event->getDataObject();
        $jet_cat_name = "Jet.com Category";
        $cat_name = "";
        $cat_name = $category->getName();
        $cat = "";
        $subcats = "";

            $cat = Mage::getModel('catalog/category')->load($category->getId());
            $subcats = $cat->getChildren();

            foreach (explode(',', $subcats) as $subCatid) {
                $jetCategory = "";
                $collection = "";
                $jetCategory = Mage::getModel('jet/jetcategory');
                $collection = $jetCategory->getCollection()->addFieldToFilter('magento_cat_id', $subCatid);
                if (count($collection) > 0) {
                    $jetCategory->load($collection->getFirstItem()->getId());
                    $jetCategory->delete();
                }
            }
            $jetCategory = "";
            $collection = "";
            $jetCategory = Mage::getModel('jet/jetcategory');
            $collection = $jetCategory->getCollection()->addFieldToFilter('magento_cat_id', $category->getId());
            if (count($collection) > 0) {
                $jetCategory->load($collection->getFirstItem()->getId());
                $jetCategory->delete();
            }
            return;
    }

    public function updatePassive_status()
    {
		$this->getProductByStatus('Archived', 'archived');
		$this->getProductByStatus('Excluded', 'excluded');
        $this->getProductByStatus('Unauthorized', 'unauthorized');
    }

    public function updateReview_status()
    {
        $this->getProductByStatus('Under Jet Review', 'under_jet_review');
		$this->getProductByStatus('Missing Listing Data', 'missing_listing_data');
    }

    public function updateActive_status()
    {
        $this->getProductByStatus('Available for Purchase', 'available_for_purchase');
    }

    public function getProductByStatus($status, $status_code)
    {


        //testing code start for report
        /*$raw_encode = rawurlencode('ProductStatus');
        $response = Mage::helper('jet')->CPostRequest('/reports/'. $raw_encode);
        
        $result = json_decode($response,true);
        $report_id = $result['report_id'];
        $rr = Mage::helper('jet')->CGetRequest('/reports/state/'. $report_id);
        print_r(json_decode($rr,true));die();*/
        //testing code end for report
		$collection = Mage::getResourceModel('catalog/product_collection');
		$count = $collection->getSize() + 100;

		$raw_encode = rawurlencode($status);
        $response = Mage::helper('jet')->CGetRequest('/portal/merchantskus?from=0&size=5000&statuses='.$raw_encode);
        $result = json_decode($response,true);

		$SKU = array();

		if(is_array($result) && isset($result['merchant_skus']) && count($result['merchant_skus'])>0){
			foreach ($result['merchant_skus'] as $sku) {
				 $SKU[] = $sku['merchant_sku'];
			}
		}

        if (count($SKU) == 0)
            return;

        $collection->addAttributeToFilter('sku', array('in', $SKU));
        $allIds = $collection->getAllIds();

        if (sizeof($allIds) > 0) {

            $chunk_data = array_chunk($allIds, 500);
            $resource = Mage::getSingleton('core/resource');
            $writeConnection = $resource->getConnection('core_write');

            /*foreach ($chunk_data as $k => $chunk) {
                $query = "Update " . $resource->getTableName('catalog_product_entity_varchar') . " cped
			   join " . $resource->getTableName('eav_attribute') . "  ev ON ev.attribute_id = cped.attribute_id
			   Set cped.value = '" . $status_code . "' where ev.attribute_code = 'jet_product_status' and  cped.entity_id in(" . implode(",", $chunk) . ")";
                $writeConnection->query($query);
            }*/
            foreach ($chunk_data as $k => $chunk) {
                for($i=0;count($chunk)>$i;$i++){
                    if(trim($chunk[$i])!=""){
                        $model="";
                        $model=Mage::getModel('catalog/product')->load(trim($chunk[$i]));
                        $model->setData('jet_product_status',$status_code);
                        $model->save();
                    }
                }
                
            }

        }

    }

    public function getupdatedStatus()
    {
		$this->getProductByStatus('Under Jet Review', 'under_jet_review');
        $this->getProductByStatus('Missing Listing Data', 'missing_listing_data');
        $this->getProductByStatus('Unauthorized', 'unauthorized');
        $this->getProductByStatus('Excluded', 'excluded');
        $this->getProductByStatus('Available for Purchase', 'available_for_purchase');
        $this->getProductByStatus('Archived', 'archived');

	}


  public function updateInventory($observer)
  {
      $order = $observer->getEvent()->getOrder();
      $_items = $order->getAllItems();
      $inventory = array();
      $fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
      $commaseperatedids = '';

      foreach ($_items as $_item) {
          $jet_product_status = "";
          $product_id = "";
          $product = "";
          $product1 = "";

          $product = $_item->getProduct();
          $product_id = $product->getId();

          $product1 = Mage::getModel('catalog/product')->loadByAttribute('sku', $product->getSku());
          if($product1){$jet_product_status = $product1->getData('jet_product_status');}


          if ($jet_product_status) {
              if ($jet_product_status == "missing_listing_data" ||
                  $jet_product_status == "available_for_purchase" ||
                  $jet_product_status == "under_jet_review") {
                  if ($product1->isConfigurable()) {
                              $simple_collection = Mage::getModel('catalog/product_type_configurable')->setProduct($product1)
                                  ->getUsedProductCollection()
                                  ->addAttributeToSelect('sku')
                                  ->addFilterByRequiredOptions();
                      foreach ($simple_collection  as $_item) {
                                      if ($_item->getData('type_id') == 'simple'){
                                          $sku = $_item->getSku();
                                          if ($commaseperatedids == "") {
                                              $commaseperatedids = $_item->getId();
                                          } else {
                                              $commaseperatedids = $commaseperatedids . "," .$_item->getId();
                                          }
                                          $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($_item);
                                          $qty = (int)$stock->getQty();
                                          if ($qty < 0) {
                                              $qty = 0;
                                          }
                                          $node1 = array();
                                          $node1['fulfillment_node_id'] = "$fullfillmentnodeid";
                                          $node1['quantity'] = $qty;
                                          $inventory[$sku]['fulfillment_nodes'][] = $node1;
                                  }
                              }
                  } elseif ($product1->getTypeId() == 'simple') {
                              $skus = $product1->getSku();
                              if ($commaseperatedids == "") {
                                  $commaseperatedids = $product1->getId();
                              } else {
                                  $commaseperatedids = $commaseperatedids . "," . $product1->getId();
                              }
                              $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product1);
                              $qty = (int)$stock->getQty();
                              if ($qty < 0) {
                                  $qty = 0;
                              }
                              $node1 = array();
                              $node1['fulfillment_node_id'] = "$fullfillmentnodeid";
                              $node1['quantity'] = $qty;
                              $inventory[$skus]['fulfillment_nodes'][] = $node1;
                         }

              }
          }
      }



      if (count($inventory) > 0) {

          try {

              $finalinventoryjson = "";
              $t = "";

              $finalinventoryjson = json_encode($inventory);
              $t = time();
              $file_type = "Inventory";
              $file_name = "inventrys" . $t . ".json";

              $file_path = Mage::getBaseDir("var") . DS . "jetupload" . DS . "inventrys" . $t . ".json";
              $myfile = fopen($file_path, "w");
              fwrite($myfile, $finalinventoryjson);
              fclose($myfile);
              if (fopen($file_path . ".gz", "r") == false) {
                  Mage::helper('jet')->gzCompressFile($file_path, 9);
              }

              $compressed_file_path = $file_path . ".gz";

              if (fopen($compressed_file_path, "r") != false) {

                  $response = Mage::helper('jet')->CGetRequest('/files/uploadToken');
                  $data = json_decode($response);
                  $fileinfo = $data->jet_file_id;
                  $tokenurl = $data->url;
                  $model = "";
                  $text = "";
                  $currentid = "";

                  $text = array('magento_batch_info' => $commaseperatedids,
                      'jet_file_id' => $fileinfo,
                      'token_url' => $tokenurl,
                      'file_name' => $file_name,
                      'file_type' => $file_type,
                      'status' => 'unprocessed');

                  $model = Mage::getModel('jet/fileinfo')->addData($text);
                  $model->save();

                  $currentid = $model->getId();

                  $reponse = Mage::helper('jet')->uploadFile($compressed_file_path, $data->url);
                  $postFields = '{"url":"' . $data->url . '","file_type":"' . $file_type . '","file_name":"' . $file_name . '"}';

                  $response = Mage::helper('jet')->CPostRequest('/files/uploaded', $postFields);
                  $data2 = json_decode($response);
                  if ($data2->status == 'Acknowledged') {
                      $update = array('status' => 'Acknowledged');
                      $model = Mage::getModel('jet/fileinfo')->load($currentid)->addData($update);
                      $model->save();
        }
              }
          } catch (Exception $e) {}

      }
  }

    /**
     * @save Jet Category information validation
     */
	/*
    public function jetCatInfocheck($observer)
    {
       $observer = $observer->getEvent()->getCategory();
        if (count($observer) > 0) {
            $is_jet_category = $observer->getIsJetCategory();
            $jet_category_id = $observer->getJetCategoryId();

            if ($observer->getId()) {

                if ($observer->getData('name') != NULL && $is_jet_category == '1' && (!is_numeric($jet_category_id) || $jet_category_id == null || $jet_category_id <= 0)) {

                    throw new Exception("Fail to Save Jet Category.If you chosen Yes Is Jet Category Information then  please enter the Jet Category Id! ");
                }
            }
        }
    }
	*/

    /**
     * @save Jet Category information save
     */
    public function jetCatInfosave($observer)
    {
        $observer = $observer->getEvent()->getCategory();
        if (count($observer) > 0 && $observer->getEntityId() > 0) {
            $jet_category_id = Mage::app()->getRequest()->getPost('custom_tab_text');
            $MagentoCategoryId = $observer->getEntityId();

            if ((strlen($jet_category_id) > 0) && ($jet_category_id > 0)) {
                $resource = Mage::getSingleton('core/resource');
                $readConnection = $resource->getConnection('core_read');
                $table_catlist = $resource->getTableName('jet/catlist');
                $table_catattr = $resource->getTableName('jet/jetcategory');

                $update = true;
                $query = 'SELECT * FROM  ' . $table_catattr . ' where jet_cate_id = "' . $jet_category_id . '"';

                $jcategorydata = $readConnection->fetchRow($query);

                $query2 = 'SELECT * FROM  ' . $table_catlist . ' where csv_cat_id = "' . $jet_category_id . '"';
                $jetattributemap = $readConnection->fetchRow($query2);


                if (!empty($jcategorydata) && count($jcategorydata) > 0) {
                    if ($jcategorydata['jet_cate_id'] != '' &&
                        $jcategorydata['magento_cat_id'] != $MagentoCategoryId
                    ) {
                        Mage::getSingleton('adminhtml/session')->addError($jcategorydata['jet_cate_id'] . " this Jet Catgeory Id already mapped or imported into another Magento Category id:" . $jcategorydata['magento_cat_id'] . ".Please choose another Jet Categroy id or 'Delete' mapped Category ");
                        $update = false;
                        try {
                            $categorySingleton = Mage::getSingleton('catalog/category');
                            $categorySingleton->setId($observer->getEntityId());
                            $categorySingleton->setJetCategoryId('');
                        } catch (Exception $e) {}
                    }
                }
                elseif(empty($jetattributemap) && (!empty($jet_category_id))){
                    $update=false;
                    $jetUrl = Mage::helper('adminhtml')->getUrl('adminhtml/adminhtml_jetattrlist/categorylist');
                    Mage::getSingleton('adminhtml/session')->addError("Jet Catgeory Id : " . $jet_category_id . " doesn't exist . <a href='$jetUrl' target='_blank'>click here</a> for existing jet categories ");
                }

                if ($update) {
                    $jetCategoryCollection = Mage::getModel('jet/jetcategory')->getCollection()
                        ->addFieldToFilter('magento_cat_id', $MagentoCategoryId);
                    $table_catlist = $resource->getTableName('jet/catlist');
                    $query = 'SELECT `attribute_ids` FROM  ' . $table_catlist . ' where csv_cat_id = "' . $jet_category_id . '"';
                    $attr_data = $readConnection->fetchRow($query);

                    try {
                        if ((count($jetCategoryCollection) > 0)) {
                            foreach ($jetCategoryCollection as $collection) {
                                $Jetcategory = Mage::getModel('jet/jetcategory')->load($collection->getId());
                                if(empty($jet_category_id)){$Jetcategory->setJetCateId('');}
                                else{$Jetcategory->setJetCateId($jet_category_id);}
								if ($attr_data['attribute_ids'] != '' && $attr_data['attribute_ids'] != null) {
                                    $Jetcategory->setJetAttributes($attr_data['attribute_ids']);
                                }
                                $Jetcategory->save();
                            }
                        } else{
                            $Jetcategory = Mage::getModel('jet/jetcategory');
                            $Jetcategory->setJetCateId($jet_category_id);
                            $Jetcategory->setMagentoCatId($MagentoCategoryId);
                            $Jetcategory->setIsCsvCategory(0);
                            if ($attr_data['attribute_ids'] != '' && $attr_data['attribute_ids'] != null) {
                                $Jetcategory->setJetAttributes($attr_data['attribute_ids']);
                            }
                            $Jetcategory->save();
                        }
                    } catch (Exception $e) {
                    }
                }
            }

            elseif(strlen($jet_category_id) == 0){
                $id = Mage::getModel('jet/jetcategory')->getCollection()->addFieldToFilter('magento_cat_id', $MagentoCategoryId)->getFirstItem()->getData('id');

                if(($id != '') && ($id != null)){
                    $row = Mage::getModel('jet/jetcategory')->load($id);
                    $row->delete();
                    Mage::getSingleton('adminhtml/session')->addSuccess("Current Magento category is successfully unlinked from jet .");
                }


            }
            elseif($jet_category_id<0){
                Mage::getSingleton('adminhtml/session')->addError("Entered Jet Id is invalid . <a href='../adminhtml_jetattrlist/categorylist' target='_blank'>click here</a> for existing jet categories ");
            }
        }

    }

    /*
     * observer for clearing jet token after saving new Jet Config details
     */

    public function clearToken(Varien_Event_Observer $observer)
    {
        $data = $observer->getEvent()->getData();
        $setup = new Mage_Core_Model_Resource_Setup();
        $setup->deleteConfigData('jetcom/token');

    }

    public function jetProductDelete($observer)
    {
        $product = "";
        $product_available = true;
        $checkStatus= Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_config_product_option');
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == 'simple') {
            $sku = "";
            $sku = $product->getSku();
            $data = "";
            $response = '';
            $data = Mage::helper('jet')->CGetRequest('/merchant-skus/'.rawurlencode($sku));
            $response = json_decode($data);
            if (!$data) {
                $product_available = false;
            }
            if ($product_available) {
                $data = "";
                $response = '';
                $arr = array();
                $arr['is_archived'] = true;
                $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $sku . '/status/archive', json_encode($arr));
                $response = json_decode($data);
            }
        }
        if ($product->isConfigurable()) {
                $simple_collection = Mage::getModel('catalog/product_type_configurable')->setProduct($product)
                    ->getUsedProductCollection()
                    ->addAttributeToSelect('sku')
                    ->addFilterByRequiredOptions();
                foreach ($simple_collection  as $_item) {
                    if ($_item->getData('type_id') == 'simple') {
                        $arr = array();
                        $arr['is_archived'] = true;
                        if (!$checkStatus) {
                            $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $_item->getSku() . '/status/archive', json_encode($arr));
                            $response = json_decode($data);
                            break;
                        } else {
                            $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $_item->getSku() . '/status/archive', json_encode($arr));
                            $response = json_decode($data);
                        }
                    }
                }
        }
       
    }

    public function jetProductEdit($observer)
    {
        $auto_sync_enable = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_product_auto_sync');
        if($auto_sync_enable == 1)
        {
            $product = $observer->getProduct();

        if ($product->getTypeId() == 'simple') {
            $is_update_price = 1;
            $is_update_qty = 1;
            $is_update_all = 1;
            $is_update_image = 1;
            $is_update_price = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_product_price');
            $is_update_qty = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_product_inventory');
            $is_update_image = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_product_images');
            $is_update_all = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_product_details');
            $product_available = true;

            $fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');

            $id = $product->getId();
            $sku = $product->getSku();
            $response = '';
            $data = Mage::helper('jet')->CGetRequest('/merchant-skus/' . $sku);
            $response = json_decode($data);
            if (!$data) {
                $product_available = false;
            }
            $is_archived = false;
            if ($response) {
                if ($response->is_archived) {
                    $is_archived = true;
                }
            }
            if ($product_available) {
                $product_qty = 0;
                $is_in_stock = true;
                $stock = "";
                $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                if ($stock && $stock->getIsInStock() != '1') {
                    $is_in_stock = false;
                }
                if ($stock && $stock->getQty() > 0) {
                    $product_qty = $stock->getQty();
                }else{
                    $product_qty = 0;
                }

                $status = "";
                $price = Mage::helper('jet/jet')->getJetPrice($product);

                $status = $product->getStatus();
                if (!$status) {
                    $data = "";
                    $response = '';
                    $arr = array();
                    $arr['is_archived'] = true;
                    $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $sku . '/status/archive', json_encode($arr));
                    $response = json_decode($data);
                } else {
                    $data = "";
                    $response = '';
                    $arr = array();
                    $arr['is_archived'] = false;
                    $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $sku . '/status/archive', json_encode($arr));
                    $response = json_decode($data);
                }
                if ($status) {
                    /*-----all data update code starts----*/
                    $update_image = true;
                    if ($is_update_all == "1") {
                        $alldataupdate = 0;
                        $alldataupdate = Mage::helper('jet')->createProductOnJet($product);
                        if ($alldataupdate['merchantsku'][$product->getSku()]) {
                            $data = "";
                            $response = '';
                            $updatedata = '';
                            $updatedata = $alldataupdate['merchantsku'][$product->getSku()];
                            $finalskujson = "";
                            $finalskujson = json_encode($updatedata);
                            $newJsondata = Mage::helper('jet')->ConvertNodeInt($finalskujson);
                            $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $sku, $newJsondata);
                            $response = json_decode($data);
                            if ($data == "") {}
                            $update_image = false;
                        }
                    }
                    /*-----all data update code ends----*/
                    /*-----price update code starts----*/
                    if ($is_update_price == "1") {
                        $data_var = array();
                        $fulfillment_arr = array();
                        $data = ""; $response = '';

                        $fulfillment_arr[0]['fulfillment_node_id'] = $fullfillmentnodeid;
                        $fulfillment_arr[0]['fulfillment_node_price'] = $price;
                        $data_var['price'] = (float)$price;
                        $data_var['fulfillment_nodes'] = $fulfillment_arr;
                        $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $sku . '/price', json_encode($data_var));
                        $response = json_decode($data);
                        if ($data == "") {}
                    }

                    /*-----price update code ends----*/
                    /*-----inventory update code starts----*/
                    if ($is_update_qty == "1") {
                        $data_var = array();
                        $fulfillment_arr = array();
                        $data = "";
                        $response = '';
                        $fulfillment_arr[0]['fulfillment_node_id'] = $fullfillmentnodeid;
                        $fulfillment_arr[0]['quantity'] = (int)$product_qty;
                        if (!$is_in_stock) {
                            $fulfillment_arr[0]['quantity'] = 0;
                        }
                        $data_var['fulfillment_nodes'] = $fulfillment_arr;
                        $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $sku . '/inventory', json_encode($data_var));
                        $response = json_decode($data);
                        if ($data == "") {}
                    }

                    /*-----inventory update code ends----*/
                    /*-----images update code starts----*/

                    if ($update_image && $is_update_image == "1") {
                        $no_image = false;
                        if ($product->getImage() == "no_selection") {
                            $no_image = true;
                        }
                        $main_image_url = "";
                        $alt_images = array();
                        if (!$no_image) {
                            $main_image_url = $product->getImageUrl();
                        }
                        if ($main_image_url != "") {
                            $alt_images["main_image_url"] = $main_image_url;
                        }
                        $all_images = '';
                        $all_images = $product->getMediaGalleryImages();
                        $jet_image_slot = 1;
                        $slot = 1;
                        foreach ($all_images as $key => $alternat_image) {
                            if ($alternat_image->getUrl() != '') {
                                if (count($alt_images) == 0) {
                                    $alt_images["main_image_url"] = $alternat_image->getUrl();
                                }
                                $alt_images['alternate_images'][] = array('image_slot_id' => $slot,
                                    'image_url' => $alternat_image->getUrl()
                                );
                                $slot++;
                                if ($jet_image_slot > 7) {
                                    break;
                                }
                                $jet_image_slot++;
                            }
                        }
                        $data = "";
                        $response = "";
                        $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $sku . '/image', json_encode($alt_images));
                        $response = json_decode($data);
                        if ($data == "") {}
                    }

                    /*-----images update code ends----*/
                }
            }
        }/*
        else if($product->getTypeId()=='configurable'){

            $is_update_all = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_product_details');
            $fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
            $is_update_qty = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_product_inventory');

            $id = $product->getId();
            $sku = $product->getSku();

            $data = Mage::helper('jet')->CGetRequest('/merchant-skus/' . rawurlencode($sku));

            $response = json_decode($data);
            if (!$data) {
                $product_available = false;
            }
            $is_archived = false;
            if ($response) {
                if ($response->is_archived) {
                    $is_archived = true;
                }
            }
            if ($product_available) {
                $product_qty = 0;
                $is_in_stock = true;
                $stock = "";
                $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                if ($stock && $stock->getIsInStock() != '1') {
                    $is_in_stock = false;
                }
                if ($stock && $stock->getQty() > 0) {
                    $product_qty = $stock->getQty();
                }

                $price = "";
                $status = "";
                $price = $product->getPrice();

                $status = $product->getStatus();
                if (!$status) {
                    $data = "";
                    $response = '';
                    $arr = array();
                    $arr['is_archived'] = true;
                    $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $sku . '/status/archive', json_encode($arr));
                    $response = json_decode($data);
                }else {
                    $data = "";
                    $response = '';
                    $arr = array();
                    $arr['is_archived'] = false;
                    $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $sku . '/status/archive', json_encode($arr));
                    $response = json_decode($data);
                }
                if ($status && $response) {
                    if ($is_update_qty == "1") {
                        $data_var = array();
                        $fulfillment_arr = array();
                        $data = "";
                        $response = '';
                        $fulfillment_arr[0]['fulfillment_node_id'] = $fullfillmentnodeid;
                        $fulfillment_arr[0]['quantity'] = (int)$product_qty;
                        if (!$is_in_stock) {
                            $fulfillment_arr[0]['quantity'] = 0;
                        }
                        $data_var['fulfillment_nodes'] = $fulfillment_arr;
                        $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $sku . '/inventory', json_encode($data_var));
                        $response = json_decode($data);
                        if ($data == "") {}
                    }
                }
            }

        }*/
        }
        //return , shipping exception save code start

        $dataRequest=Mage::app()->getRequest()->getParams();

        $go_redirect =false;
        if($dataRequest)
        {
            try
            {

                $fullfillmentnodeid=Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
                 $product = $observer->getProduct();

                $sku=$product->getSku();
               
                if(Mage::app()->getRequest()->getPost('shipping_override')){
                        $chargeamount=Mage::app()->getRequest()->getPost('shipping_charge');
                        $exceptiontype=Mage::app()->getRequest()->getPost('shipping_excep');
                        $shippinglevel=Mage::app()->getRequest()->getPost('shipping_carrier');
                        $shippingmethod=Mage::app()->getRequest()->getPost('shipping_method');
                        $overridetype=Mage::app()->getRequest()->getPost('shipping_override');
                        if($shippinglevel){
                            $shipping=array();
                            $shipping['fulfillment_nodes'][]=array('fulfillment_node_id'=>"$fullfillmentnodeid",
                                                        'shipping_exceptions'=>array(
                                                            array('service_level'=>$shippinglevel,
                                                                  'override_type'=>$overridetype,
                                                                  'shipping_charge_amount'=>(float)$chargeamount,           
                                                                  'shipping_exception_type'=>$exceptiontype)));
                        }
                        else{
                            $shipping=array();
                            $shipping['fulfillment_nodes'][]=array('fulfillment_node_id'=>"$fullfillmentnodeid",
                                                            'shipping_exceptions'=>array(
                                                                array('shipping_method'=>trim($shippingmethod),
                                                                    'override_type'=>$overridetype,
                                                                    'shipping_charge_amount'=>(float)$chargeamount,
                                                                    'shipping_exception_type'=>$exceptiontype)));
                            
                            }
                        
                        $data=Mage::helper('jet')->CPutRequest('/merchant-skus/'.rawurlencode($sku).'/shippingexception',json_encode($shipping));
                       
                        
                        if($data==''){
                            $shippingObj=Mage::getModel('jet/jetshippingexcep');
                            $collectionload=$shippingObj->getCollection()->addFieldToFilter('sku',$sku);
                            foreach ($collectionload as $value) {
                                $id=$value['id'];
                                break;
                            }

                            if($collectionload->count()>0){
                                $shippingObj->load($id)
                                            ->setData('sku',$sku)
                                            ->setData('shipping_charge',$chargeamount)
                                            ->setData('shipping_excep',$exceptiontype)
                                            ->setData('shipping_carrier',$shippinglevel)
                                            ->setData('shipping_method',$shippingmethod)
                                            ->setData('shipping_override',$overridetype);
                            }
                            else{       
                                $shippingObj->setData('sku',$sku)
                                            ->setData('shipping_charge',$chargeamount)
                                            ->setData('shipping_excep',$exceptiontype)
                                            ->setData('shipping_carrier',$shippinglevel)
                                            ->setData('shipping_method',$shippingmethod)
                                            ->setData('shipping_override',$overridetype);
                            } 

                            $shippingObj->save();
                            Mage::getSingleton('adminhtml/session')
                                          ->addSuccess('Shipping Exception has been saved successfully');
                            $go_redirect =true;           
                        }else{
                            $error = json_decode($data, true);
                            $msg = '';
                            if(isset($error['errors']) && !empty($error['errors'])){
                                $err_count = count($error['errors']);
                                if($err_count>0){
                                    for($i=0; $i<=$err_count; $i++){
                                        $msg = $msg.$error['errors'][$i].'</br>';
                                    }
                                    Mage::getSingleton('adminhtml/session')
                                          ->addError($msg);
                                }else{
                                    Mage::getSingleton('adminhtml/session')
                                          ->addError("There is an error in shipping exception processing"); 
                                }
                            }else{
                                Mage::getSingleton('adminhtml/session')
                                          ->addError("There is an error in shipping exception processing"); 
                            }
                            $go_redirect =false;    
                        }
                }

                if(Mage::app()->getRequest()->getPost('time_to_return')){
                            $return_arr=array();
                            $time_to_return='';
                            $time_to_return=Mage::app()->getRequest()->getPost('time_to_return');
                            if($time_to_return!=""  && trim($time_to_return)!=""){
                                $return_arr['time_to_return']=(int)$time_to_return;
                            }else{
                                        Mage::getSingleton('adminhtml/session')
                                            ->addError('Please enter correct Time to return.');
                                         //$this->_redirect('*/*/productDetails',array('id' => $this->getRequest()->getParam('id')));
                                        return;
                            }
                            $location_ids=array();
                            $locations=array();
                            if(Mage::app()->getRequest()->getPost('locations')){
                                        $locations=Mage::app()->getRequest()->getPost('locations');
                                        if(count($locations['value'])>0){
                                                    for($i=0;$i < count($locations['value']);$i++){
                                                            if($locations['delete'][$i]==""){
                                                                    if($locations['value'][$i]!=""  && trim($locations['value'][$i])!=""){
                                                                            $location_ids[]=$locations['value'][$i];
                                                                    }
                                                                    
                                                            }
                                                    }
                                        }
                            }
                            if(count($location_ids)>0){
                                    $return_arr['return_location_ids']=$location_ids;
                            }

                            $ship_methods=array();
                            $ship=array();
                            if(Mage::app()->getRequest()->getPost('ship_methods')){
                                        $ship=Mage::app()->getRequest()->getPost('ship_methods');
                                        if(count($ship['value'])>0){
                                                    for($i=0;$i < count($ship['value']);$i++){
                                                            if($ship['delete'][$i]==""){
                                                                    if($ship['value'][$i]!="" && trim($ship['value'][$i])!=""){
                                                                            $ship_methods[]=trim($ship['value'][$i]);
                                                                    }
                                                            }
                                                    }
                                        }
                            }
                            if(count($ship_methods)>0){
                                    $return_arr['return_shipping_methods']=$ship_methods;
                            }
                            if(count($location_ids)<=0){
                                         Mage::getSingleton('adminhtml/session')->addError('Please enter Return Location Ids.');
                                         //$this->_redirect('*/*/productDetails',array('id' => $this->getRequest()->getParam('id')));
                                        return;
                            }
                            if(count($ship_methods)<=0){
                                         Mage::getSingleton('adminhtml/session')->addError('Please enter Return Shipping Methods.');
                                        //$this->_redirect('*/*/productDetails',array('id' => $this->getRequest()->getParam('id')));
                                        return;
                            }
                            if(count($return_arr)>0){
                                    $url ='/merchant-skus/'.rawurlencode($sku).'/returnsexception';
                                    $data = Mage::helper('jet')->CPutRequest($url,json_encode($return_arr));
                                    
                                    if(!empty($data) || $data!=''){
                                                $data1="";
                                                $data1=json_decode($data);
                                                $error_str1='Return Exception Failed.<br/>';
                                                $error_str='';
                                                $j=0;

                                                foreach($data1->errors as $error){
                                                            $string="";
                                                            $title="";
                                                            $time=false;
                                                            if(strpos($error, 'return_shipping_methods')){
                                                                    $title="Error in Return Shipping Methods :";
                                                            }
                                                            if(strpos($error, 'time_to_return')){
                                                                    $title="Error in Time to Return :";
                                                                    $time=true;
                                                            }
                                                            if(preg_match('/location/',$error)){
                                                                    $title="Error in Return Location Ids :";

                                                            }
                                                            if(strpos($error, 'Path:')){
                                                                    $string=substr($error,0,strpos($error, 'Path:'));
                                                            }
                                                            if($time && strpos($error, '30L')){
                                                                    $string="Value should be 30 or fewer days.";
                                                            }
                                                            if($j>0){
                                                                if($string!=""){
                                                                        $error_str=$error_str.'<br/>'.$title.$string;
                                                                }else{
                                                                        $error_str=$error_str.'<br/>'.$title.$error;
                                                                }
                                                                        
                                                            }else{
                                                                if($string!=""){
                                                                    $error_str=$title.$string;
                                                                }else{
                                                                    $error_str=$title.$error;
                                                                }
                                                                
                                                            }
                                                    $j++;
                                                }
                                                if($error_str){
                                                    Mage::getSingleton('adminhtml/session')->addError($error_str1.$error_str);
                                                }
                                                
                                                $go_redirect =false;    
                                    }else{
                                        Mage::getSingleton('adminhtml/session')->addSuccess('Return Exception has been saved successfully');
                                        
                                        $go_redirect =true; 
                                    }
                                    
                            }

                }
            }catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->settestData($this->getRequest()->getPost());
                $go_redirect =false;
            }
        }
    
        if($go_redirect){
            //$this->_redirect('*/*/*');
        }else{
            //$this->_redirect('*/*/*',array('id' => $this->getRequest()->getParam('id')));
        }
        //return , shipping exception save code end
        
    }
    public function jetProductSaveBefore($observer)
    {
       $auto_sync_enable = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_product_auto_sync');
        if($auto_sync_enable == 1)
        {
            $product = $observer->getProduct();
            if ($product->getTypeId() == 'simple')
             {
                $product_available = true;
                $id = $product->getId();
                $current_sku = $product->getSku();
                $previous_sku = Mage::getModel('catalog/product')->load($id)->getSku();
                $response = '';
                $data = Mage::helper('jet')->CGetRequest('/merchant-skus/' . $previous_sku);
                $response = json_decode($data);
                if (!$data) {
                    $product_available = false;
                }
                $is_archived = false;
                if ($response) {
                    if ($response->is_archived) {
                        $is_archived = true;
                    }
                }
                if ($product_available) 
                {
                     if($current_sku!= $previous_sku)
                        {
                        $data = "";
                        $response = '';
                        $arr = array();
                        $arr['is_archived'] = true;
                        $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $previous_sku . '/status/archive', json_encode($arr));
                        $response = json_decode($data);
                        }
                }
            }
        }
    } 

    public function addJetAttributeTab($observer)
    {
        $tabs = $observer->getEvent()->getTabs();
        $tabs->addTab('jet_attributes', array(
            'label' => Mage::helper('catalog')->__('Jet Attributes'),
            'content' => $tabs->getLayout()->createBlock(
                'jet/adminhtml_category_tab_attribute',
                'category.attribute.grid'
            )->toHtml(),
        ));
        $tabs->addTab('jet_categorymapping', array(
            'label' => Mage::helper('catalog')->__('Jet Category Mapping '),
            'content' => $tabs->getLayout()->createBlock(
                'jet/adminhtml_category_mapping'
            )->toHtml(),
        ));
    }

    public function saveJetOrder($jetId){
        $saveJetorder=Mage::getModel('jet/jetorder')->load($jetId);
        $saveJetorder->setData('status','cancelled');
        $saveJetorder->save();
    }


	public function updateInvcron(){
		Mage::Helper('jet/jet')->createuploadDir();

		$fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');

		$resource = Mage::getSingleton('core/resource');
        $table = $resource->getTableName('jet/jetcron');


		$result=Mage::getModel('jet/jetcategory') ->getCollection()->addFieldToSelect('magento_cat_id');
		$resultdata=array();
		foreach($result as $val){
			$resultdata[]=$val['magento_cat_id'];
		}

		$collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku');

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')){
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
		}

		$collectionData = $collection;
		$collectionProd = Mage::getModel('catalog/product')->getCollection();
		$collectionProd->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id =entity_id', null, 'left');
		$collectionProd->addAttributeToSelect('*')
        ->addAttributeToFilter('category_id', array('in' => $resultdata));

		$ids = $collectionProd->getAllIds();
		$ids = array_unique($ids);

		$collection->addFieldToFilter('entity_id', array('in'=>$ids))
					->addAttributeToFilter('type_id', array('in' => array('simple','configurable')));
		$total_size = $collection->getSize();

		if($total_size!=0){
			$goupload = true;
		}


		$data = array();
		if($goupload){

			$data = $collection->getData();

			$batch_data = (array_chunk($data, 5000));

			foreach($batch_data as $data){

				$Inventory  =array();
				foreach($data as $product){

					if($product['type_id']=='configurable'){
						$ids=Mage::getResourceSingleton('catalog/product_type_configurable')->getChildrenIds($product['entity_id']);
						if(isset($ids[0]) && count ($ids[0])>0){
							$child_array = $ids[0];
							$_subproducts = Mage::getModel('catalog/product')->getCollection()
											->addAttributeToSelect('*');

							 if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')){
								$_subproducts->joinField('qty',
									'cataloginventory/stock_item',
									'qty',
									'product_id=entity_id',
									'{{table}}.stock_id=1',
									'left');
							}

							$child_data =$_subproducts->addFieldToFilter('entity_id', array('in'=>$child_array))
										 ->getData();

							if(isset($child_data[0]) && count($child_data[0])>0){
								foreach($child_data as $child){
									$temp_inv = array();
									$node =array();
									$node['fulfillment_node_id']="$fullfillmentnodeid";
									$node['quantity']=(int)$child['qty'];
									$temp_inv[$child['sku']]['fulfillment_nodes'][]=$node;

									$Inventory = Mage::Helper('jet/jet')->Jarray_merge($temp_inv,$Inventory);
								}
							}

						}

					}else{
						$temp_inv = array();
						$node =array();
						$node['fulfillment_node_id']="$fullfillmentnodeid";
						$node['quantity']=(int)$product['qty'];
						$temp_inv[$product['sku']]['fulfillment_nodes'][]=$node;

						$Inventory = Mage::Helper('jet/jet')->Jarray_merge($temp_inv,$Inventory);
					}
				}

				if(count($Inventory)>0){
					$tokenresponse = Mage::helper('jet')->CGetRequest('/files/uploadToken');
					$tokendata = json_decode($tokenresponse);

					$inventoryPath = Mage::helper('jet')->createJsonFile( "Inventory", $Inventory);
					$sku_file_name =  end(explode(DS, $inventoryPath));
					$inventoryPath=$inventoryPath.'.gz';

					$reponse = Mage::helper('jet')->uploadFile($inventoryPath,$tokendata->url);
					$postFields='{"url":"'.$tokendata->url.'","file_type":"Inventory","file_name":"'.$sku_file_name.'"}';

					$responseinventry = Mage::helper('jet')->CPostRequest('/files/uploaded',$postFields);
					//$invetrydata = json_decode($responseinventry);
					//if($invetrydata->status == 'Acknowledged'){echo $invetrydata->status;}
					$Inventory = array();
				}

			}

		}

	}

	/*
	public function updateInvcronBatch(){
		Mage::Helper('jet/jet')->createuploadDir();
		$fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');

		$resource = Mage::getSingleton('core/resource');
        $table = $resource->getTableName('jet/jetcron');


		$result=Mage::getModel('jet/jetcategory') ->getCollection()->addFieldToSelect('magento_cat_id');
		$resultdata=array();
		foreach($result as $val){
			$resultdata[]=$val['magento_cat_id'];
		}

		$collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku');

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')){
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
		}

		$collectionData = $collection;
		$collectionProd = Mage::getModel('catalog/product')->getCollection();
		$collectionProd->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id =entity_id', null, 'left');
		$collectionProd->addAttributeToSelect('*')
        ->addAttributeToFilter('category_id', array('in' => $resultdata));

		$ids = $collectionProd->getAllIds();
		$ids = array_unique($ids);

		$collection->addFieldToFilter('entity_id', array('in'=>$ids))
					->addAttributeToFilter('type_id', array('in' => array('simple','configurable')));
		$total_size = $collection->getSize();


		$readconnection = $resource->getConnection('core_read');
		$writeConnection = $resource->getConnection('core_write');

		$query = 'SELECT * FROM '.$table .' WHERE `event`= "inventory"';
		$row = $readconnection->fetchRow($query);

		$batch_start = 0;
		$batch_end = 1000;
		$goupload= false;

		if($row==false && $row['event']==''){
			$query = 'INSERT INTO ' . $table . '(event,batch_start,timestamp) VALUES ("inventory",0,'.time().')';
			$writeConnection->query($query);
			$batch_start = 0;
		}else{
			$batch_start = $row['batch_start']+$batch_end;
			$query = 'UPDATE '.$table.' SET batch_start='.$batch_start.', timestamp= '.time().' WHERE event="inventory"';
			$writeConnection->query($query);

			$diff =  round((time() - $row['timestamp'])/3600,1);

			if(($total_size <= $batch_start) && $diff > 2){
				$batch_start = 0;
				$query = 'UPDATE '.$table.' SET batch_start='.$batch_start.', timestamp= '.time().' WHERE event="inventory"';
				$writeConnection->query($query);
			}

		}

		if($total_size!=0 && $total_size > $batch_start){
			$goupload = true;
		}


		$data = array();
		if($goupload){
			$collection->setPage($batch_start ,$batch_end);
			$data = $collection->getData();
			$Inventory  =array();
			foreach($data as $product){

				if($product['type_id']=='configurable'){
					$ids=Mage::getResourceSingleton('catalog/product_type_configurable')->getChildrenIds($product['entity_id']);
					if(isset($ids[0]) && count ($ids[0])>0){
						$child_array = $ids[0];
						$_subproducts = Mage::getModel('catalog/product')->getCollection()
										->addAttributeToSelect('*');

						 if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')){
							$_subproducts->joinField('qty',
								'cataloginventory/stock_item',
								'qty',
								'product_id=entity_id',
								'{{table}}.stock_id=1',
								'left');
						}

						$child_data =$_subproducts->addFieldToFilter('entity_id', array('in'=>$child_array))
									 ->getData();

						if(isset($child_data[0]) && count($child_data[0])>0){
							foreach($child_data as $child){
								$temp_inv = array();
								$node =array();
								$node['fulfillment_node_id']="$fullfillmentnodeid";
								$node['quantity']=(int)$child['qty'];
								$temp_inv[$child['sku']]['fulfillment_nodes'][]=$node;

								$Inventory = Mage::Helper('jet/jet')->Jarray_merge($temp_inv,$Inventory);
							}
						}

					}

				}else{
					$temp_inv = array();
					$node =array();
					$node['fulfillment_node_id']="$fullfillmentnodeid";
					$node['quantity']=(int)$product['qty'];
					$temp_inv[$product['sku']]['fulfillment_nodes'][]=$node;

					$Inventory = Mage::Helper('jet/jet')->Jarray_merge($temp_inv,$Inventory);
				}
			}

			if(count($Inventory)>0){
				$tokenresponse = Mage::helper('jet')->CGetRequest('/files/uploadToken');
                $tokendata = json_decode($tokenresponse);

                $inventoryPath = Mage::helper('jet')->createJsonFile( "Inventory", $Inventory);
                $sku_file_name =  end(explode(DS, $inventoryPath));
                $inventoryPath=$inventoryPath.'.gz';

                $reponse = Mage::helper('jet')->uploadFile($inventoryPath,$tokendata->url);
				$postFields='{"url":"'.$tokendata->url.'","file_type":"Inventory","file_name":"'.$sku_file_name.'"}';

                $responseinventry = Mage::helper('jet')->CPostRequest('/files/uploaded',$postFields);
                //$invetrydata = json_decode($responseinventry);
				//if($invetrydata->status == 'Acknowledged'){echo $invetrydata->status;}
				$Inventory = array();
			}

		}
	}

	*/

}
