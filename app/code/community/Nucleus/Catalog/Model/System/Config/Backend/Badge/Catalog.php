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
 * @category   Nucleus
 * @package    Catalog
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

class Nucleus_Catalog_Model_System_Config_Backend_Badge_Catalog extends Mage_Core_Model_Config_Data
{

    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            // Clean badges catalog cache
            Mage::dispatchEvent('clean_cache_by_tags', array('tags' => array(
                Nucleus_Catalog_Block_Badges::CACHE_TAG
            )));
        }
    }

}