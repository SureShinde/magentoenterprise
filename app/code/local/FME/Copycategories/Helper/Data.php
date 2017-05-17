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
 * Class FME_Copycategories_Helper_Data
 *
 * @category FME
 * @package  FME_Copycategories
 * @author   Altaf Ahmed <support@fmeextension.com>
 * @license  http://opensource.org/licenses/osl-3.0.php  
 * Open Software License (OSL 3.0)
 * @link     https://www.fmeextensions.com
 */

class FME_Copycategories_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Set Edit Url
     *
     * @return string
     */
    public function setEditUrl()
    {
        return Mage::helper("adminhtml")->getUrl(
            "*/*/edit", array(
            '_current'=>true, 
            'store'=>null, 
            '_query'=>false, 
            'id'=>null, 
            'parent'=>null
            )
        );
    }

    /**
     * Get Parent Category
     *
     * @param cateP $cateP category parent
     * @param cates $cates categories
     * @param arr   $arr   array
     *
     * @return string
     */
    // public function getparentcate($cateP,$cates,$arr)
    // {
    //     $category = Mage::getModel('catalog/category')->load($cateP);
    //     $helper = Mage::helper('catalog/category');
    //     $childrenCategories = $category->getChildrenCategories();
    //     foreach ($childrenCategories as $category) {
    //         if (in_array($category->getId(), $cates)) {
    //             $arr[] =  $category->getId();
    //             $category = Mage::getModel('catalog/category')->load($category->getId());
    //             $copy = clone $category;
    //             $copy->setId(null)->save();
    //             $id = $copy->getId();       
    //             $categoryy = Mage::getModel('catalog/category')->load($id);
    //             $categoryy->move($cateP, null);
    //             $childrenCategori = $category->getChildrenCategories();
    //             if (sizeof($childrenCategori)> 0 ) {
    //                 $this->getparentcate($id, $cates, $arr);
    //             }
    //         }
    //     }
    // }

    /**
     * Get Categories Dropdown
     *
     * @return array
     */
    public function getCategoriesDropdown() 
    {

        $categoriesArray = Mage::getModel('catalog/category')
            ->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSort('path', 'asc')
            ->addFieldToFilter('is_active', array('eq'=>'1'))
            ->load()
            ->toArray();


        foreach ($categoriesArray as $categoryId => $category) {
            if (isset($category['name'])) {
                $categories[] = array(
                'label' => $category['name'],
                'level'  =>$category['level'],
                'value' => $categoryId
                );
            }
        }
        return $categories;
    }
}