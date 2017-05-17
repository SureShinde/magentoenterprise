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
 * Class FME_Copycategories_Block_Adminhtml_Catalog_Category_Edit_Form
 *
 * @category FME
 * @package  FME_Copycategories
 * @author   Altaf Ahmed <support@fmeextension.com>
 * @license  http://opensource.org/licenses/osl-3.0.php  
 * Open Software License (OSL 3.0)
 * @link     https://www.fmeextensions.com
 */ 
class FME_Copycategories_Block_Adminhtml_Catalog_Category_Edit_Form 
extends Mage_Adminhtml_Block_Catalog_Category_Edit_Form
{
    /**
     * Prepare Form layout
     *
     * @return Mage_Adminhtml_Block_Catalog_Category_Edit_Form
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $category = $this->getCategory();
        //$categoryId = (int) $category->getId(); 
        // 0 when we create category, otherwise some value for editing category
        $this->setChild(
            'tabs',
            $this->getLayout()->createBlock('adminhtml/catalog_category_tabs', 'tabs')
        );
        //if ($categoryId > 0 ) {
            $this->addAdditionalButton(
                'update_button',
                array(
                    'name' => 'update_button',
                    'title'=>'Copy Category',
                    'type'=>"button",
                    'label'=> Mage::helper('catalog')->__('Copy Category'),
                    'onclick'   => "location.href = '".$this->getUrl(
                        'adminhtml/copycategories/copycategory/'
                    )."'")
            );

            return parent::_prepareLayout();
        //}
    }

    
 
}