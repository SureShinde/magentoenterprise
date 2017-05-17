<?php
/**
 * @category   CLS
 * @package    ProductCompare
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

class CLS_ProductCompare_Helper_Data extends Mage_Core_Helper_Abstract
{
    const COOKIE_COMPARE_LIST = 'COMPARED_PRODUCTS';

    const PRODUCT_NAME_TRUNCATE_LENGTH = 35;

    /**
     * Truncate a product name to a number of max characters
     *
     * @param string $name
     * @return string
     */
    public function truncateProductName($name)
    {
        $truncated = $name;
        $nameLength = strlen($name);
        // Only truncate if long enough.
        // The extra characters are to make sure we don't truncate by just a few characters just to have them replaced by ellipsis
        if ($nameLength > (self::PRODUCT_NAME_TRUNCATE_LENGTH + 3)) {
            $truncated = substr($name, 0, self::PRODUCT_NAME_TRUNCATE_LENGTH);
            $truncated .= '...';
        }
        return $truncated;
    }
}