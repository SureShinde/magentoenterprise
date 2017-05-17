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

class CLS_NucleusCore_Model_Core_Design_Fallback extends Mage_Core_Model_Design_Fallback
{
    /**
     * Get fallback scheme according to theme config
     *
     * @param string $area
     * @param string $package
     * @param string $theme
     * @return array
     * @throws Mage_Core_Exception
     */
    protected function _getFallbackScheme($area, $package, $theme)
    {
        $scheme = array(array());
        $this->_visited = array();
        while ($parent = Mage::helper('cls_nucleuscore')->getNodeStringValue($this->_config->getNode($area . '/' . $package . '/' . $theme . '/parent'))) {

            $this->_checkVisited($area, $package, $theme);

            $parts = explode('/', $parent);
            if (count($parts) !== 2) {
                throw new Mage_Core_Exception('Parent node should be defined as "package/theme"');
            }
            list($package, $theme) = $parts;
            $scheme[] = array('_package' => $package, '_theme' => $theme);
        }

        return $scheme;
    }
}