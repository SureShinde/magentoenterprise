<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */


class Amasty_SeoSingleUrl_Model_Observer
{
	/**
	 * @param $observer
	 */
	public function addUrlRewriteToSitemap(Varien_Event_Observer $observer)
	{
		$block = $observer->getBlock();
		if ($block instanceof Mage_Catalog_Block_Seo_Sitemap_Product) {
			$block->getCollection()->addUrlRewrite();
		}
	}

    /**
     * @param $observer
     */
    public function addUrlRewriteToUpSell(Varien_Event_Observer $observer)
    {
        $collection = $observer->getCollection();
        if (method_exists($collection, 'addUrlRewrite')) {
            $observer->getCollection()->addUrlRewrite();
        }
    }

    public function preDispatch($observer)
    {
        if (!Mage::getStoreConfigFlag('amseourl/general/force_redirect'))
            return;

        if (!Mage::helper('amseourl')->useDefaultProductUrlRules()
            || !Mage::getStoreConfig(Mage_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_USE_CATEGORY)) {
            $request = Mage::app()->getRequest();
            /** @var Varien_Db_Select $select */
            $select = Mage::helper('amseourl/product_url_rewrite')
                ->getTableSelect(
                    array($request->getParam('id')),
                    null,
                    Mage::app()->getStore()->getId(),
                    array('request_path')
                )->limit(1);

            $canonicalPathConnection = Mage::getSingleton('core/resource')->getConnection('core_read');
            $select->reset(Varien_Db_Select::COLUMNS);
            $select->columns(array('request_path'));
            $canonicalPath = $canonicalPathConnection->fetchOne($select);

            if ($canonicalPath && ($request->getOriginalPathInfo() != '/'.$canonicalPath)) {
                Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl() . $canonicalPath, 301);
            }
        }
    }

    public function onControllerProductInit($observer)
    {
        if (Mage::getStoreConfig('amseourl/general/breadcrumb')
            != Amasty_SeoSingleUrl_Model_Source_Breadcrumb::BREADCRUMB_LAST_VISITED) {
            return;
        }

        $lastId = Mage::getSingleton('catalog/session')->getLastVisitedCategoryId();

        if ($lastId) {
            $product = Mage::registry('current_product');
            if ($product->canBeShowInCategory($lastId)) {
                $category = Mage::getModel('catalog/category')->load($lastId);
                $product->setCategory($category);

                Mage::unregister('current_category');
                Mage::register('current_category', $category);
            }
        }
    }
}
