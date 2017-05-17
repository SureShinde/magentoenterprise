<?php
/**
 * Data.php
 *
 * @category    CLS
 * @package     Custom
 * @author      Chris Huffman <chris.huffman@classyllama.com>
 * @copyright   Copyright (c) 2015 Classy Llama Studios, LLC
 */

class CLS_Custom_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_CHARACTER_LIMIT = 'catalog/product_view_display/character_count';
    const CONFIG_PATH_BADGE_URL = 'catalog/badges/badge_url_mapping';
    const CONFIG_PATH_FREE_SHIPPING_MESSAGE = 'shipping/option/free_shipping_message';
    const CONFIG_PATH_FREE_SHIPPING_MESSAGE_ADDITIONAL = 'shipping/option/free_shipping_message_additional';
    const CONFIG_PATH_FREE_SHIPPING_LIMIT = 'shipping/option/free_shipping_limit';
    const CONFIG_PATH_ACCOUNT_CREATE_CONVERSION_LABEL = 'google/adwords/account_create_conversion_label';
    const CONFIG_PATH_NEWSLETTER_SIGNUP_CONVERSION_LABEL = 'google/adwords/newsletter_signup_conversion_label';
    const CONFIG_PATH_CONTACT_FORM_SUBMIT_CONVERSION_LABEL = 'google/adwords/contact_submit_conversion_label';
    
    const BULKY_ITEM_IN_CART = 'cls_bulky_item_in_cart';

    /**
     * Return an array of the blog tags saved to a product
     *
     * @param $product
     * @return array
     */
    public function getProductTags($product)
    {
        $tags = $product->getBlogTags();

        return array_map('trim', explode(',', $tags));
    }

    /**
     * Return the character limit from system config
     *
     * @param null $store
     * @return mixed
     */
    public function getCharacterLimit($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_CHARACTER_LIMIT, $store);
    }
    

    /**
     * Return Google Analytics conversion label
     *
     * @param null $store
     * @return string
     */
    public function getAccountCreateLabel($store = null)
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_ACCOUNT_CREATE_CONVERSION_LABEL, $store);
    }

    /**
     * Return Google Analytics conversion label
     *
     * @param null $store
     * @return string
     */
    public function getNewsletterSignupLabel($store = null)
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_NEWSLETTER_SIGNUP_CONVERSION_LABEL, $store);
    }

    /**
     * Return Google Analytics conversion label
     *
     * @param null $store
     * @return string
     */
    public function getContactSubmitLabel($store = null)
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_CONTACT_FORM_SUBMIT_CONVERSION_LABEL, $store);
    }


    /**
     * @param $badgeName
     * @param null $store
     * @return string
     */
    public function getBadgeUrlMapping($badgeName, $store = null)
    {
        //get mapping from config
        $mapping = Mage::getStoreConfig(self::CONFIG_PATH_BADGE_URL, $store);

        $mappings = preg_split("/\r\n|\n|\r/", $mapping);

        $badges = array();

        //parse mapping into array indexed by badge name
        foreach ($mappings as $map) {
            $badge = explode(':', trim($map));
            $badges[$badge[0]] = $badge[1];
        }

        //check if badgeName is in the mapping
        if (isset($badges[$badgeName])) {
            //return url
            return Mage::getBaseUrl() . $badges[$badgeName];
        }

        return '';
    }

    /**
     * Build the free shipping message and return correct message
     *
     * @param $total
     * @param null $store
     * @return mixed|string
     */
    public function getFreeShippingMessage($total, $items, $store = null)
    {
        $shippingLimit = Mage::getStoreConfig(self::CONFIG_PATH_FREE_SHIPPING_LIMIT, $store);
        $shippingMessage = Mage::getStoreConfig(self::CONFIG_PATH_FREE_SHIPPING_MESSAGE, $store);
        
        foreach($items as $item) {
           if($this->isLargeGroup($item->getProduct())) {               
               $total -= $item->getRowTotal();
               Mage::register(self::BULKY_ITEM_IN_CART, true, true);
           }            
        }
       
        $amountToFreeShipping = $shippingLimit - $total;

        if ($amountToFreeShipping > 0) {
            return $this->__($shippingMessage, Mage::helper('checkout')->formatPrice($amountToFreeShipping));
        } else {
            return $this->__('Your order qualifies for free shipping.');
        }
    }
    
    /**
     * Get an additional message below free shipping 
     * if there are large items in the shopping cart
     *
     * @return string
     * @author Andrej Krupenin <andrej.krupenin@classyllama.com>
     */    
    public function getAdditionalShippingMessage()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_FREE_SHIPPING_MESSAGE_ADDITIONAL);
    }

    /**
     * Check if product is in large group
     * @param Mage_Catalog_Model_Product $_product
     * @return boolean
     * @author Andrej Krupenin <andrej.krupenin@classyllama.com>
     */    
    public function isLargeGroup($_product)
    {
        /* shipperhq_shipping_group */
        $shippingGroup = $_product->getResource()
                ->getAttributeRawValue($_product->getId(), 'shipperhq_shipping_group', Mage::app()->getStore());
        if ($shippingGroup) {
            $shippingGroupValues = explode(',', $shippingGroup);
            $shippingGroupAttr = Mage::getModel('eav/entity')->setType('catalog_product')->getAttribute('shipperhq_shipping_group');
            foreach ($shippingGroupAttr->getSource()->getAllOptions(true, true) as $option) {
                if (trim(strtolower($option['label'])) == 'large') {
                    if (in_array($option['value'], $shippingGroupValues)) {
                        return true;
                    }
                }
            }
        }
    }

}