<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */

class Amasty_SeoRichData_Helper_Data extends Mage_Core_Helper_Abstract
{
    const HTTP_FORMAT = 'https';

    public function isYotpoReviewsEnabled()
    {
        return Mage::helper('core')->isModuleEnabled('Yotpo_Yotpo');
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param int $price
     * @return int
     */
    public function getProductPrice($product, $price = null)
    {
        if (is_null($price)) {
            $price = $product->getFinalPrice();
        }

        if (Mage::getStoreConfig('amseorichdata/product/price_incl_tax')) {
            /** @var Mage_Tag_Helper_Data $taxHelper */
            $taxHelper = Mage::helper('tax');
            $price = $taxHelper->getPrice($product, $price, true);
        }

        if ($product->getTypeId() == 'grouped') {
            $price = $this->getGroupedPrice($product);
        }

        return $price;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return int
     */
    public function getGroupedPrice($product)
    {
        $ogPrice = 0;

        $product->load($product->getId());
        $_associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);

        foreach($_associatedProducts as $_associatedProduct) {
            if($ogPrice = $_associatedProduct->getPrice()) {
                $ogPrice = $_associatedProduct->getPrice();
            }
        }

        //$ogPrice = number_format((float)$ogPrice, 2, '.', '');

        return $ogPrice;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getProductDescription($product)
    {
        $description = $product->getShortDescription();

        if (Mage::getStoreConfig('amseorichdata/product/use_short_description') == false) {
            $description = $product->getDescription();
        }

        return $description;
    }
}
