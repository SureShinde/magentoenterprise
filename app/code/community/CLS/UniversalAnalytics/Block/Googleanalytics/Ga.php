<?php
/**
 * @category   CLS
 * @package    UniversalAnalytics
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

/**
 * Rewrites native 'googleanalytics/ga' block
 *
 */

class CLS_UniversalAnalytics_Block_Googleanalytics_Ga extends Mage_GoogleAnalytics_Block_Ga
{
    /**
     * Render regular page tracking javascript code
     * The custom "page name" may be set from layout or somewhere else. It must start from slash.
     *
     * @param string $accountId
     * @return string
     */
    protected function _getPageTrackingCodeUniversal($accountId)
    {
        $return = Mage::helper('cls_universalanalytics/tracking')
            ->getPageTrackingCodeUniversal($this->jsQuoteEscape($accountId), $this->_getAnonymizationCode(), $this->getOrderIds());

        // Track URL for conversion funnel
        $return .= $this->getChild('ga.conversion_funnel')->toHtml();

        return $return;
    }
}
