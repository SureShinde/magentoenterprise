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
  
class Ced_Jet_Adminhtml_JeteditController extends Mage_Adminhtml_Controller_Action{

public function jetProductEditAction($config_id,$childPrice)
	{
        if($config_id == '')
        {
             $post_id = $this->getRequest()->getParam('id');
        }
       else
       {
            $post_id = $config_id;
       }
       if (empty($childPrice))
       {
            $childPrice = '';
       }

         $arr = array();
        $product = Mage::getModel('catalog/product')->load($post_id);
        
        if ($product->getTypeId() == 'simple') 
        { 
           $this->updateonjet($product,$childPrice);
           if(count($arr)!=0)
           {    
              foreach ($arr as $value) 
               {
                   if($value!= $product->getSku())
                        {
                        Mage::getSingleton('adminhtml/session')->addSuccess("Product Data Successfully Sync With Jet");
                        $this->_redirectReferer();
                        }
               }  
           }
           else
           {
                 Mage::getSingleton('adminhtml/session')->addSuccess("Product Data Successfully Sync With Jet");
                        $this->_redirectReferer();
           }
               
        }
            else if($product->getTypeId()=='configurable')
            {
                $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
                $childPrice = Mage::helper('jet/jet')->getChildPrice($post_id);
                foreach($childProducts as $chp){
                    $arr= $chp->getSku();
                    $this->jetProductEditAction($chp->getId(),$childPrice);
                    }
                Mage::getSingleton('adminhtml/session')->addSuccess("Product Data Successfully Sync With Jet");
                $this->_redirectReferer();
                
            }
    }

    public function updateonjet($product,$childPrice)
    {

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
            $sku = trim($product->getSku());
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
                    //check if product qty is 0 then is archieve true and redirect to previous page 
                    $arr['is_archived'] = false;
                    $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $sku . '/status/archive', json_encode($arr));
                    $response = json_decode($data);
                }
                if ($status) {

                    /*-----all data update code starts----*/
                    $update_image = true;
                    if ($is_update_all == "1") {  
                        $alldataupdate = 0;
                        $alldataupdate = Mage::helper('jet')->createProductOnJet($product,false,'');
                         
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
                        if($childPrice)
                        {
                            $fulfillment_arr[0]['fulfillment_node_id'] = $fullfillmentnodeid; 
                            $fulfillment_arr[0]['fulfillment_node_price'] = $childPrice[$sku];
                            $data_var['price'] = (float)$childPrice[$sku];
                        }
                        else
                        {
                            $fulfillment_arr[0]['fulfillment_node_id'] = $fullfillmentnodeid;
                            $fulfillment_arr[0]['fulfillment_node_price'] = $price;
                            $data_var['price'] = (float)$price;    
                        }
                        
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
    }	
    
}


