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

class Ced_Jet_Adminhtml_JetproductController extends Mage_Adminhtml_Controller_Action{

	public function clearallAction(){

		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$query = "DELETE FROM ". $resource->getTableName('jet/fileinfo') ." WHERE status = 'Processed with errors' OR status = 'Processed successfully'";

		$writeConnection->query($query);

		$query = 'TRUNCATE TABLE '.$resource->getTableName('jet/errorfile').'';
		$writeConnection->query($query);

		Mage::getSingleton('adminhtml/session')->addSuccess('Rejected batch File Log cleared.');

		$this->_redirect('*/*/rejected');
	}

	public function resubmitAction(){

		$jfile_id = $this->getRequest()->getPost('jetinfofile_id');
		$error_fileid = $this->getRequest()->getPost('id');

		$loadfile = Mage::getModel('jet/fileinfo')->load($jfile_id);
		$products = $loadfile->magento_batch_info;

		$model_error = Mage::getModel('jet/errorfile')->load($error_fileid);
		if($model_error->getId()){
			$model_error->setStatus('Resubmit Requested');
			$model_error->save();
			Mage::getSingleton('adminhtml/session')->addSuccess('Inventory File submission successfully done.');
		}
		$this->_redirect("*/*/massimport",array("product"=>$products));
	}

	public function newAction(){
		Mage::getModel('jet/observer')->updateProduct();
		$this->_redirect('*/*/rejected');
	}

	public function massarchivedAction(){

		$ids = $this->getRequest()->getPost('product');

		if(!is_array($ids) && $ids!=''){
		   $ids=array($ids);
		}

		if(!is_array($ids))
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jet')->__('Please select product id(es).'));
		}
		else
		{
			try
			{
				$productdata = Mage::getModel('catalog/product');
				$cArchived=0;$cClosed=0;

				foreach ($ids as $id) {
					$productLoad=$productdata->load($id);

					 if($productLoad->isConfigurable()){

						$simple_collection = Mage::getModel('catalog/product_type_configurable')->setProduct($productLoad)
							->getUsedProductCollection()
							->addAttributeToSelect('sku');
						$i = 0;
						$first_child_code = false;


						foreach ($simple_collection  as $_item)
						{
							$childLoad= Mage::getModel('catalog/product')->load($_item['entity_id']);
							$data['is_archived']=true;
							$result = Mage::helper('jet')->CGetRequest('/merchant-skus/'.rawurlencode($_item['sku']));
							$response=json_decode($result, true);

							if($response['status'] == 'Archived' || $response['is_archived']==true){
								if($i==0)
									$first_child_code =false;
								$cArchived++;

							}else{
								if($i==0)
									$first_child_code =true;

								$cClosed++;
								$data1=Mage::helper('jet')->CPutRequest('/merchant-skus/'.$_item['sku'].'/status/archive',json_encode(array('is_archived'=>true)));
								$childLoad->setJetProductStatus('archived')->save();
							}
							$i++;
						}

						if($first_child_code)
							$productLoad->setJetProductStatus('archived')->save();

					}

					$sku=$productLoad->getSku();
					$result = Mage::helper('jet')->CGetRequest('/merchant-skus/'.rawurlencode($sku));
					$response=json_decode($result, true);

					if($response['status'] == 'Archived' || $response['is_archived']==true){
						$cArchived++;
					}else {
						$cClosed++;

						$data2=Mage::helper('jet')->CPutRequest('/merchant-skus/'.rawurlencode($sku).'/status/archive',json_encode(array('is_archived'=>true)));
						$productLoad->setJetProductStatus('archived')->save();

					}

				}

				if($cClosed>0 || $cArchived>0)
				{
					if($cClosed>0){
						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('jet')->__($cClosed.' product(s) is archived successfully'));
					}
					if($cArchived>0){
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jet')->__($cArchived.' product(s) is already archived'));
					}
				}else
				{
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jet')->__('Product(s) Archived Request Failed.'));
				}

			}catch (Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}

