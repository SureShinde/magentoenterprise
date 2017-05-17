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
class CLS_NucleusCore_Helper_Block extends Mage_Core_Helper_Abstract
{
    /**
     * Loop through the children of a child text list, add specific data to each, and collect the output
     *
     * @param Mage_Core_Block_Abstract $parentBlock
     * @param string $listBlockName
     * @param array $data
     * @return string
     */
    public function getChildHtmlDataRecursive($parentBlock, $listBlockName, $data = array())
    {
        $listBlock = $parentBlock->getChild($listBlockName);

        if (!($listBlock instanceof Mage_Core_Block_Text_List)) {
            return '';
        }

        $html = '';
        $children = $listBlock->getSortedChildren();
        foreach($children as $childName) {
            $child = $listBlock->getChild($childName);

            if (!($child instanceof Mage_Core_Block_Abstract)) {
                continue;
            }

            $child->addData($data);
            $html .= $child->toHtml();
        }

        return $html;
    }
}