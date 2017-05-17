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


class Ced_Jet_Helper_Data extends Mage_Core_Helper_Abstract{
	protected $_apiHost='';
	protected $user='';
	protected $pass='';  
	public $batcherror=array();
	public function __construct(){
		$this->_apiHost = Mage::getStoreConfig('jet_options/ced_jet/jet_apiurl');
		$this->user =  Mage::getStoreConfig('jet_options/ced_jet/jet_user');
		$this->pass =  Mage::getStoreConfig('jet_options/ced_jet/jet_userpwd');
		
	}
	public function isEnabled(){
		$flag=false;
		if(Mage::getStoreConfig('jet_options/ced_jet/active')){
			$flag=true;
		}
		return $flag;
	}	
	
	public function JrequestTokenCurl(){
	
		$ch = curl_init();
		$url= $this->_apiHost.'/Token';
		$postFields='{"user":"'.$this->user.'","pass":"'.$this->pass.'"}';
		
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$postFields);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json;"));
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	
		$server_output = curl_exec ($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($server_output, 0, $header_size);
		$body = substr($server_output, $header_size);
		curl_close ($ch);
		$token_data =json_decode($body);
		
		if(is_object($token_data) && isset($token_data->id_token)){
			$data = new Mage_Core_Model_Config();
			$data->saveConfig('jetcom/token', $body, 'default', 0);
			return json_decode($body);
		}else{
			return false;
		}
			
	}
	
	/*
	 * Post Request on Jetcom
	 */
	
	public function CPostRequest($method,$postFields){
		
		$url= $this->_apiHost.$method;
	
		$tObject =$this->Authorise_token();
	
		$headers = array();
		$headers[] = "Content-Type: application/json";
		$headers[] = "Authorization: Bearer $tObject->id_token";
	
			
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS,$postFields);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			//curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	
	
			$server_output = curl_exec ($ch);
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($server_output, 0, $header_size);
			$body = substr($server_output, $header_size);
			curl_close ($ch);
	
			return $body;
	}
	
	/*
	* PUT Request on Jetcom
	*/
	public function CPutRequest($method, $post_field){

		

		$url= $this->_apiHost.$method;
		$ch = curl_init($url);
		$tObject =$this->Authorise_token();

		$headers = array();
		$headers[] = "Content-Type: application/json";
		$headers[] = "Authorization: Bearer $tObject->id_token";
				
	
			curl_setopt($ch, CURLOPT_POSTFIELDS,$post_field);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			//curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	
			$server_output = curl_exec ($ch);
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($server_output, 0, $header_size);
			$body = substr($server_output, $header_size);
			curl_close ($ch);
	
			return $body;
	
		}
	
		public function CGetRequest($method){
			$tObject =$this->Authorise_token();
			$ch = curl_init();
			$url= $this->_apiHost.$method;
	
	
			$headers = array();
			$headers[] = "Content-Type: application/json";
			$headers[] = "Authorization: Bearer $tObject->id_token";
	
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
			$server_output = curl_exec ($ch);
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($server_output, 0, $header_size);
			$body = substr($server_output, $header_size);
			curl_close ($ch);
	
			return $body;
		}
	
	
	
		public function Authorise_token(){
			$Jtoken = json_decode(Mage::getStoreConfig('jetcom/token'));
			$refresh_token =false;
			
			if(is_object($Jtoken) && $Jtoken!=null){
				$ch = curl_init();
				$url= $this->_apiHost.'/authcheck';
				
		
				$headers = array();
				$headers[] = "Content-Type: application/json";
				$headers[] = "Authorization: Bearer $Jtoken->id_token";
		
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
				$server_output = curl_exec ($ch);
				$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
				$header = substr($server_output, 0, $header_size);
				$body = substr($server_output, $header_size);
				curl_close ($ch);
				
				$bjson = json_decode($body);
				
			if(is_object($bjson) &&
				 $bjson->Message!='' &&
				 $bjson->Message=='Authorization has been denied for this request.')
			 	 {
					$refresh_token =true;	
				 }		
				
			}else{
				$refresh_token =true;	
			}
			
			if($refresh_token){
				$token_data = $this->JrequestTokenCurl();
				if($token_data!= false){
					return $token_data;
				}else{
					
					Mage::getSingleton('adminhtml/session')->addError('API user & API password either or Invalid.Please set API user & API pass from jet configuration.');
				}		
			}else{
				return $Jtoken;
			}
		
		}
	
		/**
		* http://www.php.net/manual/en/function.gzwrite.php#34955
		*
		* @param string $source Path to file that should be compressed
		* @param integer $level GZIP compression level (default: 9)
		* @return string New filename (with .gz appended) if success, or false if operation fails
		*/
		function gzCompressFile($source, $level = 9){
			$dest = $source . '.gz';
			$mode = 'wb' . $level;
			$error = false;
			if ($fp_out = gzopen($dest, $mode)) {
			if ($fp_in = fopen($source,'rb')) {
			while (!feof($fp_in))
				gzwrite($fp_out, fread($fp_in, 1024 * 512));
					fclose($fp_in);
				} else {
					$error = true;
				}
					gzclose($fp_out);
				} else {
					$error = true;
				}
			if ($error)
				return false;
			else
				return $dest;
		}
	
	
		/*
		* New function to upload file
		*/
		public function uploadFile($localfile ,$url){
			$headers = array();
			 $headers[] = "x-ms-blob-type:BlockBlob";
			 $ch = curl_init();
			 curl_setopt($ch, CURLOPT_URL, $url);
			 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			 curl_setopt($ch, CURLOPT_HEADER, 1);
			 curl_setopt($ch, CURLOPT_PUT, 1);
			 $fp = fopen ($localfile, 'r');
			 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			 curl_setopt($ch, CURLOPT_INFILE, $fp);
			 curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localfile));
	
	
			 $http_result = curl_exec($ch);
			 $error = curl_error($ch);
			 $http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);
	
			 curl_close($ch);
			 fclose($fp);
	
		}
	
	/*
	* jet return feed backoptions
	*/
	
	public function feedbackOptArray(){
	
				return array(
						array('value' => '', 'label' => Mage::helper('adminhtml')->__('Please Select a Option')),
	           				 array('value' => 'item damaged', 'label' => Mage::helper('adminhtml')->__('item damaged')),
	           				 array('value' => 'not shipped in original packaging', 'label' => Mage::helper('adminhtml')->__('not shipped in original packaging')),
	           				 array('value' => 'customer opened item', 'label' => Mage::helper('adminhtml')->__('customer opened item')),
							
	      			  );
	        
	}
	
	public function wanttoreturn(){
		return array(
						array('value' => '0', 'label' => Mage::helper('adminhtml')->__('No')),
	           				 array('value' => '1', 'label' => Mage::helper('adminhtml')->__('Yes')),
	           			);
	}
	/*
	* jet refund reason 
	*/
	public function refundreasonOptionArr(){
	
				return array(
						array('value' => '', 'label' => Mage::helper('adminhtml')->__('Please Select a Option')),
	           				 array('value' => 'No longer want this item', 'label' => Mage::helper('adminhtml')->__('No longer want this item')),
	           				 array('value' => 'Received the wrong item', 'label' => Mage::helper('adminhtml')->__('Received the wrong item')),
	           				 array('value' => 'Website description is inaccurate', 'label' => Mage::helper('adminhtml')->__('Website description is inaccurate')),
	           				 array('value' =>'Product is defective / does not work', 'label' => Mage::helper('adminhtml')->__('Product is defective / does not work')),
	           				 array('value' => 'Item arrived damaged - box intact', 'label' => Mage::helper('adminhtml')->__('Item arrived damaged - box intact')),
	           				 array('value' => 'Item arrived damaged - box damaged', 'label' => Mage::helper('adminhtml')->__('Item arrived damaged - box damaged')),
	           				 array('value' => 'Package never arrived', 'label' => Mage::helper('adminhtml')->__('Package never arrived')),
	           				 array('value' => 'Package arrived late', 'label' => Mage::helper('adminhtml')->__('Package arrived late')),
	           				 array('value' => 'Wrong quantity received', 'label' => Mage::helper('adminhtml')->__('Wrong quantity received')),
	           				 array('value' => 'Better price found elsewhere', 'label' => Mage::helper('adminhtml')->__('Better price found elsewhere')),
	           				 array('value' => 'Unwanted gift', 'label' => Mage::helper('adminhtml')->__('Unwanted gift')),
	           				 array('value' => 'Accidental order', 'label' => Mage::helper('adminhtml')->__('Accidental order')),
	           				 array('value' => 'Unauthorized purchase', 'label' => Mage::helper('adminhtml')->__('Unauthorized purchase')),
	           				 array('value' => 'Item is missing parts / accessories', 'label' => Mage::helper('adminhtml')->__('Item is missing parts / accessories')),
	           				 array('value' => 'Return to Sender - damaged, undeliverable, refused', 'label' => Mage::helper('adminhtml')->__('Return to Sender - damaged, undeliverable, refused')),
	           				 array('value' => 'Return to Sender - lost in transit only', 'label' => Mage::helper('adminhtml')->__('Return to Sender - lost in transit only')),
	           				 array('value' => 'Item is refurbished', 'label' => Mage::helper('adminhtml')->__('Item is refurbished')),
	           				 array('value' => 'Item is expired', 'label' => Mage::helper('adminhtml')->__('Item is expired')),
	           				 array('value' => 'Package arrived after estimated delivery date', 'label' => Mage::helper('adminhtml')->__('Package arrived after estimated delivery date')),
							
	      			  );
	        
	}
	
	public function shippingCarrier(){
		return array(
						array('value' => '', 'label' => Mage::helper('adminhtml')->__('Please Select an Option')),
	           			array('value' => 'SecondDay', 'label' => Mage::helper('adminhtml')->__('SecondDay')),
	           			array('value' => 'NextDay', 'label' => Mage::helper('adminhtml')->__('NextDay')),
	           			array('value' => 'Scheduled', 'label' => Mage::helper('adminhtml')->__('Scheduled')),
	           			array('value' =>'Expedited', 'label' => Mage::helper('adminhtml')->__('Expedited')),
	           			array('value' => 'Standard', 'label' => Mage::helper('adminhtml')->__('Standard')),
							
	      			  );
	}
	
	public function shippingMethod(){
		return array(
						array('value' => '', 'label' => Mage::helper('adminhtml')->__('Please Select an Option')),
	           			array('value' => 'UPS', 'label' => Mage::helper('adminhtml')->__('UPS')),
	           			array('value' => 'FedEx', 'label' => Mage::helper('adminhtml')->__('FedEx')),
	           			array('value' => 'USPS', 'label' => Mage::helper('adminhtml')->__('USPS')),
							
	      			);
	}
	
	public function shippingOverride(){
		return array(
						array('value' => '', 'label' => Mage::helper('adminhtml')->__('Please Select an Option')),
	           			array('value' => 'Override charge', 'label' => Mage::helper('adminhtml')->__('Override charge')),
	           			array('value' => 'Additional charge', 'label' => Mage::helper('adminhtml')->__('Additional charge')),
							
	      			);
	}
	
	public function shippingExcep(){
		return array(
						array('value' => '', 'label' => Mage::helper('adminhtml')->__('Please Select an Option')),
	           			array('value' => 'exclusive', 'label' => Mage::helper('adminhtml')->__('exclusive')),
	           			array('value' => 'restricted', 'label' => Mage::helper('adminhtml')->__('restricted')),	
	      			);
	}
	
	public function getFulfillmentNode(){
		$fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
		return $fullfillmentnodeid;
		
	}
	
	public function getStandardOffsetUTC()
	{
		$timezone = date_default_timezone_get();
		
		if($timezone == 'UTC') {
			return '';
		} else {
			$timezone = new DateTimeZone($timezone);
			$transitions = array_slice($timezone->getTransitions(), -3, null, true);
	
			foreach (array_reverse($transitions, true) as $transition)
			{
				if ($transition['isdst'] == 1)
				{
					continue;
				}
	
				return sprintf('UTC %+03d:%02u', $transition['offset'] / 3600, abs($transition['offset']) % 3600 / 60);
			}
	
			return false;
		}
	}

	public function Varitionfix($json, $count){
	
		$patt='';$replacement='';
		for($i=1; $i<=$count;$i++){
			$patt=$patt.'"(\d+)",';
			$replacement=$replacement.'$'.$i.',';
		}
		$patt=rtrim($patt,',');
		$replacement=rtrim($replacement,',');
		
		$pattern = '/"variation_refinements":\['.$patt.'\]/i';
		
		$replacement = '"variation_refinements":['.$replacement.']';
		
		return preg_replace($pattern, $replacement, $json);
	
	}
	
	public function ConvertNodeInt($json){
		
		$pattern = '/"jet_browse_node_id":"(\d+)"/i';

		$replacement = '"jet_browse_node_id":$1';
		$json_replaced_node = preg_replace($pattern, $replacement, $json);
		
		$pattern1 = '"mjattr';
		$replacement1 = '';		
		$json_replaced_node = str_replace($pattern1, $replacement1, $json_replaced_node);
		$pattern1 = 'mjattr"';
		$json_replaced_node = str_replace($pattern1, $replacement1, $json_replaced_node);
		
		
		
		$pattern1 = '/"attribute_id":"(\d+)"/i';
		$replacement1 = '"attribute_id":$1';
		return preg_replace($pattern1, $replacement1, $json_replaced_node);
		
	}
	

	public function createJsonFile($type, $data){
		$t=time()+rand(2,5);
		
		$finalskujson= json_encode($data);
		if($type=='MerchantSKUs'){
			$newJsondata = $this->ConvertNodeInt($finalskujson);
		}else{
			$newJsondata = $finalskujson;
		}
		
		$file_path = Mage::getBaseDir("var").DS."jetupload".DS.$type.$t.".json";
		$file_type = $type;
		$file_name=$type.$t.".json";
		$myfile = fopen($file_path, "w") ;
		fwrite($myfile, $newJsondata);
		fclose($myfile);
		if(fopen($file_path.".gz","r") == false){
			Mage::helper('jet')->gzCompressFile($file_path,9);
		}
		return $file_path;
	}