		$this->_redirect('adminhtml/adminhtml_jetrequest/uploadproduct');
	}

	public function Unarchprocess($productLoad){

		$fullfillmentnodeid=Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');

		$sku=$productLoad->getSku();
		$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productLoad);

		$node1 = array();
		$inventory =array();
		$qty= ((int)$stock->getQty())>0 ? (int)$stock->getQty() : 0;

		$node1['fulfillment_node_id']="$fullfillmentnodeid";
		$node1['quantity']=$qty;
		$inventory['fulfillment_nodes'][]=$node1;


		$data1=Mage::helper('jet')->CPutRequest('/merchant-skus/'.$sku.'/status/archive',json_encode(array('is_archived'=>false)));

		$inventry=Mage::helper('jet')->CPutRequest('/merchant-skus/'.$sku.'/inventory',json_encode($inventory));

		$result=Mage::helper('jet')->CGetRequest('/merchant-skus/'.rawurlencode($sku).'');

		$response=json_decode($result);

		$code = false;
		if(($response->status!='') || ( $response->is_archived ==false)){
			if($response->status=='Available for Purchase'){
				$code = 'available_for_purchase';
			}else if($response->status=='Archived'){
				$code = 'archived';
			} else if($response->status=='Missing Listing Data'){
				$code = 'missing_listing_data';
			}else if($response->status=='Under Jet Review'){
				$code = 'under_jet_review';
			}else if($response->status=='Excluded'){
				$code = 'excluded';
			}else if($response->status=='Unauthorized'){
				$code = 'unauthorized';
			}
		}
		return $code;
	}


	public function unarchivedAction(){
		$ids = $this->getRequest()->getParam('product');
		if(!is_array($ids) && $ids!=''){
		   $ids=array($ids);
		}
		if(!is_array($ids))
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jet')->__('Please select product id(es).'));
		}
		else
		{
			try
			{
				$cunArchived=0;

				foreach ($ids as $id) {
					$code = false;

					$productLoad=Mage::getModel('catalog/product')->load($id);

					 if($productLoad->isConfigurable()){

						$simple_collection = Mage::getModel('catalog/product_type_configurable')->setProduct($productLoad)
							->getUsedProductCollection()
							->addAttributeToSelect('sku');
						$first_child_code = false;
						$i = 0;
						foreach ($simple_collection  as $_item)
						{
							$childLoad= Mage::getModel('catalog/product')->load($_item['entity_id']);


							$code = $this->Unarchprocess($childLoad);
							if($i == 0){
								$first_child_code = $code;
							}
							if($code != false){
								$cunArchived++;
								$childLoad->setJetProductStatus($code)->save();
							}
							$i++;
						}
						if($first_child_code != false){
							$productLoad->setJetProductStatus($first_child_code)->save();
						}

					}else{
						$code = $this->Unarchprocess($productLoad);
						if($code != false){
							$cunArchived++;
							$productLoad->setJetProductStatus($code)->save();
						}
					}

				}
				if($cunArchived>0)
				{
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('jet')->__($cunArchived.' product(s) is unarchived successfully'));
				}else
				{
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jet')->__('Product(s) Unarchive Request Failed.'));
				}

			}catch (Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('adminhtml/adminhtml_jetrequest/uploadproduct');
	}

	public function massimportAction(){
		

		Mage::Helper('jet/jet')->createuploadDir();

		$fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
		if(empty($fullfillmentnodeid) || $fullfillmentnodeid=='' || $fullfillmentnodeid== null){
			Mage::getSingleton('core/session')->addError('Enter fullfillmentnode id form Jet Configuration');
			$this->_redirect('adminhtml/adminhtml_jetrequest/uploadproduct');
		}

		$result=array();
 		$node=array();
		$inventory=array();
		$price=array();
		$relationship = array();
		$data = $this->getRequest()->getParam('product');

		if(is_string($data)){
			$data = explode("," ,$data);
		}
		$productids=(array_chunk($data,50));

		if(count($productids)==0){
			Mage::getSingleton('core/session')->addError('No Product selected to upload.');
			$this->_redirect('adminhtml/adminhtml_jetrequest/uploadproduct');
		}

		foreach($productids as  $val){
				$commaseperatedids = implode(",", $val);
					foreach($val as $pid){
					$childPrice = Mage::helper('jet/jet')->getChildPrice($pid);

					if($resultData = Mage::helper('jet')->createProductOnJet( $pid , $childPrice , $parent_image)){					
						$result = Mage::helper('jet/jet')->Jarray_merge($result, $resultData['merchantsku']);
						$price =  Mage::helper('jet/jet')->Jarray_merge($price, $resultData['price']);
						$inventory =  Mage::helper('jet/jet')->Jarray_merge($inventory, $resultData['inventory']);
						
						if(isset($resultData['relationship']))
							$relationship = Mage::helper('jet/jet')->Jarray_merge($relationship, $resultData['relationship']);
					}					
				}
				
				$upload_file = false;
				$t=time();
				if(!empty($result) && count($result)>0){
					$merchantSkuPath = Mage::helper('jet')->createJsonFile( "MerchantSKUs", $result);
					$sku_file_name=  end(explode(DS, $merchantSkuPath));
					$upload_file = true;
				}
				if(!empty($price) && count($price)>0){
					$pricePath = Mage::helper('jet')->createJsonFile("Price", $price);
					$price_file_name=end(explode(DS, $pricePath));
				}
				if(!empty($inventory) && count($inventory)>0){
					$inventoryPath = Mage::helper('jet')->createJsonFile( "Inventory", $inventory);
					$inventory_file_name=end(explode(DS, $inventoryPath));
				}

				if($upload_file==false){
					Mage::getSingleton('core/session')
							->addError('Some Product informtion was incomplete so they are not prepared for upload.');
					continue;
				}

				$merchantSkuPath =	$merchantSkuPath.".gz";
				$pricePath =$pricePath.".gz";
				$inventoryPath =$inventoryPath.".gz";

				if(fopen($merchantSkuPath,"r")!=false){

					$response = Mage::helper('jet')->CGetRequest('/files/uploadToken');
					$data = json_decode($response);
					$fileid=$data->jet_file_id;
					$tokenurl=$data->url;

					$text = array('magento_batch_info'=>$commaseperatedids,'jet_file_id'=>$fileid,'token_url'=>$tokenurl,'file_name'=>$sku_file_name,'file_type'=>"MerchantSKUs",'status'=>'unprocessed');
					$model = Mage::getModel('jet/fileinfo')->addData($text);
					$model->save();
					$currentid=$model->getId();

					$reponse = Mage::helper('jet')->uploadFile($merchantSkuPath,$data->url);
					$postFields='{"url":"'.$data->url.'","file_type":"MerchantSKUs","file_name":"'.$sku_file_name.'"}';

					$response = Mage::helper('jet')->CPostRequest('/files/uploaded',$postFields);
					$data2  = json_decode($response);



					if($data2->status=='Acknowledged'){

						$update=array('status'=>'Acknowledged');
						$model = Mage::getModel('jet/fileinfo')->load($currentid)->addData($update);
						$model->setId($currentid)->save();

						
						if(!empty($relationship) && count($relationship)>0){
							$str="";
							foreach($relationship as $key=>$relval){

									$json_data = Mage::helper('jet')->Varitionfix(json_encode($relval), count($relval['variation_refinements']));

									$res = Mage::helper('jet')
										->CPutRequest('/merchant-skus/'.$key.'/variation',$json_data);
									
									if($res){
												$res1  = json_decode($res);
												if($res1 && count($res1->errors)>0){
													$str=$str."Error(s) in Relationship for sku : ".$key;
													foreach($res1->errors as $er){
														$str=$str."<br/>".$er;
													}
													$str=$str."<br/>";
												}
									}
							}
							if($str !=""){
									Mage::getSingleton('adminhtml/session')->addError($str);
							}
						}

					}
				}

				if(fopen($pricePath,"r")!=false){
					$response = Mage::helper('jet')->CGetRequest('/files/uploadToken');
					$data = json_decode($response);
					$fileid=$data->jet_file_id;
					$tokenurl=$data->url;
					$text = array('magento_batch_info'=>$commaseperatedids,'jet_file_id'=>$fileid,'token_url'=>$tokenurl,'file_name'=>$price_file_name,'file_type'=>"Price",'status'=>'unprocessed');
					$model = Mage::getModel('jet/fileinfo')->addData($text);
					$model->save();
					$currentid=$model->getId();
					$reponse = Mage::helper('jet')->uploadFile($pricePath,$data->url);

					$postFields='{"url":"'.$data->url.'","file_type":"Price","file_name":"'.$price_file_name.'"}';
					$responseprice = Mage::helper('jet')->CPostRequest('/files/uploaded',$postFields);
					$pricedata  = json_decode($responseprice);

					if($pricedata->status=='Acknowledged'){
						$update=array('status'=>'Acknowledged');
						$model = Mage::getModel('jet/fileinfo')->load($currentid)->addData($update);
						$model->setId($currentid)->save();

					}
				}


				if(fopen($inventoryPath,"r")!=false){
					$response = Mage::helper('jet')->CGetRequest('/files/uploadToken');
					$data = json_decode($response);
					$fileid=$data->jet_file_id;
					$tokenurl=$data->url;
					$text = array('magento_batch_info'=>$commaseperatedids,'jet_file_id'=>$fileid,'token_url'=>$tokenurl,'file_name'=>$inventory_file_name,'file_type'=>"Inventory",'status'=>'unprocessed');
					$model = Mage::getModel('jet/fileinfo')->addData($text);
					$model->save();
					$currentid=$model->getId();
					$reponse = Mage::helper('jet')->uploadFile($inventoryPath,$data->url);
					$postFields='{"url":"'.$data->url.'","file_type":"Inventory","file_name":"'.$inventory_file_name.'"}';
					$responseinventry = Mage::helper('jet')->CPostRequest('/files/uploaded',$postFields);

					$invetrydata=json_decode($responseinventry);
					if($invetrydata->status=='Acknowledged'){
						$update=array('status'=>'Acknowledged');
						$model = Mage::getModel('jet/fileinfo')->load($currentid)->addData($update);
						$model->setId($currentid)->save();
					}
				}
				if($upload_file){
					Mage::getSingleton('adminhtml/session')->addSuccess('Selected products Upload Request Send Successfully on Jet.com.');
				}else{
					Mage::getSingleton('adminhtml/session')->addSuccess('No Product Selected for Upload');
				}

				unset($result);
				unset($price);
				unset($inventory);
				unset($relationship);
			}

		$this->_redirect('adminhtml/adminhtml_jetrequest/uploadproduct');

	}


	public function directapiuploadAction(){

		$fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
		
		if(empty($fullfillmentnodeid) || $fullfillmentnodeid=='' || $fullfillmentnodeid== null){
			$data = array('error' => 'Enter fullfillmentnode id in Jet Configuration');
			return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
		}

		Mage::Helper('jet/jet')->createuploadDir();

		$fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
		if(empty($fullfillmentnodeid) || $fullfillmentnodeid=='' || $fullfillmentnodeid== null){
			$data = array('error' => 'Enter fullfillmentnode id form Jet Configuration');
			return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
			
		}

		$pid = $this->getRequest()->getPost('entity_id');
		$result=array();
		$node=array();
		$inventory=array();
		$price=array();
		$relationship = array();
		
		$resultData = Mage::helper('jet')->createProduct($pid);
		
		
		if($resultData['type']=='error'){
			$data = array('error' => $resultData['data']);
			return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
		}

		$sku_array = array_keys($resultData["merchantsku"]);
		$sku = trim($sku_array[0]);
			if($resultData)
			{
					$result = $resultData['merchantsku'];
					$price =  $resultData['price'];
					$inventory =  $resultData['inventory'];
					if(isset($resultData['relationship']))
					$relationship = $resultData['relationship'];
			}
			$upload_file = false;
			$t=time();
				

				if(!empty($result) && count($result)>0){
					$merchantSkuPath = Mage::helper('jet')->prepareJsonFile( "MerchantSKUs", $result);
					
				}
				
				if(!empty($price) && count($price)>0){
					$pricePath = Mage::helper('jet')->prepareJsonFile("Price", $price);
					
				}
				
				if(!empty($inventory) && count($inventory)>0){
					$inventoryPath = Mage::helper('jet')->prepareJsonFile( "Inventory", $inventory);
					
				}
			$api_res_mer_sku = "";
			$api_res_price = "";
			$api_res_inventry = "";



			if($merchantSkuPath)
			{
					$zz = json_decode($merchantSkuPath);
					foreach ($zz as $key => $value) {
						$response = Mage::helper('jet')->CPutRequest('/merchant-skus/'.trim($key),json_encode($value));

				$api_res_mer_sku  = json_decode($response);
				if($api_res_mer_sku->Message == 'Authorization has been denied for this request.')
				{
					$data = array('error' => 'Please check API User/API Secret/Fulfillment Node Id Under Jet->Configuration.');
					return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));	
				}
				if($api_res_mer_sku->errors)
				{
					$data = array('error' => $api_res_mer_sku->errors[0]);
					return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));	
				}
				
					}
				
				
					
					if(!empty($relationship) && count($relationship)>0){
						$str="";
						foreach($relationship as $key=>$relval){

							$json_data = Mage::helper('jet')->Varitionfix(json_encode($relval), count($relval['variation_refinements']));
							$res = Mage::helper('jet')
								->CPutRequest('/merchant-skus/'.$key.'/variation',$json_data);
							if($res){
								$res1  = json_decode($res);
								if($res1 && count($res1->errors)>0){
									$str=$str."Error(s) in Relationship for sku : ".$key;
									foreach($res1->errors as $er){
										$str=$str."<br/>".$er;
									}
									$str=$str."<br/>";
								}
							}
						}
						if($str !=""){
							return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($str));
						}
					}

				
			}	
				
			if($pricePath){
				$zz = json_decode($pricePath);
				foreach ($zz as $key => $value) {
						$response = Mage::helper('jet')->CPutRequest('/merchant-skus/'.trim($key).'/price',json_encode($value));
				$api_res_price  = json_decode($response);
				if($api_res_price->errors)
				{
					$data = array('error' => $api_res_price->errors[0]);
					return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));	
				}
					}
				}
				

			if($inventoryPath){
				$zz = json_decode($inventoryPath);
				foreach ($zz as $key => $value) {

				$response = Mage::helper('jet')->CPutRequest('/merchant-skus/'.trim($key).'/inventory',json_encode($value));
				$api_res_inventry  = json_decode($response);
				if($api_res_inventry->errors)
				{
					$data = array('error' => $api_res_inventry->errors[0]);
					return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));	
				}
			}
			}

			
			if($api_res_mer_sku == null && $api_res_price == null && $api_res_inventry == null)
			{
				Mage::getSingleton('adminhtml/session')->addSuccess('Products Upload Request Send Successfully on Jet.com.');
			if($resultData['type']=='success')
			{
				$data = array('sucess' => $resultData['data']);
				return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
			}

			}else{

				
				if($resultData['type']=='error')
				{
					$data = array('error' => $resultData['data']);
					return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
				}
			}
			unset($result);
			unset($price);
			unset($inventory);
			unset($relationship);		

		//$this->_redirect('adminhtml/adminhtml_jetrequest/uploadproduct');

	}


	/*
	* Action created for showing rejected Files
	*/
	public function rejectedAction(){
		$this->loadLayout();
		$this->renderLayout();
	}

	public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('jet/adminhtml_rejected_grid')->toHtml()
        );
    }

	/*
	* Action created for Showing file resubmission
	*/
	public function jerrorDetailsAction(){
		$id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('jet/errorfile');
	    if ($id) {
            $model->load($id);
			 if ($model->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jet')->__('Choosen Record is Not Found!'));
                $this->_redirect('adminhtml/adminhtml_jetrequest/rejcted');
            }
        }
        Mage::register('errorfile_collection', $model);

        $this->loadLayout();
        $this->renderLayout();
	}

	public function massDeleteAction(){
			$success_count=0;
			if(sizeof($this->getRequest()->getParam('error_ids'))>0){
				$errorIds = $this->getRequest()->getParam('error_ids');
				foreach($errorIds as $errorid){
					$error = Mage::getModel('jet/errorfile')->load($errorid);
					$error->delete();
					$success_count++;
				}
			}
			if($success_count>0){
				Mage::getSingleton('adminhtml/session')->addSuccess("$success_count record(s) successfully deleted.");
			}else{
					Mage::getSingleton('adminhtml/session')->addNotice("No record(s) deleted.");
			}
			$this->_redirect('*/*/rejected');
	}


}
