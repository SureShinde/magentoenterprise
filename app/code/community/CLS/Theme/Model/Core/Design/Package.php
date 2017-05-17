<?php
/**
 * @category   CLS
 * @package    Theme
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

class CLS_Theme_Model_Core_Design_Package extends Mage_Core_Model_Design_Package
{
    protected $_eventPrefix = 'core_design_package';
    
    /**
     * Get the target filename of the merged css
     *
     * @return string
     * @param array $files
     * @param string $hostname
     * @param string $port
     */
    protected function _getMergedCssFilename($files, $hostname, $port) {
        /* @var $helper CLS_Theme_Helper_Data */
        $helper = Mage::helper('cls_theme');
        $targetFilename = md5(implode(',', $files) . "|{$hostname}|{$port}")
            . '-' . $helper->getMergeIncrement()
            . '.css';
        
        return $targetFilename;
    }
    
    /**
     * Get the filename of the merged JS
     *
     * @return string
     * @param array $files
     */
    protected function _getMergedJsFilename($files) {
        /* @var $helper CLS_Theme_Helper_Data */
        $helper = Mage::helper('cls_theme');
        $targetFilename = md5(implode(',', $files))
            . '-' . $helper->getMergeIncrement()
            . '.js';
        
        return $targetFilename;
    }

    /**
     * Merge specified css files and return URL to the merged file on success
     *
     * @param $files
     * @return string
     */
    public function getMergedCssUrl($files)
    {
        // secure or unsecure
        $isSecure = Mage::app()->getRequest()->isSecure();
        $mergerDir = $isSecure ? 'css_secure' : 'css';
        $targetDir = $this->_initMergerDir($mergerDir);
        if (!$targetDir) {
            return '';
        }
    
        // base hostname & port
        $baseMediaUrl = Mage::getBaseUrl('media', $isSecure);
        $hostname = parse_url($baseMediaUrl, PHP_URL_HOST);
        $port = parse_url($baseMediaUrl, PHP_URL_PORT);
        if (false === $port) {
            $port = $isSecure ? 443 : 80;
        }
    
        // merge into target file
        ## Begin Edit: Eric Wiese - add increment to target filename ##
        $targetFilename = $this->_getMergedCssFilename($files,$hostname,$port);
        ## End Edit ##
        if ($this->_mergeFiles($files, $targetDir . DS . $targetFilename, false, array($this, 'beforeMergeCss'), 'css')) {
            return $baseMediaUrl . $mergerDir . '/' . $targetFilename;
        }
        return '';
    }
    
    /**
     * Merge specified javascript files and return URL to the merged file on success
     *
     * @param $files
     * @return string
     */
    public function getMergedJsUrl($files)
    {
        ## Begin Edit: Eric Wiese - add increment to target filename ##
        $targetFilename = $this->_getMergedJsFilename($files);
        ## End Edit ##
        $targetDir = $this->_initMergerDir('js');
        if (!$targetDir) {
            return '';
        }
        ## Begin Edit: Eric Wiese - add before merge callback ##
        if ($this->_mergeFiles($files, $targetDir . DS . $targetFilename, false, array($this, 'beforeMergeJs'), 'js')) {
        ## End Edit ##
            return Mage::getBaseUrl('media', Mage::app()->getRequest()->isSecure()) . 'js/' . $targetFilename;
        }
        return '';
    }
    
    /**
     * Before merge css callback function
     *
     * @param string $file
     * @param string $contents
     * @return string
     */
    public function beforeMergeCss($file, $contents)
    {
        $contents = parent::beforeMergeCss($file, $contents);
        
        ## Begin Edit: Eric Wiese - Added event dispatch _merge_css_before ##
        $contentsObject = new Varien_Object();
        $contentsObject->setContents($contents);
        $contentsObject->setFile($file);
        Mage::dispatchEvent($this->_eventPrefix.'_merge_css_before', array('design_package'=>$this, 'file_contents_pair'=>$contentsObject));
        
        if($contentsObject->getData('contents') != $contentsObject->getOrigData('contents')) { //contents changed
            $contents = $contentsObject->getContents();
        }
        if($contentsObject->getData('file') != $contentsObject->getOrigData('file')) { //file changed
            $file = $contentsObject->getFile();
        }
        ## End Edit ##

        return $contents;
    }
    
    /**
     * Added event dispatch _merge_js_before
     *
     * @param string $file
     * @param string $contents
     * @return string
     */
    public function beforeMergeJs($file, $contents)
    {
        //dispatch event so other modules can interfere
        $contentsObject = new Varien_Object();
        $contentsObject->setContents($contents);
        $contentsObject->setFile($file);
        Mage::dispatchEvent($this->_eventPrefix.'_merge_js_before', array('design_package'=>$this, 'file_contents_pair'=>$contentsObject));
        
        if($contentsObject->getData('contents') != $contentsObject->getOrigData('contents')) { //contents changed
            $contents = $contentsObject->getContents();
        }
        if($contentsObject->getData('file') != $contentsObject->getOrigData('file')) { //file changed
            $file = $contentsObject->getFile();
        }
        //end dispatching events
        
        return $contents;
    }
}