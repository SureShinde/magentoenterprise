<?php
 /**
 * CedCommerce
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Cedcommerce.com license and  can be accessed 
 * through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to the file if you wish to upgrade this extension to newer
 * version in the future.
 * @category    Ced
 * @package     Ced_Jet
 * @author      CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 */


class Ced_Jet_Helper_Jet extends Mage_Core_Helper_Abstract{

    public function createuploadDir(){
        if(!file_exists(Mage::getBaseDir("var") . DS . "jetupload")) {
            mkdir(Mage::getBaseDir("var") . DS . "jetupload", 0777, true);
        }
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

    public function _getConnection($type = 'core_read'){
        return Mage::getSingleton('core/resource')->getConnection($type);
    }

    public function _getTableName($tableName){
        return Mage::getSingleton('core/resource')->getTableName($tableName);
    }

    public function _getIdFromSku($sku){
        $connection = $this->_getConnection('core_read');
        $sql        = "SELECT entity_id , type_id FROM " . $this->_getTableName('catalog_product_entity') . " WHERE sku = ?";
        return $connection->fetchRow($sql, array($sku));
    }

    public function _getSkuQty($id){
        $connection = $this->_getConnection('core_read');
        $sql        = "SELECT product.sku , stock.qty FROM " . $this->_getTableName('catalog_product_entity') . " AS product INNER JOIN ".$this->_getTableName('cataloginventory_stock_item')." AS stock ON product.entity_id=stock.product_id AND product.entity_id=".$id;
        return $connection->fetchRow($sql);
    }

    public function _getchildId($id){
        $connection = $this->_getConnection('core_read');
        $sql        = "SELECT productlink.product_id FROM " . $this->_getTableName('catalog_product_super_link') . " AS productlink WHERE parent_id=".$id;
        return $connection->fetchAll($sql);
    }

    public function _getTypeFromId($id){
        $connection = $this->_getConnection('core_read');
        $sql        = "SELECT products.type_id FROM " . $this->_getTableName('catalog_product_entity') . " AS products WHERE entity_id=".$id;
        return $connection->fetchRow($sql);
    }

    public function getUpdatedRefundQty($merchant_order_id){
        $refundcollection=Mage::getModel('jet/jetrefund')->getCollection()->addFieldToFilter('refund_orderid', $merchant_order_id );
        $refund_qty=array();
        if($refundcollection->count()>0){
            foreach($refundcollection as $coll){
                $refund_data = unserialize($coll->getData('saved_data'));
                foreach($refund_data['sku_details'] as $sku=>$data){
                    $refund_qty[$data['merchant_sku']]+=$data['refund_quantity'];
                }
            }
        }
        return $refund_qty;
    }

    public function getUpdatedReturnQty($merchant_order_id){
        $returncollection=Mage::getModel('jet/jetreturn')->getCollection()->addFieldToFilter('returnid', $merchant_order_id );
        $return_qty=array();
        if($returncollection->count()>0){
            foreach($returncollection as $coll){
                $return_data = unserialize($coll->getData('saved_data'));
                foreach($return_data['sku_details'] as $sku=>$data){
                    $return_qty[$data['merchant_sku']]+=$data['return_quantity'];
                }
            }
        }
        return $return_qty;
    }

    public function Jarray_merge($child_arr , $parent_arr){
        if (version_compare(phpversion(), '5.5.0', '<')===true) {
            return  array_replace($child_arr, $parent_arr);
        }else{
            return  array_merge($child_arr, $parent_arr);
        }
    }
    
    
    /*
    * Price calculations
    */

    public function getJetPrice($product){
        
        $price = (float)$product->getFinalPrice();
        $config_price = trim(Mage::getStoreConfig('jet_options/productinfo_map/jet_product_price'));

        switch ($config_price){
            case 'plus_fixed':
                $fixed_price = trim(Mage::getStoreConfig('jet_options/productinfo_map/jprice'));
                if(is_numeric($fixed_price) && ($fixed_price != '') && ($fixed_price != null) ){
                    $fixed_price = (float)$fixed_price;
                    if($fixed_price>0){
                        $price= (float) ($price + $fixed_price);
                    }
                }
                return $price;
                break;
            
            case 'plus_per':
                $percent_price = trim(Mage::getStoreConfig('jet_options/productinfo_map/jpriceper'));
                if(is_numeric($percent_price)){
                    $percent_value = (float)$percent_price;
                    if($percent_value>0){
                        $price= (float) ($price + (($price/100)*$percent_value));
                    }
                }
                return $price;
                break;
            
            case 'min_fixed':
                $fixed_price = trim(Mage::getStoreConfig('jet_options/productinfo_map/jpricedec'));
                if(is_numeric($fixed_price) && ($fixed_price != '') && ($fixed_price != null) ){
                    $fixed_price = (float)$fixed_price;
                    if($fixed_price>0){
                        $price= (float) ($price - $fixed_price);
                    }
                }
                return $price;
                break;

            case 'min_per':
                $percent_price = trim(Mage::getStoreConfig('jet_options/productinfo_map/jpriceperdec'));
                if(is_numeric($percent_price)){
                    $percent_value = (float)$percent_price;
                    if($percent_value>0){
                        $price= (float) ($price - (($price/100)*$percent_value));
                    }
                }
                return $price;
                break;
            
           case 'differ':
                $custom_price_attr = trim(Mage::getStoreConfig('jet_options/productinfo_map/j_diff_pricep'));
                try{
                    $cprice = (float)$product -> getData($custom_price_attr);
                }catch (Exception $e){}
                $price = (($cprice != 0) && is_numeric($cprice))? $cprice : $price ;
                return $price;
                break;

            default:
                return (float)$price;
            
        }
    }

    /* calculate jet shipment qty and cancel qty
       @return : array
    */
     public function getShipped_Cancelled_Qty($order_model_data){
       $shipData=unserialize($order_model_data->getData('shipment_data'));
       if($shipData){
           $ship_items_info = array();
           $orderData = $this->getOrdered_Cancelled_Qty($order_model_data);
           foreach ($shipData["shipments"] as $sdata) {
               foreach ($sdata["shipment_items"] as $items) {
                       $ship_items_info[$items['merchant_sku']]['response_shipment_cancel_qty'] += $items['response_shipment_cancel_qty'];
                       $ship_items_info[$items['merchant_sku']]['response_shipment_sku_quantity'] += $items['response_shipment_sku_quantity'];
               }
           }
           return $ship_items_info;
       }
       else{
         $temp_data = $order_model_data->getData();
           if(count($temp_data) >0 ){
             $shipData = unserialize($temp_data[0]['shipment_data']);
             if($shipData){
                 $ship_items_info = array();
                 foreach ($shipData["shipments"] as $sdata) {
                     foreach ($sdata["shipment_items"] as $items) {
                         $ship_items_info[$items['merchant_sku']]['response_shipment_cancel_qty'] += $items['response_shipment_cancel_qty'];
                         $ship_items_info[$items['merchant_sku']]['response_shipment_sku_quantity'] += $items['response_shipment_sku_quantity'];
                     }
                 }
                 return $ship_items_info;
             }
             else{
                 return false;
             }
         }
         else{
             return false;
         }
       }

     }

    public function getOrdered_Cancelled_Qty($order_model_data){
        $orderData=unserialize($order_model_data->getData('order_data'));
        if($orderData) {
            $order_items_info = array();
            foreach ($orderData->order_items as $sdata) {
                    $order_items_info[$sdata->merchant_sku]['request_sku_quantity'] += $sdata->request_order_quantity;
                    $order_items_info[$sdata->merchant_sku]['request_cancel_qty'] += $sdata->request_order_cancel_qty;
            }
            return $order_items_info;
        }elseif(!$orderData){
            $tempData = $order_model_data->getData();
            $orderData = unserialize($tempData[0]["order_data"]);
            if(count($orderData) > 0){
                $order_items_info = array();
                foreach ($orderData->order_items as $sdata) {
                    $order_items_info[$sdata->merchant_sku]['request_sku_quantity'] += $sdata->request_order_quantity;
                    $order_items_info[$sdata->merchant_sku]['request_cancel_qty'] += $sdata->request_order_cancel_qty;
                }
                return $order_items_info;
            }
        }
        else{
            return false;
        }
    }

    public function validateShipment($ordered_qty , $cancel_qty , $prev_shipped_qty , $prev_cancelled_qty , $merchant_ship_qty , $merchant_cancel_qty , $sku){
        $total_cancel_qty = $prev_cancelled_qty + $merchant_cancel_qty - $cancel_qty ;
        $total_shipped_qty = $prev_shipped_qty + $merchant_ship_qty;
        $available_ship_qty = $ordered_qty -($total_cancel_qty + $prev_shipped_qty);
        $msg = '';

        if($available_ship_qty >= 0){$msg = "clear";}
        else{$msg = "Error for sku : ".$sku." .Total cancelled + shipped qty cannot be greater than ordered quantity . Already Shipped qty : ".$prev_shipped_qty." . Already Cancelled qty : ".$prev_cancelled_qty; }

        return $msg;
    }
    public function getChildPrice($pid){
        if(is_numeric($pid))
            $product = Mage::getModel('catalog/product')->load($pid);
        if($product instanceof Mage_Catalog_Model_Product){
            if($product->getTypeId() == "configurable"){
                $childPrices=array();
                $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
                $basePrice = $product->getFinalPrice();

                foreach ($attributes as $attribute){
                    $prices = $attribute->getPrices();
                    foreach ($prices as $price){
                        if ($price['is_percent']){ //if the price is specified in percents
                            $pricesByAttributeValues[$price['value_index']] = (float)$price['pricing_value'] * $basePrice / 100;
                        }
                        else { //if the price is absolute value
                            $pricesByAttributeValues[$price['value_index']] = (float)$price['pricing_value'];
                        }
                    }
                }
                $simple = $product->getTypeInstance()->getUsedProducts();
                foreach ($simple as $sProduct){
                    $totalPrice = $basePrice;

                    foreach ($attributes as $attribute){
                        $value = $sProduct->getData($attribute->getProductAttribute()->getAttributeCode());
                        if (isset($pricesByAttributeValues[$value])){
                            $totalPrice += $pricesByAttributeValues[$value];
                        }
                    }
                    $totalPrice = $this-> getJetPriceConfig($sProduct ,$totalPrice);
                    $childPrices[$sProduct->getSku()]= round($totalPrice, 2);
                }
                return $childPrices;
            }else{
                return false;
            }
        }
    }

    public function getJetPriceConfig($product , $price ){

        //$price = (float)$product->getFinalPrice();
        $config_price = trim(Mage::getStoreConfig('jet_options/productinfo_map/jet_product_price'));

        switch ($config_price){
            case 'plus_fixed':
                $fixed_price = trim(Mage::getStoreConfig('jet_options/productinfo_map/jprice'));
                if(is_numeric($fixed_price) && ($fixed_price != '') && ($fixed_price != null) ){
                    $fixed_price = (float)$fixed_price;
                    if($fixed_price>0){
                        $price= (float) ($price + $fixed_price);
                    }
                }
                return $price;
                break;

            case 'plus_per':
                $percent_price = trim(Mage::getStoreConfig('jet_options/productinfo_map/jpriceper'));
                if(is_numeric($percent_price)){
                    $percent_value = (float)$percent_price;
                    if($percent_value>0){
                        $price= (float) ($price + (($price/100)*$percent_value));
                    }
                }
                return $price;
                break;

            case 'min_fixed':
                $fixed_price = trim(Mage::getStoreConfig('jet_options/productinfo_map/jpricedec'));
                if(is_numeric($fixed_price) && ($fixed_price != '') && ($fixed_price != null) ){
                    $fixed_price = (float)$fixed_price;
                    if($fixed_price>0){
                        $price= (float) ($price - $fixed_price);
                    }
                }
                return $price;
                break;

            case 'min_per':
                $percent_price = trim(Mage::getStoreConfig('jet_options/productinfo_map/jpriceperdec'));
                if(is_numeric($percent_price)){
                    $percent_value = (float)$percent_price;
                    if($percent_value>0){
                        $price= (float) ($price - (($price/100)*$percent_value));
                    }
                }
                return $price;
                break;

            case 'differ':
                $custom_price_attr = trim(Mage::getStoreConfig('jet_options/productinfo_map/j_diff_pricep'));
                try{
                    $cprice = $product -> getData($custom_price_attr);
                }catch (Exception $e){}
                $price = (($cprice != 0) && is_numeric($cprice))? $cprice : $price ;
                return $price;
                break;

            default:
                return (float)$price;

        }
    }
        public function getJetShipCarrier($shipstationCarrier){
                $carrier = '';
                $s_carriers = array("FedEx" , "FedEx SmartPost" , "FedEx Freight" , "UPS" , "UPS Freight" , "UPS Mail Innovations" , "UPS SurePost" , "OnTrac" , "OnTrac Direct Post" ,
                    "DHL" , "DHL Global Mail" , "USPS" , "CEVA" , "Laser Ship" , "Spee Dee" , "A Duie Pyle" , "A1" , "ABF" , "APEX" ,
                    "Averitt" , "Dynamex" , "Eastern Connection" , "Ensenda" , "Estes" , "Land Air Express" , "Lone Star" , "Meyer" ,
                    "New Penn" , "Pilot" , "Prestige" , "RBF" , "Reddaway" , "RL Carriers" , "Roadrunner" , "Southeastern Freight" ,
                    "UDS" , "UES" , "YRC" , "GSO" , "A&M Trucking" , "SAIA Freight" , "Other" );

                foreach($s_carriers as $s_carrier){
                    if(!strcasecmp($s_carrier , $shipstationCarrier)){
                        $carrier = $s_carrier;
                        break;
                    }
                }
                $carrier = ($carrier == '') ? 'Other' : $carrier ;
                return $carrier;
            }

}