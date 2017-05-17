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
 * Class FME_Copycategories_Block_Adminhtml_Catalog_Category_Edit
 *
 * @category FME
 * @package  FME_Copycategories
 * @author   Altaf Ahmed <support@fmeextension.com>
 * @license  http://opensource.org/licenses/osl-3.0.php  
 * Open Software License (OSL 3.0)
 * @link     https://www.fmeextensions.com
 */
class FME_Copycategories_Block_Adminhtml_Catalog_Category_Edit 
extends Mage_Adminhtml_Block_Catalog_Category_Edit
{
    /**
     * Prepare __construct
     *
     * @return void
     */    
    public function __construct()
    {
        parent::__construct();
             $this->setChild(
                 'update_button',
                 $this->getLayout()->createBlock('adminhtml/widget_button')
                     ->setData(
                         array(
                         'label'     => Mage::helper('catalog')->__('Update Category'),
                         'onclick'   => "categoryUpdate('" . $this->getUrl(
                             '*/*/delete', 
                             array('_current' => true)
                         ) . "', true, {$categoryId})",
                         'class' => 'delete'
                         )
                     )
             );

    }

     /**
      * Prepare getUpdateButtonHtml
      *
      * @return update_button
      */ 
    public function getUpdateButtonHtml()
    {
        return $this->getChildHtml('update_button');
    }
 
}