//for ajax upload start
	public function prepareJsonFile($type, $data){
		$t=time()+rand(2,5);
		
		$finalskujson= json_encode($data);
		if($type=='MerchantSKUs'){
			$newJsondata = $this->ConvertNodeInt($finalskujson);
		}else{
			$newJsondata = $finalskujson;
		}
		
		
		return $newJsondata;
	}
	//for ajax upload end

		public function generateJsonFile($type, $data){
			$t=time()+rand(2,5);

			$finalskujson= json_encode($data);
			if($type=='MerchantSKUs'){
				$newJsondata = $this->ConvertNodeInt($finalskujson);
			}else{
				$newJsondata = $finalskujson;
			}
			return $newJsondata;
		}
	
	public function getAssociatedJetCategoryId($product){
		$cats = $product->getCategoryIds();
		if(!$cats || (is_array($cats) && count($cats)<=0)){
				return false;
		}
		$Jet_cat_result = Mage::getModel('jet/jetcategory')
					->getCollection()
					->addFieldToFilter('magento_cat_id',array('in',$cats));
		if(count($Jet_cat_result)>0)			
			return $Jet_cat_result->getFirstItem();
		return false;
	}
	
	public function getAssociatedJetAttributeIds($product){
		if($jetCategory = $this->getAssociatedJetCategoryId($product)){
			$attribute = $jetCategory->getJetAttributes();
			//return array_map(function($val) { return "mjattr".$val."mjattr";} , explode(',', $attribute));
		}
		return false;
	}
	
	public function getMainProductSku($product){
		$sku = false;
		if($product->getTypeId()=="bundle"){
			/*
			$typeInstance = $product->getTypeInstance(true);
			$optionCollection = $typeInstance->getOptions($product);
			foreach ($optionCollection as $key => $value) {
				$option_id = $value->getData('option_id');
				$childProducts = $product->getTypeInstance(true)->getSelectionsCollection(array($option_id), $product);
				foreach($childProducts as $option){
					$detail = $this->getProductDetail($option->getSku());
					//skip the variation type of main product
					if(is_object($detail) && $detail->relationship == "Accessory")
						continue;
						
					if($sku = $option->getSku())
						break;
				}
				if($sku)
					break;						
			}
			*/
			return $sku;	
		}else if($product->getTypeId()=="grouped"){
			/*
				$childProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
				foreach($childProducts as $chp){
					 $detail = $this->getProductDetail($chp->getSku());
					//skip the variation type of main product
					if(is_object($detail) && $detail->relationship == "Accessory")
						continue;
					    $sku = $chp->getSku();
						break;
						
				}
			*/
			return $sku;				
		}else if($product->getTypeId()=="configurable"){

			 $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);

				 /*If only one product in child no need to make relation */
				 foreach($childProducts as $chp){
				 	
					 $detail = $this->getProductDetail($chp->getSku());
					
					if(is_object($detail) && $detail->relationship == "Variation"){
						//continue;
						 $sku =  $chp->getSku();
						 break;	
					}
										
					    $sku =  $chp->getSku();
					break;	
				}				
		}
			
		return $sku;
	}
	
	public function getProductDetail($sku){
		$response =$this->CGetRequest('/merchant-skus/'.rawurlencode($sku).'');
		$result=json_decode($response);	
		return $result;
	}

	public function getBatchIdFromProductId($pid=""){
		$batch_id=0;
		if($pid){
				$batchcoll="";
				$batchcoll=Mage::getModel('jet/batcherror')->getCollection();
				$batch_id="";
				foreach($batchcoll as $bat){
							if($bat->getData('product_id')==$pid){
									$batch_id=$bat->getData('id');
									return $batch_id;
							}
				}
		}
		return $batch_id;
	}
	public function initBatcherror(){
			$batcherror=array();
			Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
	}
	public function initBatchErrorForProduct($pid='',$index=0){
			$batcherror=array();
			$batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
			$batch_id=0;
			$batch_id=$this->getBatchIdFromProductId($pid);
			$model="";
			$model=Mage::getModel('catalog/product')->load($pid);
			if($batch_id){
				$batchmod='';
				$batchmod=Mage::getModel('jet/batcherror')->load($batch_id);
				$batchmod->setData("batch_num",$index);
				$batchmod->setData("is_write_mode",'1');
				$batchmod->setData("error",'');
				$batchmod->save();
			}else{
				$batchmod='';
				$batchmod=Mage::getModel('jet/batcherror');
				$batchmod->setData('product_id',$pid);
				$batchmod->setData('product_sku',$model->getSku());
				$batchmod->setData("is_write_mode",'1');
				$batchmod->setData("error",'');
				$batchmod->setData("batch_num",$index);
				$batchmod->save();
			}
			$batcherror[$pid]['error']="";
			$batcherror[$pid]['sku']=$model->getSku();
			$batcherror[$pid]['batch_num']=$index;
			Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
	}
	/* load Attribute details */
	public function getattributeType($attrcode){
		try{
			$load_attr = Mage::getModel('catalog/resource_eav_attribute')
				->loadByCode('catalog_product',$attrcode);
			if(!$load_attr->getId()){
				return false;
			}else{
				return $load_attr->getFrontendInput();
			}
		}catch(Exception $e){}
	}
	
	public function moreProductAttributesData($product){
		$config_amtype = Mage::getStoreConfig('jet_options/productextra_infomap/amazon_item_type_keyword');
		$config_num_unit = Mage::getStoreConfig('jet_options/productextra_infomap/number_units_for_ppu');
		$config_type_unit = Mage::getStoreConfig('jet_options/productextra_infomap/type_of_unit_for_ppu');
		
		$config_pl = Mage::getStoreConfig('jet_options/productextra_infomap/package_length_inches');
		$config_pw = Mage::getStoreConfig('jet_options/productextra_infomap/package_width_inches');
		$config_ph = Mage::getStoreConfig('jet_options/productextra_infomap/package_height_inches');
		$config_dl = Mage::getStoreConfig('jet_options/productextra_infomap/display_length_inches');
		$config_dw = Mage::getStoreConfig('jet_options/productextra_infomap/display_width_inches');
		$config_dh = Mage::getStoreConfig('jet_options/productextra_infomap/display_height_inches');
		
		$config_lgldesclaim = Mage::getStoreConfig('jet_options/productextra_infomap/legal_disclaimer_description');
		$config_Styw = Mage::getStoreConfig('jet_options/productextra_infomap/safety_warning');
		$config_msrp = Mage::getStoreConfig('jet_options/productextra_infomap/msrp');
		$config_filtime = Mage::getStoreConfig('jet_options/productextra_infomap/fullfillment_time');
		$confi_retun_fee = Mage::getStoreConfig('jet_options/productextra_infomap/noreturnfee_adjustment');
		$config_coo = Mage::getStoreConfig('jet_options/productextra_infomap/country_of_origin');
		$var_coo = $this->getattributeType($config_coo);
		
		$config_bullets = Mage::getStoreConfig('jet_options/productinfo_map/jbullets');
		$var_bullets = $this->getattributeType($config_bullets);
		
		$config_ship_weight = Mage::getStoreConfig('jet_options/productinfo_map/jshipping_weight_pounds');
		$config_map_price = Mage::getStoreConfig('jet_options/productinfo_map/jmap_price');
		
		
		$more=array();
		
		if(trim($config_amtype)!="" && $product->getData($config_amtype)!=""){
			$more['amazon_item_type_keyword']= $product->getData($config_amtype);
		}
		if($product->getData('category_path')!=""){
			$more['category_path']=$product->getData('category_path');
		}
		
		if($config_bullets == 'bullets' && $product->getData('bullets')!=""){ 
			$bullet_data=array();
			$bullets=$product->getData('bullets');
			preg_match_all("/\{(.*?)\}/", $bullets, $matches);
			$new_bullets=array();
			$new_bullets=$matches[1];
			$j=0;
			for($i=0;$i<count($new_bullets);$i++){
				$string=trim($new_bullets);
					if(strlen($string)<=500 && strlen($string)>0){
							$bullet_data[$j]=$string;
							$j++;
					}
					if($j>4){
						break;
					}
					
			}
			if(count($bullet_data)>0){
				$more['bullets']=$bullet_data;	
			}
		
		}else {
			
			if($var_bullets!=false){
				$buldata= $product->getData($config_bullets);
				
				if($buldata!=""){
					$explode_data = explode(",",$buldata);
					if(count($explode_data)>0){
							$more['bullets']=$explode_data;	
					}
				}
			} 
		}
		
		$ppu = (trim($config_num_unit)!="") ? $product->getData($config_num_unit) :"";
		if($ppu!="" && is_numeric($ppu)){
			$more['number_units_for_price_per_unit']=(float)$ppu;
		}
		
		if(trim($config_type_unit)!="" && $product->getData($config_type_unit)!=""){
			$more['type_of_unit_for_price_per_unit']=$product->getData($config_type_unit);
		}
		
		$ship_weight = (trim($config_ship_weight)!="") ? $product->getData($config_ship_weight): "";
		if($ship_weight!="" && is_numeric($ship_weight)){
			$more['shipping_weight_pounds']=(float)$ship_weight;
		}
		$pli = (trim($config_pl)!="") ? $product->getData($config_pl) : "";
		if($pli!=""  && is_numeric($pli)){
			$more['package_length_inches']=(float)$pli;
		}
		$plw = (trim($config_pw)!="") ? $product->getData($config_pw) : "";
		if($plw!=""  && is_numeric($plw)){
			$more['package_width_inches']=(float)$plw;
		}
		$plh = (trim($config_ph)!="") ? $product->getData($config_ph) : "";
		if($plh !=""  && is_numeric($plh)){
			$more['package_height_inches']=(float)$plh;
		}
		$dli = (trim($config_dl)!="") ? $product->getData($config_dl) : "";
		if($dli!=""  && is_numeric($dli)){
			$more['display_length_inches']=(float)$dli;
		}
		$dlw = (trim($config_dw)!="") ? $product->getData($config_dw) : "";
		if($dlw!=""  && is_numeric($dlw)){
			$more['display_width_inches']=(float)$dlw;
		}
		$dlh = (trim($config_dh)!="") ? $product->getData($config_dh) : "";
		if($dlh!=""  && is_numeric($dlh)){
			$more['display_height_inches']=(float)$dlh;
		}
		if($product->getData('prop_65')!=""){
			if($product->getData('prop_65')){
					$more['prop_65']=true;
			}else{
					$more['prop_65']=false;
			}
		}
		$lgl_desclaim = (trim($config_lgldesclaim)!="") ? $product->getData($config_lgldesclaim) : "";
		if($lgl_desclaim!=""){
			$string=trim($lgl_desclaim);
			if(strlen($string)<=500 && strlen($string)>0){
					$more['legal_disclaimer_description']=$string;
			}
		}
		if($product->getData('cpsia_cautionary_statements')!=""){
			$string="";
			$string=$product->getData('cpsia_cautionary_statements');
			$arr=explode(',',$string);
			if(count($arr)>0){
				if($arr[0]=='no warning applicable')
					array_shift($arr);	
					
				$more['cpsia_cautionary_statements']=$arr;	
			}
		}
		
		if(trim($config_Styw)!="" && $product->getData($config_Styw)!=""){
			$string=trim($product->getData($config_Styw));
			if(strlen($string)>0){
				$more['safety_warning']=substr($string,0,1999);
			}
		}
		if($product->getData('start_selling_date')!=""){
			$string=$product->getData('start_selling_date');
			$offset_end = $this->getStandardOffsetUTC(); 
			if(empty($offset_end) || trim($offset_end)==''){
				$offset = '.0000000-00:00';
			}else{
				$offset = '.0000000'.trim($offset_end);
			}
			$shipTodatetime="";
			$shipTodatetime=strtotime($string);
			$Ship_todate ="";
			$Ship_todate = date("Y-m-d", $shipTodatetime) . 'T' . date("H:i:s", $shipTodatetime).$offset;
			$more['start_selling_date']= $Ship_todate;
			
		}
		
		$fill_time = (trim($config_filtime)!="") ? $product->getData($config_filtime) : ""; 
		if($fill_time!=""  && is_numeric($fill_time)){
			$more['fulfillment_time']=(int)$fill_time;
		}
		$jmap_price = (trim($config_map_price)!="") ? $product->getData($config_map_price) : "";
		if($jmap_price!="" && is_numeric($jmap_price)){
			$more['map_price']=(float)$jmap_price;
		}
		$dmsrp = (trim($config_msrp)!="") ? $product->getData($config_msrp) : "";
		if($dmsrp!="" && is_numeric($dmsrp)){
			$more['msrp']=(float)$dmsrp;
		}
		if($product->getData('map_implementation')!=""){
				$more['map_implementation']=$product->getData('map_implementation');
		}
		if($product->getData('product_tax_code')!=""){
				$more['product_tax_code']=$product->getData('product_tax_code');
		}
		//no_return_fee_adjustment
		$dretun_fee = (trim($confi_retun_fee)!="") ? $product->getData($confi_retun_fee) : "";
		if($dretun_fee!="" && is_numeric($dretun_fee)){
				$more['no_return_fee_adjustment']=(float)$dretun_fee;
		}
		if($product->getData('exclude_from_fee_adjust')!=""){
			if($product->getData('exclude_from_fee_adjust')){
					$more['exclude_from_fee_adjustments']=true;
			}else{
					$more['exclude_from_fee_adjustments']=false;
			}
		}
		if($product->getData('ships_alone')!=""){
			if($product->getData('ships_alone')){
					$more['ships_alone']=true;
			}else{
					$more['ships_alone']=false;
			}
		}
		
		if($config_manufacture =='country_of_manufacture' &&  $product->getData('country_of_manufacture')!=""){
				$country_name = Mage::getModel('directory/country')->loadByCode($product->getData('country_of_manufacture'))->getName();
				$country_name=trim($country_name);
				if(strlen($country_name)<=50 && strlen($country_name)>0){
						$more['country_of_origin']= substr($country_name, 0,50);
				}

		}else{
			$val_count_manufacture =  $this->getattributeType($config_coo);
			if($val_count_manufacture!=false){
				$country_manufact='';
				if($val_count_manufacture=='select')
					$county_manufact = $product->getAttributeText($config_coo);
				else
					$country_manufact = $product->getData($config_coo);
					
				if($country_manufact!=''){	
					$country_manufact = substr($country_manufact,0,50);
					$SKU_Array['country_of_origin']="$country_manufact";
				}	
			}	
		}
		return $more;

	}

	public function createProduct($product){

			
		$fullfillmentnodeid = $this->getFulfillmentNode();
		$result=array();
		$node=array();
		$inventory=array();
		$price=array();
		$relationship = array();
		$msg = array();

		$config_brand = Mage::getStoreConfig('jet_options/productinfo_map/jbrand');
		$brand_attr_type = $this->getattributeType($config_brand);

		$config_mfpn = Mage::getStoreConfig('jet_options/productinfo_map/jmanufacturer_part_number');
		$config_manufacture = Mage::getStoreConfig('jet_options/productinfo_map/jmanufacture');
		$val_count_manufacture =  $this->getattributeType($config_manufacture);

		$config_title = Mage::getStoreConfig('jet_options/productinfo_map/jtitle');
		$config_descp = Mage::getStoreConfig('jet_options/productinfo_map/jdescription');
		$con_upc = Mage::getStoreConfig('jet_options/productinfo_map/jupc');
		$con_ean = Mage::getStoreConfig('jet_options/productinfo_map/jean');
		$con_isbn13 = Mage::getStoreConfig('jet_options/productinfo_map/jisbn_13');
		$con_isbn10 = Mage::getStoreConfig('jet_options/productinfo_map/jisbn_10');
		$con_gtin = Mage::getStoreConfig('jet_options/productinfo_map/jgtin_14');
		$con_asin = Mage::getStoreConfig('jet_options/productinfo_map/jasin');

		$config_multipack = Mage::getStoreConfig('jet_options/productinfo_map/jmultipack_quantity');
		$val_multipack = $this->getattributeType($config_multipack);

		if(is_numeric($product))
			$product = Mage::getModel('catalog/product')->load($product);


		if($product instanceof Mage_Catalog_Model_Product){

			if($product->getTypeId() == "configurable"){
				$jetAttrId = array();
				$sProductSku = array();
				$sku = false;
				$sku = $this->getMainProductSku($product);
				$par_brand_name="";
				if(!$sku){
					$msg['type'] = 'error';
					$msg['data'] = 'Does not contain the main product '.$product->getName().' So this is skipped from upload' ;
				return $msg;
				}
				$par_pro="";
				$par_pro=Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

				if($par_pro instanceof Mage_Catalog_Model_Product){
					$par_brand_name = '';

					if($brand_attr_type!=false){
						if($brand_attr_type=='select'){
							$par_brand_name =$par_pro->getAttributeText($config_brand);
						}else{
							$par_brand_name = $par_pro->getData($config_brand);
						}
					}else{
						$par_brand_name = $par_pro->getData('jet_brand');
					}
				}

				if($par_brand_name==""){
					$msg['type'] = 'error';
					$msg['data'] = 'Does not contain Brand Name in Child Product  main product '.$par_pro->getName().' of Parent product '.$product->getName().' So this is skipped from upload.' ;
				return $msg;

				}


				$childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
				//If only one product in child no need to make relation variation
				foreach($childProducts as $chp){
					$chd_pro=Mage::getModel('catalog/product')->load($chp->getId());

					if($brand_attr_type!=false){
						if($brand_attr_type=='select'){
							$chd_brand_name =$chd_pro->getAttributeText($config_brand);
						}else{
							$chd_brand_name = $chd_pro->getData($config_brand);
						}
					}else{
						$chd_brand_name = $chd_pro->getData('jet_brand');
					}

					if($par_brand_name != trim($chd_brand_name) ){
						$msg['type'] = 'error';
					$msg['data'] = 'Does not matching Brand Name in Child Product '.$chd_pro->getName().' with Product '.$par_pro->getName().' of Parent product '.$product->getName().' .So this is skipped from upload.' ;
				return $msg;
						
					}
				}
				foreach($childProducts as $chp){

					if($resultData = $this->createProduct($chp->getId())){
							if($resultData['type']=='error')
						{
							$msg['type'] = 'error';
							$msg['data'] = $resultData['data'];
							return $msg;
						}
						$result = Mage::helper('jet/jet')->Jarray_merge ($result, $resultData['merchantsku']);

						$price = Mage::helper('jet/jet')->Jarray_merge($price, $resultData['price']);
						$inventory =  Mage::helper('jet/jet')->Jarray_merge($inventory, $resultData['inventory']);
						$sProductSku[] = $chp->getSku();
					}
				}

				$sProductSku = array_values(array_diff( $sProductSku, array($sku))) ;

				if(count($sProductSku)>0){

					$productAttributeOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
					foreach ($productAttributeOptions as $productAttribute) {
						$jetattribute = Mage::getModel('jet/jetattribute');
						$collection = $jetattribute->getCollection()->addFieldToFilter('magento_attr_id',  $productAttribute['attribute_id']);
						if($item = $collection->getFirstItem()){
							$jetAttrId[] = $item->getJetAttrId();
						}
					}

					$relationship[$sku]['relationship']= "Variation";
					$relationship[$sku]['variation_refinements']= $jetAttrId;
					$relationship[$sku]['children_skus']= $sProductSku;

				}
				$prodData = array();
				$prodData['merchantsku'] = $result;
				$prodData['price'] = $price;
				$prodData['inventory'] = $inventory;
				$prodData['type']='success';
				if(count($relationship)>0)
					$prodData['relationship'] = $relationship;

				return $prodData;

			}else if($product->getTypeId() == "grouped"){
				$msg['type'] = 'error';
				$msg['data'] = 'product type not supported on Jet .' ;
				return $msg;

			}else if($product->getTypeId() == "bundle"){
				$msg['type'] = 'error';
				$msg['data'] = 'product type not supported on Jet .' ;
				return $msg;
			}

			$SKU_Array= array(); $ean = NULL;
			$Attribute_array = array();

			$upc =(trim($con_upc)!="") ? $product->getData($con_upc): "";
			$asin = (trim($con_asin)!="") ? $product->getData($con_asin): "";
			$ean =  (trim($con_ean)!="") ?  $product->getData($con_ean) : "";
			$isbn_10 = (trim($con_isbn10)!="") ? $product->getData($con_isbn10) :"";
			$isbn_13 = (trim($con_isbn13)!="") ? $product->getData($con_isbn13) :"";
			$gtin_14 = (trim($con_gtin)!="") ? $product->getData($con_gtin) : "";

			$is_variation =false;
			$_uniquedata =array();

			if($upc!=NULL){
				$_uniquedata=array("type"=>"UPC","value"=>$upc,"allow"=>(strlen($upc)==12)?1:0,"size"=>12);
			}else if($ean!=NULL){
				$_uniquedata=array("type"=>"EAN","value"=>$ean,"allow"=>(strlen($ean)==13)?1:0,"size"=>13);
			}else if($isbn_10!=NULL){
				$_uniquedata=array("type"=>"ISBN-10","value"=>$isbn_10,"allow"=>(strlen($isbn_10)==10)?1:0,"size"=>10);
			}else if($isbn_13!=NULL){
				$_uniquedata=array("type"=>"ISBN-13","value"=>$isbn_13,"allow"=>(strlen($isbn_13)==13)?1:0,"size"=>13);
			}else if($gtin_14!=NULL){
				$_uniquedata=array("type"=>"GTIN-14","value"=>$gtin_14,"allow"=>(strlen($gtin_14)==14)?1:0,"size"=>14);
			}else{
				$_uniquedata = array("type"=>FALSE);
			}

			$mfrp_exist =false;
			$val_mfpn = $this->getattributeType($config_mfpn);

			if($val_mfpn!=false){
				if($val_mfpn=='select'){
					$manu_part_number = $product->getAttributeText($config_mfpn);
				}else{
					$manu_part_number = $product->getData($config_mfpn);
				}
			}
			if($manu_part_number!=null){
				$mfrp_exist = true;
			}
			$brand = '';
			if($brand_attr_type!=false){
				if($brand_attr_type=='select'){
					$brand =$product->getAttributeText($config_brand);
				}else{
					$brand = $product->getData($config_brand);
				}
			}else{
				$brand = $product->getData('jet_brand');
			}

			$validate = true;

			if($brand==NULL || trim($brand)==''){
				$validate =false;
				$err_msg= "Error in Product: ".$product->getName()." Brand information Required & One of these values(UPC, EAN,GTIN-14,ISBN-13,ISBN-10) OR ASIN OR Manufacturer Part Number are Required.";

			}else if($_uniquedata['type']== FALSE && $asin==NULL && $mfrp_exist ==FALSE){
				$validate =false;
				$err_msg= "Error in Product: ".$product->getName()." Brand information Required & One of these values(UPC, EAN,GTIN-14,ISBN-13,ISBN-10) OR ASIN OR  Manufacturer Part Number are Required.";


			}else if($_uniquedata['type']!= FALSE) {

				if($_uniquedata['allow']==0){
					$err_msg=  "Error in Product: ".$product->getName()." ".$_uniquedata['type']." must be of ".$_uniquedata['size']." digits";
					$validate =false;
				}

			}else if($asin!=NULL)  {
				if(strlen($asin)!=10 && $_uniquedata['type']==FALSE){
					$validate =false;
					$err_msg=  "Error in Product: ".$product->getName()." ASIN must be of 10 digits";
				}
			}




			$cat_error ='';
			$cate_validate =true;
			$cats = $product->getCategoryIds();
			try{
				if(count($cats)>0){
					$Jet_cat_result = Mage::getModel('jet/jetcategory')
						->getCollection()
						->addFieldToFilter('magento_cat_id',array('in',$cats))->getData();

					if(count($Jet_cat_result)>0){
						$attribute=$Jet_cat_result[0]['jet_attributes'];
						$arr=explode(",",$attribute);

						$prd_browser_nodeid = $Jet_cat_result[0]['jet_cate_id'];
					}else{
						$parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')
      							->getParentIdsByChild($product->getId());

      					$ass_cat_id = Mage::getModel('catalog/product')->load($parentIds)->getCategoryIds();

					if(count($ass_cat_id)>0)
					{
								$Jet_cat_result = Mage::getModel('jet/jetcategory')
								->getCollection()
								->addFieldToFilter('magento_cat_id',array('in',$ass_cat_id))->getData();

							if(count($Jet_cat_result)>0){
								$attribute=$Jet_cat_result[0]['jet_attributes'];
								$arr=explode(",",$attribute);

								$prd_browser_nodeid = $Jet_cat_result[0]['jet_cate_id'];
							}
							else
							{
								$cate_validate =false;
								$cat_error = 'SKU: <b>'.$product->getSku().'</b></br> Rejected : Product is not mapped with any Jet category ';
							}
					}
					else
						{
							$cate_validate =false;
									$cat_error = 'SKU: <b>'.$product->getSku().'</b></br> Rejected : Product is not mapped with any Jet category ';
							
						}
					}

				}else{
					
					$cate_validate =false;
					$cat_error = 'SKU: <b>'.$product->getSku().' </b></br> Rejected : Product is not mapped with any Jet category ';
				}
			}catch(Exception $e){
				$cate_validate =false;
				$cat_error =$e->getMessage();
			}
			$batcherror=array();
			
			if($cate_validate ==false){
				//Mage::getSingleton('adminhtml/session')->addError($cat_error);

				$msg['type'] = 'error';
				$msg['data'] = $cat_error ;
				return $msg;

				/* if(count($batcherror)>0){
					if(array_key_exists($product->getId(),$batcherror)){
						$batcherror[$product->getId()]['error']=$batcherror[$product->getId()]['error'].'<br/>'.$cat_error;
						$batcherror[$product->getId()]['sku']=$product->getSku();
					}else{
						$batcherror[$product->getId()]['error']=$cat_error;
						$batcherror[$product->getId()]['sku']=$product->getSku();
					}
					Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
				}*/

			}else if($validate==false){

				$msg['type'] = 'error';
				$msg['data'] = $err_msg ;
				return $msg;

				/*$batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();

				if(count($batcherror)>0){
					if(array_key_exists($product->getId(),$batcherror)){
						$batcherror[$product->getId()]['error']=$batcherror[$product->getId()]['error'].'<br/>'.$err_msg;
						$batcherror[$product->getId()]['sku']=$product->getSku();
					}else{
						$batcherror[$product->getId()]['error']=$err_msg;
						$batcherror[$product->getId()]['sku']=$product->getSku();
					}
					Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
				}
				Mage::getSingleton('core/session')->addError($err_msg);*/

			} else {
				$more=array();
				$more=$this->moreProductAttributesData($product);
				$SKU_Array=Mage::helper('jet/jet')->Jarray_merge($SKU_Array,$more);

				$sku=$product->getSku();

				if(trim($config_title)!="" && $product->getData($config_title)!="")
					$SKU_Array['product_title'] = substr($product->getData($config_title), 0,500);
				else
					$SKU_Array['product_title']= substr($product->getName(),0,500);

				if(strlen($SKU_Array['product_title']) < 5){
					$msg['type'] = 'error';
					$msg['data'] = 'product title length must be equal or greater than 5' ;
					return $msg;
				}

				$description="";

				if(trim($config_descp) && $product->getData($config_descp)!=""){
					$description = $product->getData($config_descp);
				}else{
					$description = $product->getDescription();
				}
				$description = (strlen($description) > 1999) ? substr($description,0,1999) : $description;
				$description = strip_tags($description);

				if(strlen($description) == 0){
					$msg['type'] = 'error';
					$msg['data'] = 'product description not found' ;
					return $msg;
				}


				$SKU_Array['product_description'] = $description;
				$SKU_Array['brand']= substr($brand,0,100);
				if($asin!=null){
					$SKU_Array['ASIN']=$asin;
				}
				if(isset($_uniquedata['type']) && $_uniquedata['type']!= FALSE){
					$txt['standard_product_code']= trim($_uniquedata['value']);
					$txt['standard_product_code_type']=$_uniquedata['type'];
					$SKU_Array['standard_product_codes'][]=$txt;
				}
				if($mfrp_exist){
					$SKU_Array['mfr_part_number']=substr($manu_part_number, 0,50);
				}

				$SKU_Array['jet_browse_node_id']= $prd_browser_nodeid;
				$multipack = 1;

				if($val_multipack != false){
					if($val_multipack=='select')
						$multipack = $product->getAttributeText($config_multipack);
					else
						$multipack = $product->getData($config_multipack);

					if(is_numeric($multipack) && $multipack >0 && $multipack < 129){
						$SKU_Array['multipack_quantity']= (int)$multipack;
					}
				}else{
					$SKU_Array['multipack_quantity']= 1;
				}


				if($config_manufacture =='country_of_manufacture'){
					$country_name=Mage::app()->getLocale()->getCountryTranslation($product->getData('country_of_manufacture'));
					$country_name = substr($country_name,0,100);
					if($country_name!=''){
						$SKU_Array['manufacturer']="$country_name";
					}
				}else{
					if($val_count_manufacture!=false){
						$country_manufact='';
						if($val_count_manufacture=='select')
							$county_manufact = $product->getAttributeText($config_manufacture);
						else
							$country_manufact = $product->getData($config_manufacture);

						if($country_manufact!=''){
							$SKU_Array['manufacturer']="$country_manufact";
						}
					}
				}

				$image= Mage::getModel('catalog/product_media_config')->getMediaUrl( $product->getImage());
				$image1=explode("/",$image);
				if(end($image1)!='no_selection'){
					$SKU_Array['main_image_url']=$image;
				}else{
					$msg['type'] = 'error';
					$msg['data'] = 'product image not found ' ;
					return $msg;
				}



				$_images  =  $product->getMediaGalleryImages();
				$jet_image_slot = 1;
				$jetalternat_image = array();
				foreach($_images as $alternat_image){
					if($alternat_image->getUrl() !='' && $alternat_image->getUrl()!= $image){
						$SKU_Array['alternate_images'][]= array('image_slot_id'=>$jet_image_slot,
							'image_url'=> $alternat_image->getUrl()
						);
						$jet_image_slot++;
						if($jet_image_slot>7)
							break;

					}
				}

				/* image upload alternatives msising */
				foreach ($arr as $key =>$ar){

					$attribute =	Mage::getModel('jet/jetattribute')->getCollection()->addFieldToFilter('jet_attr_id',$ar)->getFirstItem();
					if($attribute){
						if($attribute->getJetAttrId()>0){
							$magentoattributeid=$attribute->getMagentoAttrId();
							$data=	Mage::getModel('eav/entity_attribute')->load($magentoattributeid)->getData();


							if(isset($data['attribute_id']) && $data['attribute_id']!=null){
								$code= $data['attribute_code'];
								$attr_type = $data['frontend_input'];

								if(isset($attr_type) && $attr_type =='select'){
									$codevalue= (string) $product->getAttributeText($code);
								}else{
									$codevalue= (string) $product->getData($code);
								}

								/* check free text value first it is 0 , 1 or 2 */
								$free_text = $attribute->getFreetext();
								if(isset($free_text) && $free_text!=null && trim($codevalue)!=''){


									if($free_text ==0 || $free_text ==1){

										$Attribute_array[] = array(
											'attribute_id'=>$attribute->getJetAttrId(),
											'attribute_value'=>$codevalue
										);

									}else{

										$units_exp = explode("," ,$attribute->getUnit());

										if($attribute->getUnit() && $attribute->getUnit()!=null && count($units_exp)>0){

											$code_before_space = explode(" ",$codevalue);
											array_pop($code_before_space);
											$first_half = trim($code_before_space[0]);

											$getUnit_value = end(explode(" ",$codevalue));
											$getUnit_value = trim($getUnit_value);

											if(isset($first_half) && $first_half!=''){

												if(count($units_exp)>0){
													if(!empty($getUnit_value) || $getUnit_value!=''){

														if(in_array($getUnit_value,$units_exp)){

															$Attribute_array[] = array(
																'attribute_id'=> $attribute->getJetAttrId(),
																'attribute_value'=>$first_half,
																'attribute_value_unit'=>$Unit_value);
														}else{

															$emsg= 'Unit value is required for this attribute '.$data['attribute_code'].' from one of these comma seperated values: '.$attribute->getUnit().' for example : '.$code_before_space[0].'{space}'.$units_exp[0].' ie. '.$code_before_space[0].' '.$units_exp[0];

															
															$err_msg="";
															$err_msg=$emsg.' for product '. $product->getName().' So this is skipped from upload.';
																$msg['type'] = 'error';
																$msg['data'] = $err_msg ;
																return $msg;
															
														}
													}
												}
											}

										}else{
											$Attribute_array[] = array(
												'attribute_id'=> $attribute->getJetAttrId(),
												'attribute_value'=>$codevalue);

										}
									}

								}
							}
						}
					}
				}

				if(!empty($SKU_Array)){
					$SKU_Array['attributes_node_specific'] = $Attribute_array;
					$result[$sku]= $SKU_Array;
					$product_price =  Mage::helper('jet/jet')->getJetPrice($product);

					$node['fulfillment_node_id']="$fullfillmentnodeid";
					$node['fulfillment_node_price'] = $product_price;
					$price[$sku]['price']=$product_price;
					$price[$sku]['fulfillment_nodes'][]=$node;


					$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
					$qty= (int)$stock->getQty();
					$node1['fulfillment_node_id']="$fullfillmentnodeid";

					if($qty <= 0){
						$node1['quantity'] = 0;
						$msg['type'] = 'error';
						$msg['data'] = 'product is out of stock' ;
						return $msg;
					}else{
						$node1['quantity']=$qty;
					}
					$inventory[$sku]['fulfillment_nodes'][]=$node1;

					$prodData = array();
					$prodData['merchantsku'] = $result;
					$prodData['price'] = $price;
					$prodData['inventory'] = $inventory;
					$prodData['type']='success';

					return $prodData;
				}
			}
		}
		else{
			return false;
		}
	}

	public function createProductOnJet($product , $childPrice , $parent_image){

		$fullfillmentnodeid = $this->getFulfillmentNode();
		$result=array();
		$node=array();
		$inventory=array();
		$price=array();
		$relationship = array();
		$msg = array();

		$config_brand = Mage::getStoreConfig('jet_options/productinfo_map/jbrand');
		$brand_attr_type = $this->getattributeType($config_brand);

		$config_mfpn = Mage::getStoreConfig('jet_options/productinfo_map/jmanufacturer_part_number');
		$config_manufacture = Mage::getStoreConfig('jet_options/productinfo_map/jmanufacture');
		$val_count_manufacture =  $this->getattributeType($config_manufacture);

		$config_title = Mage::getStoreConfig('jet_options/productinfo_map/jtitle');
		$config_descp = Mage::getStoreConfig('jet_options/productinfo_map/jdescription');
		$con_upc = Mage::getStoreConfig('jet_options/productinfo_map/jupc');
		$con_ean = Mage::getStoreConfig('jet_options/productinfo_map/jean');
		$con_isbn13 = Mage::getStoreConfig('jet_options/productinfo_map/jisbn_13');
		$con_isbn10 = Mage::getStoreConfig('jet_options/productinfo_map/jisbn_10');
		$con_gtin = Mage::getStoreConfig('jet_options/productinfo_map/jgtin_14');
		$con_asin = Mage::getStoreConfig('jet_options/productinfo_map/jasin');

		$config_multipack = Mage::getStoreConfig('jet_options/productinfo_map/jmultipack_quantity');
		$val_multipack = $this->getattributeType($config_multipack);

		if(is_numeric($product))
			$product = Mage::getModel('catalog/product')->load($product);


		if($product instanceof Mage_Catalog_Model_Product){

			if($product->getTypeId() == "configurable"){ 
				$jetAttrId = array();
				$sProductSku = array();
				$sku = false;
				$sku = $this->getMainProductSku($product);
				
				$par_brand_name="";
				if(!$sku){
					$batcherror=array();
					$batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
					$err_msg="";
					$err_msg="Does not contain the main product ".$product->getName().". So this is skipped from upload.";
					if(count($batcherror)>0){
						if(array_key_exists($product->getId(),$batcherror)){
							$batcherror[$product->getId()]['error']=$batcherror[$product->getId()]['error'].'<br/>'.$err_msg;
							$batcherror[$product->getId()]['sku']=$product->getSku();
						}else{
							$batcherror[$product->getId()]['error']=$err_msg;
							$batcherror[$product->getId()]['sku']=$product->getSku();
						}
						Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
					}
					Mage::getSingleton('core/session')->addError('Does not contain the main product '.$product->getName().' So this is skipped from upload');

					return;
				}

				$par_pro="";
				$par_pro=Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

				if($par_pro instanceof Mage_Catalog_Model_Product){
					$par_brand_name = '';

					if($brand_attr_type!=false){
						if($brand_attr_type=='select'){
							$par_brand_name =$par_pro->getAttributeText($config_brand);
						}else{
							$par_brand_name = $par_pro->getData($config_brand);
						}
					}else{
						$par_brand_name = $par_pro->getData('jet_brand');
					}
				}

				if($par_brand_name==""){
					$batcherror=array();
					$batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
					$err_msg="";
					$err_msg='Does not contain Brand Name in Child Product  main product '.$par_pro->getName().' of Parent product '.$product->getName().' .So this is skipped from upload.';
					if(count($batcherror)>0){
						if(array_key_exists($product->getId(),$batcherror)){
							$batcherror[$product->getId()]['error']=$batcherror[$product->getId()]['error'].'<br/>'.$err_msg;
							$batcherror[$product->getId()]['sku']=$product->getSku();
						}else{
							$batcherror[$product->getId()]['error']=$err_msg;
							$batcherror[$product->getId()]['sku']=$product->getSku();
						}
						Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
					}

					Mage::getSingleton('core/session')->addError('Does not contain Brand Name in Child Product  main product '.$par_pro->getName().' of Parent product '.$product->getName().' So this is skipped from upload.');
					return;
				}


				$childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
				//If only one product in child no need to make relation variation
				foreach($childProducts as $chp){
					$chd_pro=Mage::getModel('catalog/product')->load($chp->getId());

					if($brand_attr_type!=false){
						if($brand_attr_type=='select'){
							$chd_brand_name =$chd_pro->getAttributeText($config_brand);
						}else{
							$chd_brand_name = $chd_pro->getData($config_brand);
						}
					}else{
						$chd_brand_name = $chd_pro->getData('jet_brand');
					}

					if($par_brand_name != trim($chd_brand_name) ){
						$batcherror=array();
						$batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
						$err_msg="";
						$err_msg='Does not matching Brand Name in Child Product '.$chd_pro->getName().' with Product '.$par_pro->getName().' of Parent product '.$product->getName().' .So this is skipped from upload.';
						if(count($batcherror)>0){
							if(array_key_exists($product->getId(),$batcherror)){
								$batcherror[$product->getId()]['error']=$batcherror[$product->getId()]['error'].'<br/>'.$err_msg;
								$batcherror[$product->getId()]['sku']=$product->getSku();
							}else{
								$batcherror[$product->getId()]['error']=$err_msg;
								$batcherror[$product->getId()]['sku']=$product->getSku();
							}
							Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
						}
						Mage::getSingleton('core/session')->addError('Does not matching Brand Name in Child Product '.$chd_pro->getName().' with Product '.$par_pro->getName().' of Parent product '.$product->getName().' .So this is skipped from upload.');

						return;
					}
				}
				foreach($childProducts as $chp){

					if($resultData = $this->createProductOnJet($chp->getId() , $childPrice ,$parent_image)){						
						$result = Mage::helper('jet/jet')->Jarray_merge ($result, $resultData['merchantsku']);
						$price = Mage::helper('jet/jet')->Jarray_merge($price, $resultData['price']);
						$inventory =  Mage::helper('jet/jet')->Jarray_merge($inventory, $resultData['inventory']);
						$sProductSku[] = $chp->getSku();
					}
				}

				

				$sProductSku = array_values(array_diff( $sProductSku, array($sku))) ;
				
				
				

				if(count($sProductSku)>0){

					$productAttributeOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);

					foreach ($productAttributeOptions as $productAttribute) {
						$jetattribute = Mage::getModel('jet/jetattribute');
						$collection = $jetattribute->getCollection()->addFieldToFilter('magento_attr_id',  $productAttribute['attribute_id']);
						if($item = $collection->getFirstItem()){
							$jetAttrId[] = $item->getJetAttrId();
						}
					}

					$relationship[$sku]['relationship']= "Variation";
					$relationship[$sku]['variation_refinements']= $jetAttrId;
					$relationship[$sku]['children_skus']= $sProductSku;

				}
				$prodData = array();
				$prodData['merchantsku'] = $result;
				$prodData['price'] = $price;
				$prodData['inventory'] = $inventory;

				if(count($relationship)>0)
					$prodData['relationship'] = $relationship;
				
				return $prodData;

			}else if($product->getTypeId() == "grouped"){
				$msg['type'] = 'error';
				$msg['data'] = 'product type not supported on Jet .' ;
				return $msg;

			}else if($product->getTypeId() == "bundle"){
				$msg['type'] = 'error';
				$msg['data'] = 'product type not supported on Jet .' ;
				return $msg;
			}

			$SKU_Array= array(); $ean = NULL;
			$Attribute_array = array();

			$upc =(trim($con_upc)!="") ? $product->getData($con_upc): "";
			$asin = (trim($con_asin)!="") ? $product->getData($con_asin): "";
			$ean =  (trim($con_ean)!="") ?  $product->getData($con_ean) : "";
			$isbn_10 = (trim($con_isbn10)!="") ? $product->getData($con_isbn10) :"";
			$isbn_13 = (trim($con_isbn13)!="") ? $product->getData($con_isbn13) :"";
			$gtin_14 = (trim($con_gtin)!="") ? $product->getData($con_gtin) : "";

			$is_variation =false;
			$_uniquedata =array();

			if($upc!=NULL){
				$_uniquedata=array("type"=>"UPC","value"=>$upc,"allow"=>(strlen($upc)==12)?1:0,"size"=>12);
			}else if($ean!=NULL){
				$_uniquedata=array("type"=>"EAN","value"=>$ean,"allow"=>(strlen($ean)==13)?1:0,"size"=>13);
			}else if($isbn_10!=NULL){
				$_uniquedata=array("type"=>"ISBN-10","value"=>$isbn_10,"allow"=>(strlen($isbn_10)==10)?1:0,"size"=>10);
			}else if($isbn_13!=NULL){
				$_uniquedata=array("type"=>"ISBN-13","value"=>$isbn_13,"allow"=>(strlen($isbn_13)==13)?1:0,"size"=>13);
			}else if($gtin_14!=NULL){
				$_uniquedata=array("type"=>"GTIN-14","value"=>$gtin_14,"allow"=>(strlen($gtin_14)==14)?1:0,"size"=>14);
			}else{
				$_uniquedata = array("type"=>FALSE);
			}

			$mfrp_exist =false;
			$val_mfpn = $this->getattributeType($config_mfpn);

			if($val_mfpn!=false){
				if($val_mfpn=='select'){
					$manu_part_number = $product->getAttributeText($config_mfpn);
				}else{
					$manu_part_number = $product->getData($config_mfpn);
				}
			}
			if($manu_part_number!=null){
				$mfrp_exist = true;
			}
			$brand = '';
			if($brand_attr_type!=false){
				if($brand_attr_type=='select'){
					$brand =$product->getAttributeText($config_brand);
				}else{
					$brand = $product->getData($config_brand);
				}
			}else{
				$brand = $product->getData('jet_brand');
			}

			$validate = true;

			if($brand==NULL || trim($brand)==''){
				$validate =false;
				$err_msg= "Error in Product: ".$product->getName()." Brand information Required & One of these values(UPC, EAN,GTIN-14,ISBN-13,ISBN-10) OR ASIN OR Manufacturer Part Number are Required.";

			}else if($_uniquedata['type']== FALSE && $asin==NULL && $mfrp_exist ==FALSE){
				$validate =false;
				$err_msg= "Error in Product: ".$product->getName()." Brand information Required & One of these values(UPC, EAN,GTIN-14,ISBN-13,ISBN-10) OR ASIN OR  Manufacturer Part Number are Required.";


			}else if($_uniquedata['type']!= FALSE) {

				if($_uniquedata['allow']==0){
					$err_msg=  "Error in Product: ".$product->getName()." ".$_uniquedata['type']." must be of ".$_uniquedata['size']." digits";
					$validate =false;
				}

			}else if($asin!=NULL)  {
				if(strlen($asin)!=10 && $_uniquedata['type']==FALSE){
					$validate =false;
					$err_msg=  "Error in Product: ".$product->getName()." ASIN must be of 10 digits";
				}
			}




			$cat_error ='';
			$cate_validate =true;
			$cats = $product->getCategoryIds();
			try{
				if(count($cats)>0){
					$Jet_cat_result = Mage::getModel('jet/jetcategory')
						->getCollection()
						->addFieldToFilter('magento_cat_id',array('in',$cats))->getData();

					if(count($Jet_cat_result)>0){
						$attribute=$Jet_cat_result[0]['jet_attributes'];

						$arr=explode(",",$attribute);

						$prd_browser_nodeid = $Jet_cat_result[0]['jet_cate_id'];
					}else{
						$cate_validate =false;
						$cat_error = 'SKU: <b>'.$product->getSku().'</b></br> Rejected : Product is not mapped with any Jet category ';
					}

				}else{
					$cate_validate =false;
					$cat_error = 'SKU: <b>'.$product->getSku().' </b></br> Rejected : Product is not mapped with any Jet category ';
				}
				
			}catch(Exception $e){
				$cate_validate =false;
				$cat_error =$e->getMessage();
			}
			$batcherror=array();

			if($cate_validate ==false){
				//Mage::getSingleton('adminhtml/session')->addError($cat_error);

				$msg['type'] = 'error';
				$msg['data'] = $cat_error ;
				return $msg;

				/* if(count($batcherror)>0){
					if(array_key_exists($product->getId(),$batcherror)){
						$batcherror[$product->getId()]['error']=$batcherror[$product->getId()]['error'].'<br/>'.$cat_error;
						$batcherror[$product->getId()]['sku']=$product->getSku();
					}else{
						$batcherror[$product->getId()]['error']=$cat_error;
						$batcherror[$product->getId()]['sku']=$product->getSku();
					}
					Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
				}*/

			}else if($validate==false){

				$msg['type'] = 'error';
				$msg['data'] = $err_msg ;
				return $msg;

				/*$batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();

				if(count($batcherror)>0){
					if(array_key_exists($product->getId(),$batcherror)){
						$batcherror[$product->getId()]['error']=$batcherror[$product->getId()]['error'].'<br/>'.$err_msg;
						$batcherror[$product->getId()]['sku']=$product->getSku();
					}else{
						$batcherror[$product->getId()]['error']=$err_msg;
						$batcherror[$product->getId()]['sku']=$product->getSku();
					}
					Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
				}
				Mage::getSingleton('core/session')->addError($err_msg);*/

			} else {
				$more=array();
				$more=$this->moreProductAttributesData($product);
				$SKU_Array=Mage::helper('jet/jet')->Jarray_merge($SKU_Array,$more);

				$sku=$product->getSku();

				if(trim($config_title)!="" && $product->getData($config_title)!="")
					$SKU_Array['product_title'] = substr($product->getData($config_title), 0,500);
				else
					$SKU_Array['product_title']= substr($product->getName(),0,500);

				if(strlen($SKU_Array['product_title']) < 5){
					$msg['type'] = 'error';
					$msg['data'] = 'product title length must be equal or greater than 5' ;
					return $msg;
				}
				
				$description="";

				if(trim($config_descp) && $product->getData($config_descp)!=""){
					$description = $product->getData($config_descp);
				}else{
					$description = $product->getDescription();
				}
				$description = (strlen($description) > 1999) ? substr($description,0,1999) : $description;
				$description = strip_tags($description);

				if(strlen($description) == 0){
					$msg['type'] = 'error';
					$msg['data'] = 'product description not found' ;
					return $msg;
				}
				

				$SKU_Array['product_description'] = $description;
				$SKU_Array['brand']= substr($brand,0,100);
				if($asin!=null){
					$SKU_Array['ASIN']=$asin;
				}
				if(isset($_uniquedata['type']) && $_uniquedata['type']!= FALSE){
					$txt['standard_product_code']=$_uniquedata['value'];
					$txt['standard_product_code_type']=$_uniquedata['type'];
					$SKU_Array['standard_product_codes'][]=$txt;
				}
				if($mfrp_exist){
					$SKU_Array['mfr_part_number']=substr($manu_part_number, 0,50);
				}

				$SKU_Array['jet_browse_node_id']= $prd_browser_nodeid;
				$multipack = 1;

				if($val_multipack != false){
					if($val_multipack=='select')
						$multipack = $product->getAttributeText($config_multipack);
					else
						$multipack = $product->getData($config_multipack);

					if(is_numeric($multipack) && $multipack >0 && $multipack < 129){
						$SKU_Array['multipack_quantity']= (int)$multipack;
					}
				}else{
					$SKU_Array['multipack_quantity']= 1;
				}


				if($config_manufacture =='country_of_manufacture'){
					$country_name=Mage::app()->getLocale()->getCountryTranslation($product->getData('country_of_manufacture'));
					$country_name = substr($country_name,0,100);
					if($country_name!=''){
						$SKU_Array['manufacturer']="$country_name";
					}
				}else{
					if($val_count_manufacture!=false){
						$country_manufact='';
						if($val_count_manufacture=='select')
							$county_manufact = $product->getAttributeText($config_manufacture);
						else
							$country_manufact = $product->getData($config_manufacture);

						if($country_manufact!=''){
							$SKU_Array['manufacturer']="$country_manufact";
						}
					}
				}

				$image= Mage::getModel('catalog/product_media_config')->getMediaUrl( $product->getImage());
				$image1=explode("/",$image);
				if(end($image1)!='no_selection'){
					$SKU_Array['main_image_url']=$image;
				}else{
					$msg['type'] = 'error';
					$msg['data'] = 'product image not found ' ;
					return $msg;
				}



				$_images  =  $product->getMediaGalleryImages();
				$jet_image_slot = 1;
				$jetalternat_image = array();
				foreach($_images as $alternat_image){
					if($alternat_image->getUrl() !='' && $alternat_image->getUrl()!= $image){
						$SKU_Array['alternate_images'][]= array('image_slot_id'=>$jet_image_slot,
							'image_url'=> $alternat_image->getUrl()
						);
						$jet_image_slot++;
						if($jet_image_slot>7)
							break;

					}
				}

				/* image upload alternatives msising */
				foreach ($arr as $key =>$ar){

					$attribute =	Mage::getModel('jet/jetattribute')->getCollection()->addFieldToFilter('jet_attr_id',$ar)->getFirstItem();
					if($attribute){
						if($attribute->getJetAttrId()>0){
							$magentoattributeid=$attribute->getMagentoAttrId();
							$data=	Mage::getModel('eav/entity_attribute')->load($magentoattributeid)->getData();


							if(isset($data['attribute_id']) && $data['attribute_id']!=null){
								$code= $data['attribute_code'];
								$attr_type = $data['frontend_input'];

								if(isset($attr_type) && $attr_type =='select'){
									$codevalue= (string) $product->getAttributeText($code);
								}else{
									$codevalue= (string) $product->getData($code);
								}

								/* check free text value first it is 0 , 1 or 2 */
								$free_text = $attribute->getFreetext();
								if(isset($free_text) && $free_text!=null && trim($codevalue)!=''){


									if($free_text ==0 || $free_text ==1){

										$Attribute_array[] = array(
											'attribute_id'=>$attribute->getJetAttrId(),
											'attribute_value'=>$codevalue
										);

									}else{

										$units_exp = explode("," ,$attribute->getUnit());

										if($attribute->getUnit() && $attribute->getUnit()!=null && count($units_exp)>0){

											$code_before_space = explode(" ",$codevalue);
											array_pop($code_before_space);
											$first_half = trim($code_before_space[0]);

											$getUnit_value = end(explode(" ",$codevalue));
											$getUnit_value = trim($getUnit_value);

											if(isset($first_half) && $first_half!=''){

												if(count($units_exp)>0){
													if(!empty($getUnit_value) || $getUnit_value!=''){

														if(in_array($getUnit_value,$units_exp)){

															$Attribute_array[] = array(
																'attribute_id'=> $attribute->getJetAttrId(),
																'attribute_value'=>$first_half,
																'attribute_value_unit'=>$Unit_value);
														}else{

															$emsg= 'Unit value is required for this attribute '.$data['attribute_code'].' from one of these comma seperated values: '.$attribute->getUnit().' for example : '.$code_before_space[0].'{space}'.$units_exp[0].' ie. '.$code_before_space[0].' '.$units_exp[0];

															$batcherror=array();
															$batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
															$err_msg="";
															$err_msg=$emsg.' for product '. $product->getName().' So this is skipped from upload.';
															/*if(count($batcherror)>0){
																if(array_key_exists($product->getId(),$batcherror)){
																	$batcherror[$product->getId()]['error']=$batcherror[$product->getId()]['error'].'<br/>'.$err_msg;
																	$batcherror[$product->getId()]['sku']=$product->getSku();
																}else{
																	$batcherror[$product->getId()]['error']=$err_msg;
																	$batcherror[$product->getId()]['sku']=$product->getSku();
																}
																Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
															}
															Mage::getSingleton('core/session')
																->addError($emsg.' for product '. $product->getName().' So this is skipped from upload');
															$emsg='';															
															continue;*/

															if(count($batcherror)>0){
																$msg['type'] = 'error';
																$msg['data'] = $err_msg ;
																return $msg;
															}
														}
													}
												}
											}

										}else{
											$Attribute_array[] = array(
												'attribute_id'=> $attribute->getJetAttrId(),
												'attribute_value'=>$codevalue);

										}
									}

								}
							}
						}
					}
				}

				if(!empty($SKU_Array)){
					$SKU_Array['attributes_node_specific'] = $Attribute_array;
					$result[$sku]= $SKU_Array;
					$product_price =  Mage::helper('jet/jet')->getJetPrice($product);

					$node['fulfillment_node_id']="$fullfillmentnodeid";
					
					if($childPrice){
						$node['fulfillment_node_price'] = $childPrice[$sku];
						$price[$sku]['price']=$childPrice[$sku];
					}
					else{$node['fulfillment_node_price'] = $product_price;
						$price[$sku]['price']=$product_price;}
					$price[$sku]['fulfillment_nodes'][]=$node;


					$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
					$qty= (int)$stock->getQty();
					$node1['fulfillment_node_id']="$fullfillmentnodeid";

					if($qty < 0){
						$node1['quantity'] = 0;
						$msg['type'] = 'error';
						$msg['data'] = 'product is out of stock' ;
						return $msg;
					}else{
						$node1['quantity']=$qty;
					}
					$inventory[$sku]['fulfillment_nodes'][]=$node1;

					$prodData = array();
					$prodData['merchantsku'] = $result;
					$prodData['price'] = $price;
					$prodData['inventory'] = $inventory;
					$prodData['type']='success';

					return $prodData;
				}
			}
		}
		else{
			return false;
		}

	}

	public function saveBatchData($index=""){
				$date="";
				$date = date('Y-m-d H:i:s');
				$batcherror=array();
				$batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
				foreach($batcherror as $key=>$val){
						$id=$key;
						if($val['error'] !=""){
							$batch_id=0;
							$batch_id=$this->getBatchIdFromProductId($id);
							//$model="";
							//$model=Mage::getModel('catalog/product')->load($id);
							if($batch_id){
										$batchmod='';

										$batchmod=Mage::getModel('jet/batcherror')->load($batch_id);
										$batchmod->setData("batch_num",$index);
										$batchmod->setData("is_write_mode",'0');
										$batchmod->setData("error",$val['error']);
										$batchmod->setData('product_sku',$val['sku']);
										$batchmod->setData("date_added",$date);
										$batchmod->save();
							}else{
										$batchmod='';
										$batchmod=Mage::getModel('jet/batcherror');
										$batchmod->setData('product_id',$id);
										$batchmod->setData('product_sku',$val['sku']);
										$batchmod->setData("is_write_mode",'0');
										$batchmod->setData("error",$val['error']);
										$batchmod->setData("batch_num",$index);
										$batchmod->setData("date_added",$date);
										$batchmod->save();
							}
						}else{
							$batch_id=0;
							$batch_id=$this->getBatchIdFromProductId($id);
							$err="";
							if($batch_id){
										$batchmod='';
										$batchmod=Mage::getModel('jet/batcherror')->load($batch_id);
										$batchmod->setData("batch_num",$index);
										$batchmod->setData("is_write_mode",'0');
										$batchmod->setData("error",$err);
										$batchmod->setData('product_sku',$val['sku']);
										$batchmod->setData("date_added",$date);
										$batchmod->save();
							}
						}
				}
				Mage::getSingleton('adminhtml/session')->unsBatcherror();
	}
	
	public function generateCreditMemoForRefund($details_after_saved=''){
		if($details_after_saved && count($details_after_saved)>0){
			$sku_details="";
			$sku_details=$details_after_saved['sku_details'];
			$item_details=array();
			$merchant_order_id="";
			$merchant_order_id=$details_after_saved['refund_orderid'];
			$shipping_amount=0;
			$adjustment_positive=0;
			foreach($sku_details as $detail){
					if($detail['refund_quantity']>0 && $detail['return_quantity']>=$detail['refund_quantity'] && $detail['refund_quantity']<=$detail['available_to_refund_qty']){
						$item_details[]=array('sku'=>$detail['merchant_sku'],'refund_qty'=>$detail['refund_quantity']);
						$return_shipping_cost=0;
						$return_shipping_tax=0;
						$return_tax=0;
						if($detail['return_shipping_cost']!="" && is_numeric ($detail['return_shipping_cost'])){
								$return_shipping_cost=(float)trim($detail['return_shipping_cost']);
						}
						if($detail['return_tax']!="" && is_numeric ($detail['return_tax'])){
								$return_tax=(float)trim($detail['return_tax']);
						}
						if($detail['return_shipping_tax']!="" && is_numeric ($detail['return_shipping_tax'])){
								$return_shipping_tax=(float)trim($detail['return_shipping_tax']);
						}
						$shipping_amount=$shipping_amount+$return_shipping_cost+$return_shipping_tax;
						$adjustment_positive=$adjustment_positive+$return_tax;
					}
			}
			$collection="";
			$magento_order_id='';
			$collection=Mage::getModel('jet/jetorder')->getCollection();
			$collection->addFieldToFilter( 'merchant_order_id', $merchant_order_id );
			if($collection->count()>0){
				foreach($collection as $coll){
							$magento_order_id=$coll->getData('magento_order_id');
							break;
				}	
			}
			if($magento_order_id !=''){
				try {
						$order ="";
						$order = Mage::getModel('sales/order')->loadByIncrementId($magento_order_id);
						if (!$order->getId()) {
							Mage::getSingleton('adminhtml/session')->addError("Order not Exists.Can't generate Credit Memo.");
							return false;
							
						}
						$data=array();
						$data['shipping_amount']=0;
						$data['adjustment_positive']=0;
						if($shipping_amount>0){
								$data['shipping_amount']=$shipping_amount;
						}
						if($adjustment_positive>0){
								$data['adjustment_positive']=$adjustment_positive;
						}
						//$data['adjustment_positive']=1;
						//$data['adjustment_negative']=2;
						foreach ($item_details as $key => $value) {
							$orderItem="";
							$orderItem = $order->getItemsCollection()->getItemByColumnValue('sku', $value['sku']);
							$data['qtys'][$orderItem->getId()]=$value['refund_qty'] ;
						}

						if(!array_key_exists("qtys",$data)){
							Mage::getSingleton('adminhtml/session')->addError("Problem in Credit Memo Data Preparation.Can't generate Credit Memo.");
							return false;
						}
						if($data['shipping_amount']==0)
					                {
					                	Mage::getSingleton('adminhtml/session')->addError("Amount is 0 .So Credit Memo Not Generated.");
										
					                }
					                else
					                {
					                	$creditmemo_api="";
										$creditmemo_id="";
										$creditmemo_api=Mage::getModel('sales/order_creditmemo_api');
										$comment="";
										$comment="Credit memo generated from Jet.com refund functionality.";
										$creditmemo_id=$creditmemo_api->create($magento_order_id,$data,$comment);
										if($creditmemo_id !="")
										{
												Mage::getSingleton('adminhtml/session')->addSuccess("Credit Memo ".$creditmemo_id." is Successfully generated for Order :".$magento_order_id.".");
												return true;
										}
					                }
						
				}
				catch (Mage_Core_Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage().".Can't generate Credit Memo.");
					return false;
					
				}

			}else{
				Mage::getSingleton('adminhtml/session')->addError("Order not found.Can't generate Credit Memo.");
				return false;
				
			}
		}else{
			Mage::getSingleton('adminhtml/session')->addError("Can't generate Credit Memo.");
			return false;
		}
	}
	

	public function getMagentoIncrementOrderId($merchant_order_id=""){
				$merchant_order_id=trim($merchant_order_id);
				if($merchant_order_id==""){
						return 0;
				}
				try{
						
						$collection=Mage::getModel('jet/jetorder')->getCollection();
						$collection->addFieldToFilter( 'merchant_order_id', $merchant_order_id );
						if($collection->count()>0){
							foreach($collection as $coll){
										$magento_order_id=$coll->getData('magento_order_id');
										return $magento_order_id;
							}	
						}
						return 0;
				}catch(Exception $e){
						return 0;
				}
				
	}
	public function getRefundedQtyInfo($order="",$item_sku=""){
			$item_sku=trim($item_sku);
			$check=array();
			$check['error']=1;
			if($order==""){
					$check['error_msg']="Order not found for current item.";
					return $check;
			}
			if($item_sku==""){
					$check['error_msg']="Item Sku not found for current item.";
					return $check;
			}
			if($order instanceof Mage_Sales_Model_Order){
					$orderItem="";
					$orderItem = $order->getItemsCollection()->getItemByColumnValue('sku',$item_sku);
					if($orderItem instanceof Mage_Sales_Model_Order_Item){
							$qty_ordered=0;
							$qty_refunded=0;
							$qty_ordered=(int)$orderItem->getData('qty_shipped');
							$qty_refunded=(int)$orderItem->getData('qty_refunded');
							$available_to_refund_qty=0;
							$available_to_refund_qty=$qty_ordered-$qty_refunded;
							if($available_to_refund_qty<=0){
									$check['error_msg']="No Qty available to refund for current item.";
									return $check;
							}
							else{
								$check['error']=0;
								$check['qty_already_refunded']=$qty_refunded;
								$check['available_to_refund_qty']=$available_to_refund_qty;
								$check['qty_ordered']=$qty_ordered;
								return $check;
							}
					}else{
						$check['error_msg']="Item Data not available for current item.";
						return $check;
					}
			}else{
				$check['error_msg']="Order Data not available for current item.";
				return $check;
			}
	}

	public function prepareDataAfterSubmitReturn($details_saved_after="",$id=""){
				$skus="";
				$skus=$details_saved_after['sku_details'];
				$returnModel ="";
				$returnModel = Mage::getModel('jet/jetreturn')->load($id);
				$return_ser_data="";
	   			$return_ser_data=$returnModel->getData('return_details');
	   			$return_data="";
	   			$return_data=unserialize($return_ser_data);
	   			$magento_order_id=0;
	   			$magento_order_id=$this->getMagentoIncrementOrderId($return_data->merchant_order_id);
	   			$order ="";
				$order = Mage::getModel('sales/order')->loadByIncrementId($magento_order_id);
	   			

				$result=array();
				$result['id']=$returnModel->getData('id');
	   			$result['returnid']=$returnModel->getData('returnid');
	   			$result['merchant_order_id']=$return_data->merchant_order_id;
	   			$result['agreeto_return']=$details_saved_after['agreeto_return'];
				$i=0;
				foreach($return_data->return_merchant_SKUs as $sku_detail){
							$check=array();
			   				$check=$this->getRefundedQtyInfo($order,$sku_detail->merchant_sku);
			   				if($check['error']=='1'){
			   						continue;
			   				}
							$flag=false;
							foreach($skus as $key=>$detail){
										if($sku_detail->merchant_sku==$detail['merchant_sku'] && $detail['want_to_return'] =='1'){
											$result['sku_details']["sku$i"]['refund_quantity']=trim($detail['refund_quantity']);	
											$result['sku_details']["sku$i"]['return_refundfeedback']=trim($detail['return_refundfeedback']);
											$result['sku_details']["sku$i"]['return_actual_principal']=trim($detail['return_actual_principal']);
											$result['sku_details']["sku$i"]['want_to_return']=$detail['want_to_return'];
							   				$result['sku_details']["sku$i"]['changes_made']=0;
							   				$result['sku_details']["sku$i"]['qty_already_refunded']=$detail['qty_already_refunded'];
							   				$result['sku_details']["sku$i"]['available_to_refund_qty']=$detail['available_to_refund_qty'];
							   				$result['sku_details']["sku$i"]['qty_ordered']=$detail['qty_ordered'];
							   				$result['sku_details']["sku$i"]['order_item_id']=$detail['order_item_id'];
							   				$result['sku_details']["sku$i"]['return_quantity']=$detail['return_quantity'];
							   				$result['sku_details']["sku$i"]['merchant_sku']=$detail['merchant_sku'];
							   				$result['sku_details']["sku$i"]['return_principal']=trim($detail['return_principal']);
							   				$result['sku_details']["sku$i"]['return_tax']=trim($detail['return_tax']);
							   				$result['sku_details']["sku$i"]['return_shipping_cost']=trim($detail['return_shipping_cost']);
							   				$result['sku_details']["sku$i"]['return_shipping_tax']=trim($detail['return_shipping_tax']);
							   				$flag=true;
											break;
										}
							}
					if($flag){
							$i++;
							continue;
					}	
					$result['sku_details']["sku$i"]['refund_quantity']=0;	
					$result['sku_details']["sku$i"]['return_refundfeedback']="";
					$result['sku_details']["sku$i"]['return_actual_principal']=$sku_detail->requested_refund_amount->principal;
					$result['sku_details']["sku$i"]['want_to_return']=0;
	   				$result['sku_details']["sku$i"]['changes_made']=0;
	   				$result['sku_details']["sku$i"]['qty_already_refunded']=$check['qty_already_refunded'];
	   				$result['sku_details']["sku$i"]['available_to_refund_qty']=$check['available_to_refund_qty'];
	   				$result['sku_details']["sku$i"]['qty_ordered']=$check['qty_ordered'];
	   				$result['sku_details']["sku$i"]['order_item_id']=$sku_detail->order_item_id;
	   				$result['sku_details']["sku$i"]['return_quantity']=$sku_detail->return_quantity;
	   				$result['sku_details']["sku$i"]['merchant_sku']=$sku_detail->merchant_sku;
	   				$result['sku_details']["sku$i"]['return_principal']=$sku_detail->requested_refund_amount->principal;
	   				$result['sku_details']["sku$i"]['return_tax']=$sku_detail->requested_refund_amount->tax;
	   				$result['sku_details']["sku$i"]['return_shipping_cost']=$sku_detail->requested_refund_amount->shipping_cost;
	   				$result['sku_details']["sku$i"]['return_shipping_tax']=$sku_detail->requested_refund_amount->shipping_tax;
	   				$i++;
	   			}
	   			return $result;
	}
	public function saveChangesMadeValue($details_saved_after=""){
			$skus="";
			$skus=$details_saved_after['sku_details'];
			foreach($skus as $key=>$detail){
					if($detail['want_to_return'] =='1'){
						$details_saved_after['sku_details'][$key]['changes_made']=1;
					}
			}
			return $details_saved_after;
	}
	public function checkOrderInRefund($merchant_order_id=""){
			$merchant_order_id=trim($merchant_order_id);
			try{
						$collection="";
						$collection=Mage::getModel('jet/jetrefund')->getCollection()->addFieldToFilter('refund_orderid', $merchant_order_id );
						if($collection->count()>0){
							return true;
						}
						return false;
				}catch(Exception $e){
						return false;
				}
	}
	public function checkViewCaseForReturn($details_saved_after=""){
			$skus="";
			$skus=$details_saved_after['sku_details'];
			$count=0;
			$count=count($skus);
			$i=0;
			foreach($skus as $key=>$detail){
					if($detail['changes_made'] =='1'){
						$i++;
					}
			}
			if($count >0 && $count==$i){
					return true;
			}
			return false;
	}

	public function generateCreditMemoForReturn($return_id=''){
		if($return_id !=""){
				$model ="";
				$orderId ="";
				$magento_order_id="";
				$details_after_saved="";
				$return_details="";
				$details_after_saved_result=array();
				$model = Mage::getModel('jet/jetreturn')->load($return_id);
				$return_details=$model->getData('return_details');
				$details_after_saved=$model->getData('details_saved_after');
				$details_after_saved_result=unserialize($details_after_saved);
				if(count($details_after_saved_result)==0){
					Mage::getSingleton('adminhtml/session')->addError("Details of Return Submission are not saved.Can't generate Credit Memo.");
					return false;
				}
				$sku_details="";
				$sku_details=$details_after_saved_result['sku_details'];
				$item_details=array();
				$shipping_amount=0;
				$adjustment_positive=0;
				foreach($sku_details as $detail){
						if($detail['changes_made']>0 && $detail['refund_quantity']>0 && $detail['return_quantity']>=$detail['refund_quantity'] && $detail['refund_quantity']<=$detail['available_to_refund_qty']){
							$item_details[]=array('sku'=>$detail['merchant_sku'],'refund_qty'=>$detail['refund_quantity']);
							$return_shipping_cost=0;
							$return_shipping_tax=0;
							$return_tax=0;
							if($detail['return_shipping_cost']!="" && is_numeric ($detail['return_shipping_cost'])){
									$return_shipping_cost=(float)trim($detail['return_shipping_cost']);
							}
							if($detail['return_tax']!="" && is_numeric ($detail['return_tax'])){
									$return_tax=(float)trim($detail['return_tax']);
							}
							if($detail['return_shipping_tax']!="" && is_numeric ($detail['return_shipping_tax'])){
									$return_shipping_tax=(float)trim($detail['return_shipping_tax']);
							}
							$shipping_amount=$shipping_amount+$return_shipping_cost+$return_shipping_tax;
							$adjustment_positive=$adjustment_positive+$return_tax;
						}
				}
				$return_details=unserialize($return_details);
				if( !is_object ($return_details)){
						Mage::getSingleton('adminhtml/session')->addError("Details of Return are not saved.Can't generate Credit Memo.");
						return false;
						
				}
				$merchant_order_id="";
				$merchant_order_id=$return_details->merchant_order_id;
				$collection="";
				$collection=Mage::getModel('jet/jetorder')->getCollection();
				$collection->addFieldToFilter( 'merchant_order_id', $merchant_order_id );
				if($collection->count()>0){
					foreach($collection as $coll){
								$magento_order_id=$coll->getData('magento_order_id');
								break;
					}	
				}
				if($magento_order_id !=''){
							try {
									$order ="";
					                $order = Mage::getModel('sales/order')->loadByIncrementId($magento_order_id);
						            if (!$order->getId()) {
						            	Mage::getSingleton('adminhtml/session')->addError("Order not Exists.Can't generate Credit Memo.");
										return false;
						                
						            }
						            $data=array();
					                $data['shipping_amount']=0;
					                $data['adjustment_positive']=0;
					                if($shipping_amount>0){
					                		$data['shipping_amount']=$shipping_amount;
					                }
					                if($adjustment_positive>0){
					                		$data['adjustment_positive']=$adjustment_positive;
					                }
					                //$data['adjustment_positive']=1;
					                //$data['adjustment_negative']=2;
					                foreach ($item_details as $key => $value) {
					                	$orderItem="";
					                	$orderItem = $order->getItemsCollection()->getItemByColumnValue('sku', $value['sku']);
					                	$data['qtys'][$orderItem->getId()]=$value['refund_qty'] ;
					                }
					                
					                if(!array_key_exists("qtys",$data)){
					                	Mage::getSingleton('adminhtml/session')->addError("Problem in Credit Memo Data Preparation.Can't generate Credit Memo.");
										return false;
					                }
					                
					                if($data['shipping_amount']==0)
					                {
					                	Mage::getSingleton('adminhtml/session')->addError("Amount is 0 .So Credit Memo Not Generated.");
										
					                }
					                else
					                {
					                	$creditmemo_api="";
					                $creditmemo_id="";
					                $creditmemo_api=Mage::getModel('sales/order_creditmemo_api');
					                $comment="";
					                $comment="Credit memo generated from Jet.com return functionality.";
					                $creditmemo_id=$creditmemo_api->create($magento_order_id,$data,$comment);
					                if($creditmemo_id !=""){
					                		Mage::getSingleton('adminhtml/session')->addSuccess("Credit Memo ".$creditmemo_id." is Successfully generated for Order :".$magento_order_id.".");
											return true;
					                	}	
					                }
					                
					        }
					        catch (Mage_Core_Exception $e) {
					        	Mage::getSingleton('adminhtml/session')->addError($e->getMessage().".Can't generate Credit Memo.");
								return false;
					            
					        }

				}else{
					Mage::getSingleton('adminhtml/session')->addError("Order not found.Can't generate Credit Memo.");
					return false;
					
				}
						
			}else{
				Mage::getSingleton('adminhtml/session')->addError("Can't generate Credit Memo.");
				return false;
				
			}
	}
	public function checkOrderForReturn($merchant_order_id=""){
		$merchant_order_id=trim($merchant_order_id);
		$collection=Mage::getModel('jet/jetreturn')->getCollection();
		$collection->addFieldToFilter( 'merchant_order_id', $merchant_order_id );
		if($collection->count()>0){
			foreach($collection as $coll){
						$magento_order_id=$coll->getData('magento_order_id');
						return true;
			}	
		}
		return false;
	}
}	
