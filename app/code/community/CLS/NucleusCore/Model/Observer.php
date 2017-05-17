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
class CLS_NucleusCore_Model_Observer extends Mage_Core_Model_Abstract
{
    const CONFIG_PATH_CATEGORY_LAYOUT = 'catalog/frontend/category_default_layout';

    /**
     * Check that we have the minimum version of Magento installed
     *
     * @param Varien_Event_Observer $observer
     * @throws CLS_NucleusCore_Versionexception
     */
    public function checkMageVersion($observer)
    {
        $minVersion = Mage::helper('cls_nucleuscore')->getMinMageVersion();
        if (version_compare(Mage::getVersion(), $minVersion) < 0) {
            throw new CLS_NucleusCore_Versionexception(Mage::helper('cls_nucleuscore')->__('Nucleus requires Magento %s edition version %s or greater.', Mage::getEdition(), $minVersion));
        }
    }

    /**
     * Default to two-column-right layout for categories if none is set.
     * Done here instead of in layout so that custom value set in admin will still be honored.
     *
     * @param Varien_Event_Observer $observer
     */
    function setCategoryDefaultLayout($observer)
    {
        if (($category = $observer->getCategory())) {
            $this->_setCategoryDefaultLayout($category, Mage::registry('current_category'));
        }
    }

    /**
     * Default to two-column-right layout for categories if none is set.
     * Sam as setCategoryDefaultLayout, but for collection
     *
     * @param Varien_Event_Observer $observer
     */
    function setCategoryCollectionDefaultLayout($observer)
    {
        if ($categories = $observer->getCategoryCollection()) {
            $currentDisplayCategory = Mage::registry('current_category');
            foreach ($categories as $category) {
                $this->_setCategoryDefaultLayout($category, $currentDisplayCategory);
            }
        }
    }

    /**
     * Add layout files added via theme.xml to layout updates
     * for all themes that are parents of this theme.
     * Observes: core_layout_update_updates_get_after
     *
     * @param Varien_Event_Observer $observer
     * @todo Remove when this is fixed in core
     */
    function addFallbackThemesLayoutUpdates($observer)
    {
        /* @var $updates Mage_Core_Model_Config_Element */
        $updates = $observer->getUpdates();
        /* @var $designPackage Mage_Core_Model_Design_Package */
        $designPackage = Mage::getSingleton('core/design_package');
        /* @var $fallback Mage_Core_Model_Design_Fallback */
        $fallback = Mage::getModel('core/design_fallback');

        $fallbacks = $fallback->getFallbackScheme($designPackage->getArea(), $designPackage->getPackageName(), $designPackage->getTheme('layout'));

        for($i=count($fallbacks)-1; $i>=0; $i--) {
            $fallback = $fallbacks[$i];
            if(!isset($fallback['_package']) || !isset($fallback['_theme'])) {
                continue;
            }

            $fallbackPackage = $fallback['_package'];
            $fallbackTheme = $fallback['_theme'];

            $themeUpdateGroups = Mage::getSingleton('core/design_config')->getNode("{$designPackage->getArea()}/$fallbackPackage/$fallbackTheme/layout/updates");

            if(!$themeUpdateGroups) {
                continue;
            }

            foreach($themeUpdateGroups as $themeUpdateGroup) {
                $themeUpdateGroupArray = $themeUpdateGroup->asArray();

                foreach($themeUpdateGroupArray as $key => $themeUpdate) {
                    $updateNode = $updates->addChild($key);
                    $updateNode->addChild('file', $themeUpdate['file']);
                }
            }
        }
    }

    /**
     * Logic for the default layout
     *
     * @param Mage_Catalog_Model_Category $category
     * @param Mage_Catalog_Model_Category $displayCategory
     * @return Mage_Catalog_Model_Category
     */
    protected function _setCategoryDefaultLayout($category, $displayCategory)
    {
        if (!$category->getPageLayout()) {
            // If there is not yet a registered display category, we can assume we should use the first category's value
            if (is_null($displayCategory)) {
                $displayCategory = $category;
            }

            $defaultLayout = Mage::getStoreConfig(self::CONFIG_PATH_CATEGORY_LAYOUT);
            if ($displayCategory->getDisplayMode() == CLS_NucleusCore_Helper_Data::CATEGORY_DM_SUBCAT
                || $displayCategory->getDisplayMode() == CLS_NucleusCore_Helper_Data::CATEGORY_DM_SUBCAT_PAGE) {
                // If we are displaying sub-categories without products, should always default to one-column
                $defaultLayout = 'one_column';
            }

            $category->setPageLayout($defaultLayout);
        }
        return $category;
    }

    /**
     * Use configured admin package / theme, if provided.
     * Observes: adminhtml_controller_action_predispatch_start
     *
     * @param Varien_Event_Observer $observer
     */
    public function useConfigAdminTheme(Varien_Event_Observer $observer) {
        $adminPackage = Mage::helper('cls_nucleuscore')->getAdminPackage();
        $adminTheme = Mage::helper('cls_nucleuscore')->getAdminTheme();

        if(!empty($adminPackage)) {
            Mage::getDesign()->setPackageName($adminPackage);
        }

        if(!empty($adminTheme)) {
            Mage::getDesign()->setTheme($adminTheme);
        }
    }

    public function onBeforeControllerLayoutLoad(Varien_Event_Observer $observer) {
        $category = Mage::registry('current_category');
        if ($category) {
            $this->_addDisplayModeLayoutUpdateHandle(
                $observer->getEvent()->getLayout(), $category->getDisplayMode());
        }
    }

    protected function _addDisplayModeLayoutUpdateHandle(Mage_Core_Model_Layout $layout, $displayMode) {
        if ($displayMode) {
            $layout->getUpdate()->addHandle('catalog_category_displaymode__' . $displayMode);
        }
    }
}