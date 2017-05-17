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
  
class Ced_Jet_Adminhtml_JetajaxController extends Mage_Adminhtml_Controller_Action{

	protected $_bulk_upload_batch = 50;
	protected $_bulk_archive_batch = 500;
	protected $_bulk_unarchive_batch = 500;
	protected $_bulk_invprice_batch = 50; 

	public function errordetailsAction(){
				if($this->getRequest()->getParam('id')){
							$product_id=$this->getRequest()->getParam('id');
							$this->getResponse()->setBody(
								$this->getLayout()
								->createBlock('core/template')->setTemplate("ced/jet/jeterror.phtml")
								->setData('id',$product_id)
								->toHtml()
						);
						
				}
	}

	public function massimportAction()
    {
		$data = $this->getRequest()->getParam('product');
		if ($data) {
            Mage::Helper('jet/jet')->createuploadDir();

            $productids = (array_chunk($data, $this->_bulk_upload_batch));
            Mage::getSingleton('adminhtml/session')->setProductChunks($productids);
            $this->loadLayout();
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError('No Product Selected.');
            $this->_redirect('*/adminhtml_jetrequest/uploadproduct');
        }
    }

    public function massarchivedAction(){
		$data = $this->getRequest()->getParam('product');
        if ($data) {
			Mage::Helper('jet/jet')->createuploadDir();
			
            $productids = (array_chunk($data, $this->_bulk_archive_batch));
            Mage::getSingleton('adminhtml/session')->setProductArcChunks($productids);
            $this->loadLayout();
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError('No Product Selected.');
            $this->_redirect('*/adminhtml_jetrequest/uploadproduct');
        }
    }

