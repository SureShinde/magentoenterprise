<?php
/**
 * Copy Category
 *
 * NOTICE OF LICENSE
 *
 * PHP version 5
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category  FME
 * @package   FME_Copycategories
 * @author    Altaf Ahmed <support@fmeextension.com>
 * @copyright 2016 FME Extensions (https://www.fmeextensions.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  
 * Open Software License (OSL 3.0)
 * @link      https://www.fmeextensions.com
 */

/**
 * Class FME_Copycategories_Adminhtml_CopycategoriesController
 *
 * @category FME
 * @package  FME_Copycategories
 * @author   Altaf Ahmed <support@fmeextension.com>
 * @license  http://opensource.org/licenses/osl-3.0.php  
 * Open Software License (OSL 3.0)
 * @link     https://www.fmeextensions.com
 */

class FME_Copycategories_Adminhtml_CopycategoriesController 
extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize Category
     *
     * @param getrootinstead $getRootInstead get root category
     *
     * @return category
     */
    protected function _initCategory($getRootInstead = false)
    {
        $this->_title($this->__('Catalog'))
            ->_title($this->__('Categories'))
            ->_title($this->__('Manage Categories'));

        $categoryId = (int) $this->getRequest()->getParam('id', false);
        $storeId    = (int) $this->getRequest()->getParam('store');
        $category = Mage::getModel('catalog/category');
        $category->setStoreId($storeId);

        if ($categoryId) {
            $category->load($categoryId);
            if ($storeId) {
                $rootId = Mage::app()->getStore($storeId)->getRootCategoryId();
                if (!in_array($rootId, $category->getPathIds())) {
                    // load root category instead wrong one
                    if ($getRootInstead) {
                        $category->load($rootId);
                    } else {
                        $this->_redirect('*/*/', array('_current'=>true, 'id'=>null));
                        return false;
                    }
                }
            }
        }

        if ($activeTabId = (string) $this->getRequest()->getParam('active_tab_id')) {
            Mage::getSingleton('admin/session')->setActiveTabId($activeTabId);
        }

        Mage::register('category', $category);
        Mage::register('current_category', $category);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $category;
    }


   

 

    /**
     * WYSIWYG editor action for ajax request
     *
     * @return void
     */
    public function wysiwygAction()
    {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeId = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $content = $this->getLayout()->createBlock(
            'adminhtml/catalog_helper_form_wysiwyg_content', '', array(
            'editor_element_id' => $elementId,
            'store_id'          => $storeId,
            'store_media_url'   => $storeMediaUrl,
            )
        );

        $this->getResponse()->setBody($content->toHtml());
    }

    /**
     * Get tree node (Ajax version)
     *
     * @return void
     */
    public function categoriesJsonAction()
    {
        if ($this->getRequest()->getParam('expand_all')) {
            Mage::getSingleton('admin/session')->setIsTreeWasExpanded(true);
        } else {
            Mage::getSingleton('admin/session')->setIsTreeWasExpanded(false);
        }
        if ($categoryId = (int) $this->getRequest()->getPost('id')) {
            $this->getRequest()->setParam('id', $categoryId);

            if (!$category = $this->_initCategory()) {
                return;
            }
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('copycategories/adminhtml_copycategories_edit_tab_form')
                    ->getTreeJson($category)
            );
        }
    }


    

    

   

    /**
     * Tree Action
     * Retrieve category tree
     *
     * @return void
     */
    public function treeAction()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        $categoryId = (int) $this->getRequest()->getParam('id');

        if ($storeId) {
            if (!$categoryId) {
                $store = Mage::app()->getStore($storeId);
                $rootId = $store->getRootCategoryId();
                $this->getRequest()->setParam('id', $rootId);
            }
        }

        $category = $this->_initCategory(true);

        $block = $this->getLayout()->createBlock('adminhtml/catalog_category_tree');
        $root  = $block->getRoot();
        $this->getResponse()->setBody(
            Mage::helper('core')->jsonEncode(
                array(
                'data' => $block->getTree(),
                'parameters' => array(
                'text'        => $block->buildNodeName($root),
                'draggable'   => false,
                'allowDrop'   => ($root->getIsVisible()) ? true : false,
                'id'          => (int) $root->getId(),
                'expanded'    => (int) $block->getIsWasExpanded(),
                'store_id'    => (int) $block->getStore()->getId(),
                'entity_id' => (int) $category->getId(),
                'root_visible'=> (int) $root->getIsVisible()
                ))
            )
        );
    }

    /**
     * Build response for refresh input element 'path' in form
     *
     * @return void
     */
    public function refreshPathAction()
    {
        if ($cateId = (int) $this->getRequest()->getParam('id')) {
            $category = Mage::getModel('catalog/category')->load($cateId);
            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(
                    array(
                    'id' => $cateId,
                    'path' => $category->getPath(),
                    )
                )
            );
        }
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/categories');
    }

    /**
     * Copy Categories action
     *
     * @return void
     */
    public function copycategoryAction()
    {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('copycategories/adminhtml_copycategories_edit_tab_form'));
        // $block = $this->getLayout()->createBlock('copycategories/adminhtml_copycategories_edit_tab_form');  
        // $this->getLayout()->getBlock('content')->append($block);  
        $this->renderLayout();  

    } 

    /**
     * Save category action
     *
     * @param categoryId        $categoryId        category id
     * @param parentId          $parentId          parent_id
     * @param copyproducts      $copyproducts      copyproducts
     * @param copySubCategories $copySubCategories copySubCategories
     * @param serchArr          $serchArr          serchArr
     *
     * @return void
     */
    public function copycat($categoryId,$parentId,$copyproducts,$copySubCategories,$serchArr,$selectedCatgories)
    {
        
        $sourceCategory = Mage::getModel('catalog/category')->load($categoryId);
        $childcat= $sourceCategory->getChildrenCategories();
        $name =  $sourceCategory->getName();
        $des = $sourceCategory->getDescription();
        $cpage = $sourceCategory->getMetaTitle();
        $metakeyword = $sourceCategory->getMetaKeywords();
        $metadescription = $sourceCategory->getMetaDescription();
        $level = $sourceCategory->getLevel();
        $categoryApi = new Mage_Catalog_Model_Category_Api();
        // $parentId = $parent;
        // ---------------  Search and replace text code -----------------
        if (!empty($serchArr)) {
            foreach ($serchArr as $key => $value) {
        
                if (strpos($name, $value) !== false) {
                    $name = str_replace($value, $key, $name);
                }
                if (strpos($des, $value) !== false) {
                    $des = str_replace($value, $key, $des);
                }
                if (strpos($cpage, $value) !== false) {
                    $cpage = str_replace($value, $key, $cpage);
                }
                if (strpos($metakeyword, $value) !== false) {
                    $metakeyword = str_replace($value, $key, $metakeyword);
                }
                if (strpos($metadescription, $value) !== false) {
                    $metadescription = str_replace($value, $key, $metadescription);
                }
            }
        }
        try {
            $newCategoryId = $categoryApi->create(
                $parentId,
                array(
                'name' => $name,
                'is_active' => 1,
                'position' => 1,
                //<!-- position parameter is deprecated, 
                //category anyway will be positioned in the end of list
                //and you can not set position directly, use catalog_category.move instead -->
                'available_sort_by' => 'position',
                'custom_design' => null,
                'custom_apply_to_products' => null,
                'custom_design_from' => null,
                'custom_design_to' => null,
                'custom_layout_update' => null,
                'default_sort_by' => 'position',
                'description' => $des,
                'display_mode' => null,
                'is_anchor' => 0,
                'landing_page' => null,
                'meta_description' => $metadescription,
                'meta_keywords' => $metakeyword,
                'meta_title' => $cpage,
                'page_layout' => 'two_columns_left',
                'include_in_menu' => 1,
                )
            );
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError('error '.$e->getMessage());
        }
       
        if ($copyproducts) {
            $products = $categoryApi->assignedProducts($categoryId);
            foreach ($products as $product) {
                $categoryApi->assignProduct($newCategoryId, $product['product_id']);
            }
        }
        if ($copySubCategories==1) {
            foreach ($childcat as $key => $value) {
                 $this->copycat(
                     $value->getId(), 
                     $newCategoryId, 
                     $copyproducts, 
                     $copySubCategories, 
                     $serchArr,
                     $selectedCatgories
                 );
            }
        } elseif ($copySubCategories==2) {
            foreach ($childcat as $key => $value) {
                if (in_array($value->getId(), $selectedCatgories)) {
                    $this->copycat(
                        $value->getId(), 
                        $newCategoryId, 
                        $copyproducts, 
                        $copySubCategories, 
                        $serchArr,
                        $selectedCatgories
                    );
                }
                 
            }
        }
    }/*End Function*/

    /**
     * Copy only product
     *
     * @param destinationCategory $destinationCategory destinationCategory
     * @param sourceCategoryId    $sourceCategoryId    sourceCategoryId
     *
     * @return void
     */
    public function onlyproducts($destinationCategory,$sourceCategoryId)
    {
        $categoryApi = new Mage_Catalog_Model_Category_Api();
        $products = $categoryApi->assignedProducts($sourceCategoryId);
        foreach ($products as $product) {
            $categoryApi->assignProduct($destinationCategory, $product['product_id']);
        }
    }
    /**
     * Save Category Action
     *
     * @return void
     */
    public function savecategoryAction()
    {   
        $categories  = $this->getRequest()->getPost('tree'); 
        $copySubCategories=$this->getRequest()->getPost('copy_categories');
        $destinationCategory = $this->getRequest()->getPost('user_selected_parent');
        $copyproducts=$this->getRequest()->getPost('copy_products');
        $onlyProducts= $this->getRequest()->getPost('copy_only_products');
        $serchArr = array_combine(
            $this->getRequest()->getPost('cate_replace'), 
            $this->getRequest()->getPost('cate_search')
        );
        $serchArr =  array_filter($serchArr);
        $destCategory=Mage::getModel('catalog/category')->load($destinationCategory);
        foreach ($categories as  $sourceCategoryId) {
            $sourceCategory = Mage::getModel('catalog/category')->load($sourceCategoryId);

            $parentId= $sourceCategory->getParentId();
            if (in_array($parentId, $categories)) {
                continue;
            }

            
            if ($onlyProducts) {
                $this->onlyproducts($destinationCategory, $sourceCategoryId);
            } else {
                              
                $destpath= explode('/', $destCategory->getPath());
                
                
                if (in_array($sourceCategoryId, $destpath)) {
                    Mage::getSingleton('adminhtml/session')
                    ->addError(
                        Mage::helper('copycategories')
                        ->__('Not allowd to copy Parent Category to Child Category.')
                    );
               
                } else {
                    
                    $this->copycat(
                        $sourceCategoryId, 
                        $destinationCategory,  
                        $copyproducts, 
                        $copySubCategories, 
                        $serchArr,
                        $categories
                    );
                    Mage::getSingleton('core/session')->addSuccess('Successfully Copied');
       

                } /*End If Else*/
            }
        
        }
       
        $this->_redirect('adminhtml/catalog_category/');
        
    }

    
   

    

    // protected function _isAllowed()
    // {
    //     return true;
    // }

   
}
