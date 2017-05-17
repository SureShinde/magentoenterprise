<?php
/**
 * @category   CLS
 * @package    CustomerAdvanced
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */
class CLS_CustomerAdvanced_Helper_Quickaccess extends Mage_Core_Helper_Abstract
{
    const CONFIG_PATH_REGISTER_ENABLED = 'customer/quick_access/enable_register';
    const CONFIG_PATH_LOGIN_ENABLED = 'customer/quick_access/enable_login';
    const CONFIG_PATH_RESTRICTED_URLS = 'customer/quick_access/restricted_urls';

    const ID_PREFIX = 'quick';

    protected $_restrictedUrlPatterns = null;

    /**
     * Whether quick registration is enabled
     *
     * @return bool
     */
    public function quickRegisterEnabled()
    {
        $enabled = (bool) Mage::getStoreConfig(self::CONFIG_PATH_REGISTER_ENABLED);
        return $enabled && !Mage::helper('cls_customeradvanced')->customerLoggedIn();
    }

    /**
     * Whether quick login is enabled
     *
     * @return bool
     */
    public function quickLoginEnabled()
    {
        $enabled = (bool) Mage::getStoreConfig(self::CONFIG_PATH_LOGIN_ENABLED);
        return $enabled && !Mage::helper('cls_customeradvanced')->customerLoggedIn();
    }

    /**
     * Return the HTML ID prefix
     */
    public function getHtmlIdPrefix()
    {
        return self::ID_PREFIX;
    }

    /**
     * Add the HTML prefix we use to keep HTML IDs unique to all IDs in a string
     *
     * @param string $html
     * @return string mixed
     */
    public function addPrefixToHtmlIds($html)
    {
        $html = preg_replace(
            array(
                '/id="([^"]+)"/',
                '/for="([^"]+)"/',
            ),
            array(
                'id="'.self::ID_PREFIX.'_$1"',
                'for="'.self::ID_PREFIX.'_$1"',
            ),
            $html
        );
        return $html;
    }

    /**
     * Get the list of URL patterns to match for restricted access
     *
     * @return array
     */
    public function getRestrictedUrlPatterns()
    {
        if (is_null($this->_restrictedUrlPatterns)) {
            $patternStr = Mage::getStoreConfig(self::CONFIG_PATH_RESTRICTED_URLS);
            if (trim($patternStr) == '') {
                return array();
            }

            $this->_restrictedUrlPatterns = array();
            $patterns = explode("\n", $patternStr);
            foreach ($patterns as $pattern) {
                $pattern = trim($pattern);
                if (!empty($pattern)) {
                    $this->_restrictedUrlPatterns[] = $pattern;
                }
            }
        }
        return $this->_restrictedUrlPatterns;
    }

    /**
     * Get URL patterns in JSON format
     *
     * @return string
     */
    public function getJsonRestrictedUrlPatterns()
    {
        return Mage::helper('core')->jsonEncode($this->getRestrictedUrlPatterns());
    }
}