	/**
     * Import product pne by one
     */
	public function startUploadAction(){
		$message=array();
		$message['error']="";
		$message['success']="";
		$commaseperatedskus='';
		$commaseperatedskus1="";
		$commaseperatedids ="";
		
		$helper = Mage::helper('jet');
		$helper->initBatcherror();
		$key = $this->getRequest()->getParam('index');
		$api_dat =array();
		$api_dat = Mage::getSingleton( 'adminhtml/session' )->getProductChunks();
		$index = $key + 1;
		if(count($api_dat) <= $index){
			Mage::getSingleton('adminhtml/session')->unsProductChunks();
		}
		if(isset($api_dat[$key])){
					$product_ids= array();
					$product_ids= $api_dat[$key];
					$fullfillmentnodeid ="";
					$fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
					if(empty($fullfillmentnodeid) || $fullfillmentnodeid=='' || $fullfillmentnodeid== null){
						$message['error']=$message['error'].'Enter fullfillmentnode id in Jet Configuration.';
						echo json_encode($message);
						return;
					}
					$result=array();
			 		$node=array();
					$inventory=array();
					$price=array();
					$relationship = array();
					
					foreach($product_ids as $pid){
						$model="";
						$model=Mage::getModel('catalog/product')->load($pid);
						if($commaseperatedskus==""){
								$commaseperatedids = $pid; 
								$commaseperatedskus=$model->getSku();
								$commaseperatedskus1=$model->getSku();
						}else{
								$commaseperatedids = $commaseperatedids.','.$pid; 
								$commaseperatedskus=$commaseperatedskus." , ".$model->getSku();
								$commaseperatedskus1=$commaseperatedskus1.",".$model->getSku();
						}
						/*----batch manage start-----*/
						$helper->initBatchErrorForProduct($model->getId(),$index);
						/*----batch manage end-----*/
						if($resultData = $helper->createProductOnJet($pid)){
						
							$result = Mage::helper('jet/jet')->Jarray_merge($result, $resultData['merchantsku']);
							$price = Mage::helper('jet/jet')->Jarray_merge($price, $resultData['price']);
							$inventory =  Mage::helper('jet/jet')->Jarray_merge($inventory, $resultData['inventory']);
							
							$batcherror=array();
							$batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
							$error_msg="";
							$error_msg="Error occured in Batch $index :";
							$msg="";
							foreach($batcherror as $key=>$val){
									if($batcherror[$key]['error'] !=""){
											$msg=$msg."<br/>Error in Product Sku (".$batcherror[$key]['sku'].") :  ".$batcherror[$key]['error'];
									}
									
							}
							if($msg !=""){
									$message['error']=$error_msg.$msg;
							}
							if(isset($resultData['relationship']))
								$relationship = Mage::helper('jet/jet')->Jarray_merge($relationship, $resultData['relationship']);
					
						}
					}

							
					$upload_file = false;
					$t=time();
					if(!empty($result) && count($result)>0){
						$merchantSkuPath = $helper->createJsonFile( "MerchantSKUs", $result);
						$sku_file_name=  end(explode(DS, $merchantSkuPath));
						$upload_file = true;
					}
					if(!empty($price) && count($price)>0){
						$pricePath = $helper->createJsonFile( "Price", $price);
						$price_file_name=end(explode(DS, $pricePath));
					}
					if(!empty($inventory) && count($inventory)>0){
						$inventoryPath =$helper->createJsonFile( "Inventory", $inventory);
						$inventory_file_name=end(explode(DS, $inventoryPath));
						
					}
					
					if($upload_file==false){
						$batcherror=array();
						$batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
						$error_msg="";
						$error_msg="Error occured in Batch $index :";
						$message['error']=$error_msg;
						$msg="";
						foreach($batcherror as $key=>$val){
									if($batcherror[$key]['error'] !=""){
											$msg=$msg."<br/>Error in Product Sku (".$batcherror[$key]['sku'].") :  ".$batcherror[$key]['error'];
									}
									
						}
						if($msg !=""){
									$message['error']=$message['error'].$msg;
						}
						$message['error']=$message['error']."<br/>Some Product informtion was incomplete so they are not prepared for upload.";
						$message['error']=$message['error']."<br/>Product sku(s) that are not uploaded :- $commaseperatedskus";
						
						try{
								Mage::helper('jet')->saveBatchData($index);
						}catch(Exception $e){
								echo json_encode($message);
								return;
						}
						echo json_encode($message);
						return;
					}

					$merchantSkuPath =	$merchantSkuPath.".gz";
					$pricePath =$pricePath.".gz";
					$inventoryPath =$inventoryPath.".gz";
					
					if(fopen($merchantSkuPath,"r")!=false){
						$response =$helper->CGetRequest('/files/uploadToken');
						$data = json_decode($response);
						$fileid=$data->jet_file_id;
						$tokenurl=$data->url;
						$text = array('magento_batch_info'=>$commaseperatedids,'jet_file_id'=>$fileid,'token_url'=>$tokenurl,'file_name'=>$sku_file_name,'file_type'=>"MerchantSKUs",'status'=>'unprocessed');
						$model = Mage::getModel('jet/fileinfo')->addData($text);
						$model->save();
						$currentid=$model->getId();
						
						$reponse = $helper->uploadFile($merchantSkuPath,$data->url);
						$postFields='{"url":"'.$data->url.'","file_type":"MerchantSKUs","file_name":"'.$sku_file_name.'"}';
						$response = $helper->CPostRequest('/files/uploaded',$postFields);
						$data2  = json_decode($response);
						
						if($data2->status=='Acknowledged'){
							$update=array('status'=>'Acknowledged');
							$model = Mage::getModel('jet/fileinfo')->load($currentid)->addData($update);
							$model->setId($currentid)->save();
							if(!empty($relationship) && count($relationship)>0){
								$str="";
								foreach($relationship as $key=>$relval){
									$json_data = Mage::helper('jet')->Varitionfix(json_encode($relval), count($relval['variation_refinements'])); 
									
									$res = Mage::helper('jet')->CPutRequest('/merchant-skus/'.$key.'/variation',$json_data);
									if($res){
										$res1  = json_decode($res);
											if($res1 && count($res1->errors)>0){
												$rel_errors="";
												$rel_errors="Error(s) in Relationship for sku : ".$key;
												$str=$str."Error(s) in Relationship for sku : ".$key;
												foreach($res1->errors as $er){
													$str=$str."<br/>".$er;	
													$rel_errors=$rel_errors."<br/>".$er;	
												}

												$batcherror=array();
												$batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
												$err_msg="";
												$rel_product="";
												$rel_product=Mage::getModel('catalog/product')->loadByAttribute('sku', $key);
											if($rel_product instanceof Mage_Catalog_Model_Product){
												if($rel_product->getId()){
													if(count($batcherror)>0){
														if(array_key_exists($rel_product->getId(),$batcherror)){
															$batcherror[$rel_product->getId()]['error']=$batcherror[$rel_product->getId()]['error'].'<br/>'.$rel_errors;
															$batcherror[$rel_product->getId()]['sku']=$key;
														}else{
															$batcherror[$rel_product->getId()]['error']=$rel_errors;
															$batcherror[$rel_product->getId()]['sku']=$key;
														}
														Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
													}
												}
													
											}
											$str=$str."<br/>";
										}
									}
								}
								if($str !=""){
									$batcherror=array();
									$batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
									$error_msg="";
									$error_msg="Error occured in Batch $index :";
									
									if($message['error'] !=""){
											$message['error']=$message['error']."<br/>".$str;
									}else{
										$message['error']=$error_msg."<br/>".$str;
									}
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
					
					$batcherror=array();
					$batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
					$error_msg1="";
					$error_msg1="Error occured in Batch $index :";
					$message['error']=$error_msg1;
					$msg1="";
					$errored_skus=array();
					foreach($batcherror as $key=>$val){
								if($batcherror[$key]['error'] !=""){
										$errored_skus[]=$batcherror[$key]['sku'];
										$msg1=$msg1."<br/>Error in Product Sku (".$batcherror[$key]['sku'].") :  ".$batcherror[$key]['error'];
								}
								
					}
					if($msg1 !=""){
						$message['error']=$message['error'].$msg1;
					}else{
 						$message['error']="NO Error occured in Batch $index";
					} 
					
					$exploded_arr=array();
					if($commaseperatedskus1 !=""){
							$exploded_arr=explode(',',$commaseperatedskus1);
					}
					$successfull_arr=array();
					$successfull_arr=array_diff($exploded_arr,$errored_skus);
					
					$imploded_str="";
					$imploded_str=implode(' , ', $successfull_arr);
					$message['success']=$message['success']."Batch $index products Upload Request Send Successfully on Jet.com.Contained product skus are : $commaseperatedskus.Successfully uploaded product skus are : $imploded_str .";
					
					echo json_encode($message);
					
							
					unset($result);
					unset($price);
					unset($inventory);
					unset($relationship);
			
		}
		else{
			$message['error']=$message['error']."Batch $index included Product(s) data not found.";
			echo json_encode($message);
			
		}
		
		try{
			Mage::helper('jet')->saveBatchData($index);
		}catch(Exception $e){
				return;
		}
		
		
	}

    public function startArchieveAction(){
        $message=array();
        $message['error']="";
        $message['success']="";
        $commaseperatedskus='';
        $commaseperatedskus1="";
		$commp_ids ="";
		
        $helper=Mage::helper('jet');
        $helper->initBatcherror();
        $key = $this->getRequest()->getParam('index');
        $api_dat =array();
        $api_dat = Mage::getSingleton( 'adminhtml/session' )->getProductArcChunks();
        $index = $key + 1;
        if(count($api_dat) <= $index){ 
            Mage::getSingleton('adminhtml/session')->unsProductArcChunks();
        }

        if(isset($api_dat[$key])) {
            $product_ids = array();
            $product_ids = $api_dat[$key];
            $fullfillmentnodeid = "";
            $fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
            if (empty($fullfillmentnodeid) || $fullfillmentnodeid == '' || $fullfillmentnodeid == null) {
                $message['error'] = $message['error'] . 'Enter fullfillmentnode id in Jet Configuration.';
                //echo $helper->__('Enter fullfillmentnode id in Jet Configuration.');
                echo json_encode($message);
                return;
            }
        }

        $merchantNode=array();
        $productdata = Mage::getModel('catalog/product');
        $commaseperatedids = implode(",", $product_ids);
        foreach($product_ids as $pid){
            $commaseperatedids = implode(",", $pid);
            $productLoad=$productdata->load($pid);
            
            if($productLoad->isConfigurable()){
                $simple_collection = Mage::getModel('catalog/product_type_configurable')->setProduct($productLoad)
                    ->getUsedProductCollection()
                    ->addAttributeToSelect('sku')
                    ->addFilterByRequiredOptions();
					
                foreach ($simple_collection  as $_item)
                {
					
                    if($commaseperatedskus==""){
                        $commaseperatedskus=$_item->getSku();
						$commp_ids = $_item->getId(); 
                    }else{
						$commp_ids = $commp_ids.",".$_item->getId(); 
                        $commaseperatedskus=$commaseperatedskus." , ".$_item->getSku();
                    }

                    $merchantNode[$_item->getSku()]=array('is_archived'=>true);
                }
            }
            else{
                $sku = $productLoad->getData('sku');
                $merchantNode[$sku]=array('is_archived'=>true);

                if($commaseperatedskus==""){
                    $commaseperatedskus = $sku;
					$commp_ids = $productLoad->getId(); 
                }else{
					$commp_ids = $commp_ids.",".$productLoad->getId(); 
                    $commaseperatedskus = $commaseperatedskus.",".$sku;
                }
            }

        }

        $tokenresponse = Mage::helper('jet')->CGetRequest('/files/uploadToken');
        $tokendata = json_decode($tokenresponse);
        $fileid=$tokendata->jet_file_id;
        $tokenurl=$tokendata->url;
		
        $merchantSkuPath = Mage::helper('jet')->createJsonFile("ArchieveSKUs", $merchantNode);
		$sku_file_name = end(explode(DS, $merchantSkuPath));
		$merchantSkuPath=$merchantSkuPath.'.gz';
		

        $text = array('magento_batch_info'=>$commp_ids,'jet_file_id'=>$fileid,'token_url'=>$tokenurl,'file_name'=>$sku_file_name,'file_type'=>"Archive",'status'=>'unprocessed');
        $model = Mage::getModel('jet/fileinfo')->addData($text);
        $model->save();
        $currentid = $model->getId();
		
			
        $reponse = Mage::helper('jet')->uploadFile($merchantSkuPath,$tokenurl);

        $postFields='{"url":"'.$tokenurl.'","file_type":"Archive","file_name":"'.$sku_file_name.'"}';
        $response = Mage::helper('jet')->CPostRequest('/files/uploaded',$postFields);
        $data2  = json_decode($response);

        if($data2->status=='Acknowledged'){
			$update=array('status'=>'Acknowledged');
			$model1 = Mage::getModel('jet/fileinfo')->load($currentid);
			$model1->addData($update);
			$model1->save();
			
            $message['success']="Batch $index products Archieve Request Send Successfully on Jet.com.Contained product skus are : $commaseperatedskus.";
        }else{
            $message['error']="Batch $index products Archieve Request rejected on Jet.com";
        }
		
        echo json_encode($message);
		
        return;
    }

	public function startUnarchieveAction(){
		
        $message=array();
        $message['error']="";
        $message['success']="";
        $commaseperatedskus='';
        $commaseperatedskus1="";
		$commp_ids ="";
		
        $helper=Mage::helper('jet');
        $helper->initBatcherror();
        $key = $this->getRequest()->getParam('index');
        $api_dat =array();
        $api_dat = Mage::getSingleton( 'adminhtml/session' )->getProductUndoArcChunks();
        $index = $key + 1;
        if(count($api_dat) <= $index){ 
            Mage::getSingleton('adminhtml/session')->unsProductUndoArcChunks();
        }

        if(isset($api_dat[$key])) {
            $product_ids = array();
            $product_ids = $api_dat[$key];
            $fullfillmentnodeid = "";
            $fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
            if (empty($fullfillmentnodeid) || $fullfillmentnodeid == '' || $fullfillmentnodeid == null) {
                $message['error'] = $message['error'] . 'Enter fullfillmentnode id in Jet Configuration.';
                //echo $helper->__('Enter fullfillmentnode id in Jet Configuration.');
                echo json_encode($message);
                return;
            }
        }

        $merchantNode=array();
		$Inventory=array();
		
        $productdata = Mage::getModel('catalog/product');
        $commaseperatedids = implode(",", $product_ids);
        foreach($product_ids as $pid){
            $commaseperatedids = implode(",", $pid);
            $productLoad=$productdata->load($pid);
            
            if($productLoad->isConfigurable()){
                $simple_collection = Mage::getModel('catalog/product_type_configurable')->setProduct($productLoad)
                    ->getUsedProductCollection()
                    ->addAttributeToSelect('sku')
                    ->addFilterByRequiredOptions();
					
                foreach ($simple_collection  as $_item)
                {
					
                    if($commaseperatedskus==""){
                        $commaseperatedskus=$_item->getSku();
						$commp_ids = $_item->getId(); 
                    }else{
						$commp_ids = $commp_ids.",".$_item->getId(); 
                        $commaseperatedskus=$commaseperatedskus." , ".$_item->getSku();
                    }

                    $merchantNode[$_item->getSku()]=array('is_archived'=>false);
					$qty=(int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($_item)->getQty();
					$qty = ($qty < 0) ? 0 : $qty; 
					$temp = array();
					$temp=array($_item->getSku() => array(
									'fulfillment_nodes'=>array(
										 array('fulfillment_node_id'=>$fullfillmentnodeid
										 ,'quantity'=>$qty))));
										 
					$Inventory = Mage::helper('jet/jet')->Jarray_merge($temp,$Inventory);
                }
            }
            else{
                $sku = $productLoad->getData('sku');
                $merchantNode[$sku]=array('is_archived'=>false);

                if($commaseperatedskus==""){
                    $commaseperatedskus = $sku;
					$commp_ids = $productLoad->getId(); 
                }else{
					$commp_ids = $commp_ids.",".$productLoad->getId(); 
                    $commaseperatedskus = $commaseperatedskus.",".$sku;
                }
				
				$temp = array();
                $qty=(int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($productLoad)->getQty();
                $qty = ($qty < 0) ? 0 : $qty; 
				
				$temp= array($sku => array('fulfillment_nodes' =>array(array('fulfillment_node_id' => $fullfillmentnodeid, 'quantity' => $qty))));
				
				$Inventory = Mage::helper('jet/jet')->Jarray_merge($temp,$Inventory);
            }

        }

        $tokenresponse = Mage::helper('jet')->CGetRequest('/files/uploadToken');
        $tokendata = json_decode($tokenresponse);
        $fileid=$tokendata->jet_file_id;
        $tokenurl=$tokendata->url;
		
        $merchantSkuPath = Mage::helper('jet')->createJsonFile("unArchieveSKUs", $merchantNode);
		$sku_file_name = end(explode(DS, $merchantSkuPath));
		$merchantSkuPath=$merchantSkuPath.'.gz';
		

        $text = array('magento_batch_info'=>$commp_ids,'jet_file_id'=>$fileid,'token_url'=>$tokenurl,'file_name'=>$sku_file_name,'file_type'=>"Archive",'status'=>'unprocessed');
        $model = Mage::getModel('jet/fileinfo')->addData($text);
        $model->save();
        $currentid = $model->getId();
		
			
        $reponse = Mage::helper('jet')->uploadFile($merchantSkuPath,$tokenurl);

        $postFields='{"url":"'.$tokenurl.'","file_type":"Archive","file_name":"'.$sku_file_name.'"}';
        $response = Mage::helper('jet')->CPostRequest('/files/uploaded',$postFields);
        $data2  = json_decode($response);
		
        if($data2->status=='Acknowledged'){
			$update=array('status'=>'Acknowledged');
			$model1 = Mage::getModel('jet/fileinfo')->load($currentid);
			$model1->addData($update);
			$model1->save();
			
			if(count($Inventory)>0){		
				$tokenresponse2 = Mage::helper('jet')->CGetRequest('/files/uploadToken');
				$tokendata2 = json_decode($tokenresponse2);
					
				$inventorypath = Mage::helper('jet')->createJsonFile( "UncinventorySKUs", $Inventory);
				$inventory_file_name =  end(explode(DS, $inventorypath));
				$inventorypath = $inventorypath.'.gz';
	
				$text = array('magento_batch_info'=>$commp_ids,'jet_file_id'=>$tokendata2->jet_file_id,'token_url'=>$tokendata2->url,'file_name'=>$inventory_file_name,'file_type'=>"Inventory",'status'=>'unprocessed');
				$model2 = Mage::getModel('jet/fileinfo')->addData($text);
				$model2->save();
				
				$currentinvid=$model2->getId();
				
				$reponse = Mage::helper('jet')->uploadFile($inventorypath,$tokendata2->url);
				$postFields='{"url":"'.$tokendata2->url.'","file_type":"Inventory","file_name":"'.$inventory_file_name.'"}';
				$responseinventry = Mage::helper('jet')->CPostRequest('/files/uploaded',$postFields);
				
				$invetrydata=json_decode($responseinventry);
	
				if($invetrydata->status=='Acknowledged'){
				
					$update=array('status'=>'Acknowledged');
					$model = Mage::getModel('jet/fileinfo')->load($currentinvid)->addData($update);
					$model->save();
					
					$message['success']="Batch $index products Unarchieve Request Sent Successfully on Jet.com. Contained product skus are : $commaseperatedskus.";
				}else{
					$message['error']="Batch $index products Uarchieve Request rejected on Jet.com";
				}
			}		
            
        }
		
        echo json_encode($message);
		
        return;
    
	}
	
    public function archieveProduct($product,$sku){
        $data['is_archived']=true;
        $result = Mage::helper('jet')->CGetRequest('/merchant-skus/'.rawurlencode($sku));
        $response=json_decode($result);
        $msg=array();

        if($response->status == 'Archived' || $response->is_archived ==true){
            $msg[0]="error";
            $msg[1]="product with sku &nbsp".$sku."&nbsp already archieved";
            return $msg;
        }else {
            $data1=Mage::helper('jet')->CPutRequest('/merchant-skus/'.$sku.'/status/archive',json_encode($data));
            $product->setData('jet_product_status','Archived')->save();
            $msg[0]="success";
            $msg[1]="product with sku &nbsp".$sku."&nbsp successfully archieved";
           return $msg;
        }
    }


    public function massunarchivedAction(){
		$data = $this->getRequest()->getParam('product');
		if ($data) {
			Mage::Helper('jet/jet')->createuploadDir();
			
            $productids = (array_chunk($data, $this->_bulk_unarchive_batch));
			Mage::getSingleton('adminhtml/session')->setProductUndoArcChunks($productids);
			
            $this->loadLayout();
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError('No Product Selected.');
            $this->_redirect('*/adminhtml_jetrequest/uploadproduct/');
        }
    }

	 public function syncAction(){
	 
		Mage::Helper('jet/jet')->createuploadDir();
		
		$result=Mage::getModel('jet/jetcategory') ->getCollection()->addFieldToSelect('magento_cat_id');
		$resultdata=array();
		foreach($result as $val){
			$resultdata[]=$val['magento_cat_id'];
		}
		
		$collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('entity_id');
				
		$collectionData = $collection;
	
		$collectionProd = Mage::getModel('catalog/product')->getCollection();
		$collectionProd->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id =entity_id', null, 'left');
		$collectionProd->addAttributeToSelect('*')
				        ->addAttributeToFilter('category_id', array('in' => $resultdata));

		$ids = $collectionProd->getAllIds();
		$ids = array_unique($ids);
		
		$collection->addFieldToFilter('entity_id', array('in'=>$ids))
					->addAttributeToFilter('type_id', array('in' => array('simple','configurable')));
		
		$coll_data = $collection->getData();
		
		if(count($coll_data)>0){
			$productrows=array_chunk($coll_data,$this->_bulk_invprice_batch);
			Mage::getSingleton('adminhtml/session')->setSyncChunks($productrows);
			
			$this->loadLayout();
			$this->renderLayout();
		}else{
			Mage::getSingleton('core/session')->addError('No Product available in Upload Product list.');
			$this->_redirect('adminhtml/adminhtml_jetrequest/uploadproduct');	
		}
    }

	public function beginInvPriceSynAction(){
		$message=array();
        $message['error']="";
        $message['success']="";
        //$commaseperatedskus='';
        $commaseperatedids  ='';
		$fullfillmentnodeid = "";
		$fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
		
		if (empty($fullfillmentnodeid) || $fullfillmentnodeid == '' || $fullfillmentnodeid == null) {
			$message['error'] = $message['error'] . 'Enter fullfillmentnode id in Jet Configuration.';
			echo json_encode($message);
			return;
		}
		
        $helper=Mage::helper('jet')->initBatcherror();
		$key = $this->getRequest()->getParam('index');
		
        $api_dat =array();
        $api_dat = Mage::getSingleton( 'adminhtml/session' )->getSyncChunks();
        $index = $key + 1;
        if(count($api_dat) <= $index){
            Mage::getSingleton('adminhtml/session')->unsSyncChunks();
        }
		
        if(isset($api_dat[$key])) {
            $product_ids = array();
            $product_ids = $api_dat[$key];
		}
		
		$Inventory = array();
		$Price = array();
		
        
        foreach($product_ids as $prow){
            $commaseperatedids = $commaseperatedids .','.$prow['entity_id'];
			$productLoad = Mage::getModel('catalog/product')->load($prow['entity_id']);
			
            if(!$productLoad){
				return;
			}
			
           if($productLoad->isConfigurable()){ 

				$ids=Mage::getResourceSingleton('catalog/product_type_configurable')->getChildrenIds($prow['entity_id']);

				if(isset($ids[0]) && count ($ids[0])>0){
					// load all children data
					foreach ($ids[0]  as $prd)
					{
						$commaseperatedids = $commaseperatedids .','.$prd;
						$_item =false;
						$_item = Mage::getModel('catalog/product')->load($prd);
						if($_item){
							$node = array();	
							$node1 = array();
							
							if(!array_key_exists($Inventory,$_item->getSku())){
								$qty = 0;
								$qty=(int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($_item)->getQty();
								$qty = ($qty < 0) ? 0 : $qty; 
								$node1['fulfillment_node_id']="$fullfillmentnodeid";
								$node1['quantity']=$qty;
								
								$Inventory[$_item->getSku()]['fulfillment_nodes'][]=$node1;
								
								$product_price =0;
								
								$product_price =  Mage::helper('jet/jet')->getJetPrice($_item);
								$node['fulfillment_node_id']="$fullfillmentnodeid";
								$node['fulfillment_node_price'] = $product_price;
								$Price[$_item->getSku()]['price']=$product_price;
								$Price[$_item->getSku()]['fulfillment_nodes'][]=$node;
							}
						}	
					} 
				}
			}else{
                $node = array();	
				$node1 = array();
				
                $qty=(int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($productLoad)->getQty();
				$qty = ($qty < 0) ? 0 : $qty; 
				$node1['fulfillment_node_id']="$fullfillmentnodeid";
				$node1['quantity']=$qty;
				$Inventory[$productLoad->getSku()]['fulfillment_nodes'][]=$node1;
				
				$product_price =0;
				$product_price =   Mage::helper('jet/jet')->getJetPrice($productLoad);
					
				$node['fulfillment_node_id']="$fullfillmentnodeid";
				$node['fulfillment_node_price'] = $product_price;
				$Price[$productLoad->getSku()]['price']=$product_price;
				$Price[$productLoad->getSku()]['fulfillment_nodes'][]=$node;
		    }
        }
		
		if(count($Inventory)>0){
			$inventoryPath= "";	
			$inventoryPath = Mage::helper('jet')->createJsonFile("SyncInventory", $Inventory);
			$inventory_file_name=  end(explode(DS, $inventoryPath));
			$inventoryPath = $inventoryPath.'.gz';
			
			$tokenresponse = Mage::helper('jet')->CGetRequest('/files/uploadToken');
			$tokendata = json_decode($tokenresponse);
			
			$reponse = Mage::helper('jet')->uploadFile($inventoryPath,$tokendata->url);
			
			$postFields='{"url":"'.$tokendata->url.'","file_type":"Inventory","file_name":"'.$inventory_file_name.'"}';
			$responseInv = Mage::helper('jet')->CPostRequest('/files/uploaded',$postFields);
			
			$data2  = json_decode($responseInv);
	
			if($data2->status=='Acknowledged'){
				 $message['success']="Batch $index products Inventory Update Request Sent Successfully on Jet.com. Contained Product Ids are : $commaseperatedids.";
			}else{
				$message['error']="Batch $index products Inventory Update Request Rejected on Jet.com";
			}  
		}
		
		if(count($Price)>0){
			$price_Path ="";
			$price_file_name ="";
			
			$price_Path = Mage::helper('jet')->createJsonFile( "SyncPrice", $Price);
			$price_file_name =  end(explode(DS, $price_Path));
			$price_Path = $price_Path.'.gz';
			
			$tokenresponse2 = Mage::helper('jet')->CGetRequest('/files/uploadToken');
			$tokendata2 = json_decode($tokenresponse2);
			
			$reponse = Mage::helper('jet')->uploadFile($price_Path,$tokendata2->url);
			$postFields='{"url":"'.$tokendata2->url.'","file_type":"Price","file_name":"'.$price_file_name.'"}';
			$responsePrice = Mage::helper('jet')->CPostRequest('/files/uploaded',$postFields);
			
			$pricedata=json_decode($responsePrice);
	
			if($pricedata->status=='Acknowledged'){
				$message['success1']="Batch $index products Price Update Request Sent Successfully on Jet.com. Contained Product Ids are : $commaseperatedids.";
				
			}else{
				$message['error1']="Batch $index products Price Update Request Rejected on Jet.com";
			}
		}
		
        echo json_encode($message);
        return;

	}
}


