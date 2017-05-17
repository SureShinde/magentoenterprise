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
 * Class FME_Copycategories_Block_Copycategories
 *
 * @category FME
 * @package  FME_Copycategories
 * @author   Altaf Ahmed <support@fmeextension.com>
 * @license  http://opensource.org/licenses/osl-3.0.php  
 * Open Software License (OSL 3.0)
 * @link     https://www.fmeextensions.com
 */
class FME_Copycategories_Block_Copycategories extends Mage_Core_Block_Template
{
    /**
     * Prepare Layout
     *
     * @return layout
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    /**
     * Get Copy Categories
     *
     * @return array
     */
    public function getCopycategories()     
    { 
        if (!$this->hasData('copycategories')) {
            $this->setData('copycategories', Mage::registry('copycategories'));
        }
        return $this->getData('copycategories');
        
    }
}
