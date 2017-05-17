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

/**
 * Get CMS blocks list for config options selection
 *
 */
class CLS_NucleusCore_Model_System_Config_Source_Cms_Block
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();

        $options = Mage::getResourceModel('cms/block_collection')
            ->addOrder('title', Varien_Data_Collection_Db::SORT_ORDER_ASC)
            ->load()
            ->toOptionArray();
        array_unshift($options, array('value' => '', 'label' => Mage::helper('catalog')->__('None')));

        return $options;
    }

}