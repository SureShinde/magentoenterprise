<?php
/**
 * @category   CLS
 * @package    ProductCompare
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

require_once Mage::getModuleDir('controllers', 'Mage_Catalog').DS.'Product'.DS.'CompareController.php';

class CLS_ProductCompare_Catalog_Product_CompareController extends Mage_Catalog_Product_CompareController {

    /**
     * Add item to compare list, and return json response
     */
    public function ajaxAddAction()
    {
        $productId = (int) $this->getRequest()->getParam('product');
        if ($productId
            && (Mage::getSingleton('log/visitor')->getId() || Mage::getSingleton('customer/session')->isLoggedIn())
        ) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            // Set initial response
            $response = array(
                'activity' => 'add',
                'success'=> 0,
                'summary_update'=>'',
            );

            if ($product->getId()/* && !$product->isSuper()*/) {
                Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
                Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
                // Change response to successful
                $response['success'] = 1;
            }

            $response['summary_update'] = $this->_getCompareTopbarHtml();

            // Return json
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        }
    }


    /**
     * Remove item from compare list, and return json response
     */
    public function ajaxRemoveAction()
    {
        if ($productId = (int) $this->getRequest()->getParam('product')) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            // Set initial response
            $response = array(
                'activity' => 'remove',
                'success'=> 0,
                'summary_update'=>'',
            );

            if($product->getId()) {
                $item = Mage::getModel('catalog/product_compare_item');
                if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
                } elseif ($this->_customerId) {
                    $item->addCustomerData(
                        Mage::getModel('customer/customer')->load($this->_customerId)
                    );
                } else {
                    $item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
                }

                $item->loadByProduct($product);

                if($item->getId()) {
                    $item->delete();
                    Mage::dispatchEvent('catalog_product_compare_remove_product', array('product'=>$item));
                }

                // Change response to successful, doesn't matter if the product was actually found or not
                $response['success'] = 1;
            }

            $response['summary_update'] = $this->_getCompareTopbarHtml();

            // Return json
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        }
    }

    /**
     * Get HTML for the product compare top bar
     *
     * @return string
     */
    protected function _getCompareTopbarHtml()
    {
        $block = Mage::app()->getLayout()->createBlock('catalog/product_compare_sidebar');
        $block->setTemplate('cls/productcompare/topbar.phtml');
        return $block->toHtml();
    }
}
