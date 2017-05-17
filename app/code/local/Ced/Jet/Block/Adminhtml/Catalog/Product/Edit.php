<?php
class Ced_Jet_Block_Adminhtml_Catalog_Product_Edit extends Mage_Adminhtml_Block_Catalog_Product_Edit
{
    /**
     * @var Mage_Catalog_Model_Product Product instance
     */
    private $_product;
 
    /**
     * Preparing global layout
     * 
     * @return Ced_Jet_Block_Adminhtml_Catalog_Product_Edit|Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $sku= Mage::registry('current_product')->getSku();
        $id = Mage::registry('current_product')->getId();
       $type =  Mage::registry('current_product')->getTypeId();
       $pro = Mage::registry('current_product');
        $response_simple = array();
        $response_configurable = array();
                    $api_url = Mage::getStoreConfig('jet_options/ced_jet/jet_apiurl');
                   $user = Mage::getStoreConfig('jet_options/ced_jet/jet_user');
                   $pass = Mage::getStoreConfig('jet_options/ced_jet/jet_userpwd');
                   $fullfil = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
                   
                   if($api_url!='' || $user!='' || $pass!='' ||  $fullfil!='')
                   {
                        return $this;
                   }
       if($type == 'configurable')
        {
             $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$pro);
                foreach($childProducts as $chp){

                   $response_configurable[] = Mage::helper('jet')->CGetRequest('/merchant-skus/' . $chp->getSku());
                   
                } 
        }
        if($type == 'simple')
        {
            $response_simple = Mage::helper('jet')->CGetRequest('/merchant-skus/' . $sku);
        }
       
      
      
        if(count(json_decode($response_simple))!=0 || count($response_configurable)!=0)
        {
             
        $this->_product = $this->getProduct();
        $this->setChild('view_on_front',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label'     => Mage::helper('catalog')->__('Sync With Jet'),
                'onclick'   =>  "setLocation('{$this->getUrl('adminhtml/adminhtml_jetedit/jetProductEdit/id/'.$id.'')}')",
                
                'title' => Mage::helper('catalog')->__('Sync Product With Jet')
            ))
        );
 
        
        }
            return $this;
    }
 
    /**
     * Returns duplicate & view on front buttons html
     * 
     * @return string
     */
    public function getDuplicateButtonHtml()
    {
        return $this->getChildHtml('duplicate_button') . $this->getChildHtml('view_on_front');
    }
 
    /**
     * Checking product visibility
     * 
     * @return bool
     */
    private function _isVisible()
    {
        return $this->_product->isVisibleInCatalog() && $this->_product->isVisibleInSiteVisibility();
    }
 
}
?>