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
 * Class FME_Copycategories_Block_Adminhtml_Copycategories_Edit_Tab_Form
 *
 * @category FME
 * @package  FME_Copycategories
 * @author   Altaf Ahmed <support@fmeextension.com>
 * @license  http://opensource.org/licenses/osl-3.0.php  
 * Open Software License (OSL 3.0)
 * @link     https://www.fmeextensions.com
 */

class FME_Copycategories_Block_Adminhtml_Copycategories_Edit_Tab_Form 
extends Mage_Adminhtml_Block_Catalog_Category_Abstract
{

    protected $_withProductCount;

    /**
     * Get __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('copycategories/catalog/category/categories.phtml');
        $this->setFormAction(Mage::getUrl('*/*/savecategory')); 
        $this->setUseAjax(true);
        $this->_withProductCount = true;
    }

    /**
     * Prepare Form Layout
     *
     * @return _prepareLayout
     */
    protected function _prepareLayout()
    {
        $addUrl = $this->getUrl(
            "*/*/add", array(
            '_current'=>true,
            'id'     =>null,
            '_query' => false
            )
        );

   
        return parent::_prepareLayout();
    }

    /**
     * Get Default Store Id
     *
     * @return String
     */
    protected function _getDefaultStoreId()
    {
        return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    }

    /**
     * Retun categoyr collection
     *
     * @return $collection
     */
    public function getCategoryCollection()
    {
        $storeId = $this->getRequest()->getParam('store', $this->_getDefaultStoreId());
        $collection = $this->getData('category_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('catalog/category')->getCollection();
            $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('is_active')
                ->setProductStoreId($storeId)
                ->setLoadProductCount($this->_withProductCount)
                ->setStoreId($storeId);

            $this->setData('category_collection', $collection);
        }
        return $collection;
    }

    /**
     * Get Expand Button Html
     *
     * @return expand_button
     */
    public function getExpandButtonHtml()
    {
        return $this->getChildHtml('expand_button');
    }

    /**
     * Get Collapse Button Html
     *
     * @return expand_button
     */
    public function getCollapseButtonHtml()
    {
        return $this->getChildHtml('collapse_button');
    }

    /**
     * Get Store Switch Html
     *
     * @return store_switcher
     */
    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

    /**
     * Get Load Tree Url
     *
     * @param string $expanded got tree expended
     *
     * @return string
     */
    public function getLoadTreeUrl($expanded=null)
    {
        $params = array('_current'=>true, 'id'=>null,'store'=>null);
        if ((is_null($expanded) && Mage::getSingleton('admin/session')->getIsTreeWasExpanded())
            || $expanded == true
        ) {
            $params['expand_all'] = true;
        }
        return $this->getUrl('*/*/categoriesJson', $params);
    }

    /**
     * Get getNodesUrl
     *
     * @return string
     */
    public function getNodesUrl()
    {
        return $this->getUrl('*/copycategories_edit_tab_form/jsonTree');
    }

    /**
     * Get getSwitchTreeUrl
     *
     * @return string
     */
    public function getSwitchTreeUrl()
    {
        return $this->getUrl(
            "*/copycategories_edit_tab_form/tree", 
            array(
                '_current'=>true, 
                'store'=>null, 
                '_query'=>false, 
                'id'=>null, 
                'parent'=>null
                )
        );
    }

    /**
     * Get getIsWasExpanded
     *
     * @return boolean
     */
    public function getIsWasExpanded()
    {
        return Mage::getSingleton('admin/session')->getIsTreeWasExpanded();
    }

    /**
     * Get getMoveUrl
     *
     * @return string
     */
    public function getMoveUrl()
    {
        return $this->getUrl(
            '*/copycategories_edit_tab_form/move', 
            array(
                'store'=>$this->getRequest()->getParam('store')
                )
        );
    }

    /**
     * Get getTree
     *
     * @param string $parenNodeCategory parent category
     *
     * @return object
     */
    public function getTree($parenNodeCategory=null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parenNodeCategory));
        $tree = isset($rootArray['children']) ? $rootArray['children'] : array();
        return $tree;
    }

    /**
     * Get getTreeJson
     *
     * @param string $parenNodeCategory parent category
     *
     * @return json object
     */
    public function getTreeJson($parenNodeCategory=null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parenNodeCategory));
        $json = Mage::helper('core')->jsonEncode(isset($rootArray['children']) ? $rootArray['children'] : array());
        return $json;
    }

    /**
     * Get JSON of array of categories, that are breadcrumbs for specified category path
     *
     * @param string $path              breadcrumbs
     * @param string $javascriptVarName javascript varibale name
     *
     * @return string
     */
    public function getBreadcrumbsJavascript($path, $javascriptVarName)
    {
        if (empty($path)) {
            return '';
        }

        $categories = Mage::getResourceSingleton('catalog/category_tree')
            ->setStoreId($this->getStore()->getId())->loadBreadcrumbsArray($path);
        if (empty($categories)) {
            return '';
        }
        foreach ($categories as $key => $category) {
            $categories[$key] = $this->_getNodeJson($category);
        }
        return
            '<script type="text/javascript">'
            . $javascriptVarName . ' = ' . Mage::helper('core')->jsonEncode($categories) . ';'
            . ($this
                ->canAddSubCategory() ? '$("add_subcategory_button").show();' : '$("add_subcategory_button").hide();')
            . '</script>';
    }

    /**
     * Get JSON of a tree node or an associative array
     *
     * @param Varien_Data_Tree_Node|array $node  node id
     * @param int                         $level node lavel
     * 
     * @return string
     */
    protected function _getNodeJson($node, $level = 0)
    {
        // create a node from data array
        if (is_array($node)) {
            $node = new Varien_Data_Tree_Node($node, 'entity_id', new Varien_Data_Tree);
        }

        $item = array();
        $item['text'] = $this->buildNodeName($node);

        
        $rootForStores = in_array($node->getEntityId(), $this->getRootIds());

        $item['id']  = $node->getId();
        $item['store']  = (int) $this->getStore()->getId();
        $item['path'] = $node->getData('path');

        $item['cls'] = 'folder ' . ($node->getIsActive() ? 'active-category' : 'no-active-category');
        //$item['allowDrop'] = ($level<3) ? true : false;
        $allowMove = $this->_isCategoryMoveable($node);
        $item['allowDrop'] = $allowMove;
        // disallow drag if it's first level and category is root of a store
        $item['allowDrag'] = $allowMove && (($node->getLevel()==1 && $rootForStores) ? false : true);

        if ((int)$node->getChildrenCount()>0) {
            $item['children'] = array();
        }

        $isParent = $this->_isParentSelectedCategory($node);

        if ($node->hasChildren()) {
            $item['children'] = array();
            if (!($this->getUseAjax() && $node->getLevel() > 1 && !$isParent)) {
                foreach ($node->getChildren() as $child) {
                    $item['children'][] = $this->_getNodeJson($child, $level+1);
                }
            }
        }

        if ($isParent || $node->getLevel() < 2) {
            $item['expanded'] = true;
        }

        return $item;
    }

    /**
     * Get category name
     *
     * @param Varien_Object $node category node
     *
     * @return string
     */
    public function buildNodeName($node)
    {
        $result = $this->htmlEscape($node->getName());
        if ($this->_withProductCount) {
             $result .= ' (' . $node->getProductCount() . ')';
        }
        return $result;
    }

    /**
     * Get is Category Moveable
     *
     * @param Varien_Object $node category node
     *
     * @return string
     */
    protected function _isCategoryMoveable($node)
    {
        $options = new Varien_Object(
            array(
            'is_moveable' => true,
            'category' => $node
            )
        );

        Mage::dispatchEvent(
            'adminhtml_copycategories_edit_tab_form_is_moveable',
            array('options'=>$options)
        );

        return $options->getIsMoveable();
    }

    /**
     * Get is Parent Selected Category
     *
     * @param Varien_Object $node category node
     *
     * @return boolean
     */
    protected function _isParentSelectedCategory($node)
    {
        if ($node && $this->getCategory()) {
            $pathIds = $this->getCategory()->getPathIds();
            if (in_array($node->getId(), $pathIds)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if page loaded by outside link to category edit
     *
     * @return boolean
     */
    public function isClearEdit()
    {
        return (bool) $this->getRequest()->getParam('clear');
    }

    /**
     * Check availability of adding root category
     *
     * @return boolean
     */
    public function canAddRootCategory()
    {
        $options = new Varien_Object(array('is_allow'=>true));
        Mage::dispatchEvent(
            'adminhtml_copycategories_edit_tab_form_can_add_root_category',
            array(
                'category' => $this->getCategory(),
                'options'   => $options,
                'store'    => $this->getStore()->getId()
            )
        );

        return $options->getIsAllow();
    }

    /**
     * Check availability of adding sub category
     *
     * @return boolean
     */
    public function canAddSubCategory()
    {
        $options = new Varien_Object(array('is_allow'=>true));
        Mage::dispatchEvent(
            'adminhtml_copycategories_edit_tab_form_can_add_sub_category',
            array(
                'category' => $this->getCategory(),
                'options'   => $options,
                'store'    => $this->getStore()->getId()
            )
        );

        return $options->getIsAllow();
    }
}
