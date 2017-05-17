<?php
/**
 * Nucleus
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Nucleus License
 * that is bundled with this package in the file LICENSE_NUCLEUS.txt.
 * It is also available through the World Wide Web at this URL:
 * http://www.nucleuscommerce.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the World Wide Web, please send an email
 * to support@nucleuscommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Nucleus
 * to newer versions in the future.
 *
 * @category   CLS
 * @package    NucleusCore
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */
class CLS_NucleusCore_Helper_Data extends Mage_Core_Helper_Abstract
{
    const NUCLEUS_VERSION = '1.6.4';
    const MAGE_VERSION_CE = '1.9.2.1';
    const MAGE_VERSION_EE = '1.14.2.1';

    const CATEGORY_DM_SUBCAT = 'SUBCATEGORIES';
    const CATEGORY_DM_SUBCAT_PAGE = 'SUBCATEGORIES_AND_PAGE';
    const CATEGORY_DM_SUBCAT_PRODUCT = 'SUBCATEGORIES_AND_PRODUCTS';
    const CATEGORY_DM_SUBCAT_PAGE_PRODUCT = 'SUBCATEGORIES_PAGE_PRODUCTS';

    const CONFIG_PATH_ADMIN_PACKAGE = 'design/package/admin_name';
    const CONFIG_PATH_ADMIN_THEME = 'design/theme/admin_default';

    const CONFIG_PATH_CHECKOUT_LEAD_TIME_NOTICE = 'design/checkout_order/lead_time_notice';
    const CONFIG_PATH_CHECKOUT_POLICY_BLOCK     = 'design/checkout_order/shipping_returns_policy_block';

    /**
     * Get Nucleus version
     *
     * @return string
     */
    public function getVersion()
    {
        return self::NUCLEUS_VERSION;
    }

    /**
     * Get the minimum Magento version required
     */
    public function getMinMageVersion()
    {
        return (Mage::getEdition() == Mage::EDITION_ENTERPRISE) ? self::MAGE_VERSION_EE : self::MAGE_VERSION_CE;
    }

    /**
     * Get the specific RWD theme that Nucleus should fall back on
     *
     * @return string
     */
    public function getRwdFallback()
    {
        return (Mage::getEdition() == Mage::EDITION_ENTERPRISE) ? 'rwd/enterprise' : 'rwd/default';
    }

    /**
     * Get the appropriate Nucleus theme for this install
     *
     * @return string
     */
    public function getNucleusTheme()
    {
        return (Mage::getEdition() == Mage::EDITION_ENTERPRISE) ? 'enterprise' : 'default';
    }

    /**
     * Take an XML node and return the simple string value or value defined by resolution of "helper" attr
     *
     * @param Varien_Simplexml_Element $node
     * @return string
     */
    public function getNodeStringValue($node)
    {
        // Return nothing if node wasn't an XML element
        if (!($node instanceof Varien_Simplexml_Element)) {
            return '';
        }

        if (isset($node['helper'])) {
            // If the node has a "helper" attribute, resolve it by getting the helper and calling the method
            $helperName = explode('/', (string)$node['helper']);
            $helperMethod = array_pop($helperName);
            $helperName = implode('/', $helperName);
            $node = $node->asArray();
            unset($node['@']);
            return call_user_func_array(array(Mage::helper($helperName), $helperMethod), $node);
        } else {
            // Otherwise, return the literal value of the node
            return (string) $node;
        }
    }

    /**
     * Resize category thumbnail if necessary and return URL
     *
     * @param Mage_Catalog_Model_Category $category
     * @param string $attributeName
     * @param int $width
     * @param int $height
     * @return string
     */
    public function resizeCategoryThumbnail($category, $attributeName, $width, $height=null)
    {
        // Basic data
        $categoryThumbPath = trim($category->getData($attributeName));
        if (empty($categoryThumbPath)) {
            return '';
        }

        $categoryBaseMediaPath = 'catalog/category';

        // Form full path to where we want to cache resized version
        $destPathArr = array(
            $categoryBaseMediaPath,
            'cache',
            Mage::app()->getStore()->getId(),
            $attributeName,
            $width.'x'.$height,
            $categoryThumbPath,
        );

        $destPath = implode('/', $destPathArr);

        // Check if cached image exists already
        if (!file_exists(Mage::getBaseDir('media') . '/' . $destPath)) {
            // Check for source image
            $sourceFilePath = Mage::getBaseDir('media') . '/' . $categoryBaseMediaPath . '/' . $categoryThumbPath;
            if (!file_exists($sourceFilePath)) {
                return '';
            }

            // Do resize and save
            $processor = new Varien_Image($sourceFilePath);
            $processor->resize($width, $height);
            $processor->save(Mage::getBaseDir('media') . '/' . $destPath);
        }

        return Mage::getBaseUrl('media', Mage::app()->getStore()->isCurrentlySecure()) . $destPath;
    }

    /**
     * Get configured admin package.
     *
     * @return string
     */
    public function getAdminPackage() {
        return (string)Mage::getStoreConfig(self::CONFIG_PATH_ADMIN_PACKAGE);
    }

    /**
     * Get configured admin theme.
     *
     * @return string
     */
    public function getAdminTheme() {
        return (string)Mage::getStoreConfig(self::CONFIG_PATH_ADMIN_THEME);
    }

    /**
     * Set the no-route CMS page to import the code template
     */
    public function modifyNoRouteCmsPage()
    {
        $noRouteData = array(
            'title'         => '404 Not Found',
            'root_template' => 'one_column',
            'meta_keywords' => '',
            'meta_description' => '',
            'identifier'    => 'no-route',
            'content'       => "{{block type=\"core/template\" template=\"cms/default/no-route.phtml\"}}"
        );

        $page = Mage::getModel('cms/page');
        $page->load('no-route', 'identifier');
        foreach ($noRouteData as $key=>$value) {
            $page->setData($key, $value);
        }
        $page->save();
    }
}