<?php
/**
 * @category   CLS
 * @package    Theme
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

include_once 'CLS' . DS . 'Theme' . DS . 'lib' . DS . 'Minify' . DS . 'JSMin.php';
include_once 'CLS' . DS . 'Theme' . DS . 'lib' . DS . 'Minify' . DS . 'Minify' . DS . 'CSS.php';

class CLS_Theme_Model_Observer extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'cls_theme_observer';
    
    /**
     * Monitor merged JS and CSS files in order to 
     * queue for minification.
     * Observes: core_data_merge_after
     *
     * @return void
     * @param Varien_Event_Observer $observer
     */
    public function doMinification(Varien_Event_Observer $observer) {
        /* @var $helper CLS_Theme_Helper_Data */
        $helper = Mage::helper('cls_theme');
        $params = $observer->getParams();
        
        $srcFiles = $params->getSrcFiles();
        $targetFilename = $params->getTargetFile();
        $targetFilePathInfo = pathinfo($targetFilename);
        
        foreach($srcFiles as $file) {
        	switch($targetFilePathInfo['extension']) {
        	
        		case 'js':
        			if(Mage::getStoreConfig('dev/js/enable_js_minification')) {
        	
        				$helper->scheduleMinification('js', $file, $targetFilename);
        	
        			}
        			break;
        		case 'css':
        			if(Mage::getStoreConfig('dev/js/enable_css_minification')) {
        	
        				$helper->scheduleMinification('css', $file, $targetFilename);
        	
        			}
        			break;
        	
        	} //end target file extension switch
        } //end looping source files
    }
    
    /**
     * This method runs on the cron and
     * discovers queued files which need to 
     * to be minified.
     *
     * @return void
     */
    public function flushMinifyQueue() {
    	/* @var $variableHelper CLS_Core_Helper_Variable */
    	$variableHelper = Mage::helper('cls_core/variable');
    	 
    	$queue = $variableHelper->getVariable(CLS_Theme_Helper_Data::MINIFICATION_QUEUE_VARIABLE_CODE);
    	if($queue === false) {
    		return; //missing queue--nothing to do
    	} else {
    		$queue = unserialize($queue);
    	}
    	 
    	if(!is_array($queue) || empty($queue)) {
    		return; //corrupt/empty queue
    	}
    	 
    	/* @var $helper CLS_Theme_Helper_Data */
    	$helper = Mage::helper('cls_theme');

        Mage::getSingleton('core/resource')->getConnection('read')->beginTransaction();
        try {
            $helper->flushMinifyQueue($queue);

            if (Mage::getConfig()->getModuleConfig('Enterprise_Enterprise')) {
                $type = 'full_page';
                Mage::app()->getCacheInstance()->cleanType($type);
                Enterprise_PageCache_Model_Cache::getCacheInstance()->clean(Enterprise_PageCache_Model_Processor::CACHE_TAG);
            }

            $variableHelper->setVariable(CLS_Theme_Helper_Data::MINIFICATION_QUEUE_VARIABLE_CODE, serialize($queue));
            Mage::getSingleton('core/resource')->getConnection('read')->commit();
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('core/resource')->getConnection('read')->rollBack();
        }
    }
    
    /**
     * When the JS/CSS cache is flushed,
     * increment merged js/css increment 
     * integer.
     * Observes: clean_media_cache_after
     *
     * @return void
     * @param Varien_Event_Observer $observer
     */
    public function incrementMergeIncrement(Varien_Event_Observer $observer) {
        /* @var $helper CLS_Theme_Helper_Data */
        $helper = Mage::helper('cls_theme');
        $helper->incrementMergeIncrement();
    }
    
    /**
     * When JS/CSS cache is flushed, 
     * clear minification queue.
     * Observes: clean_media_cache_after
     * 
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function clearMinifyQueue(Varien_Event_Observer $observer) {
    	/* @var $variableHelper CLS_Core_Helper_Variable  */
    	$variableHelper = Mage::helper('cls_core/variable');
    	
    	//set variable to empty array
    	$variableHelper->setVariable(CLS_Theme_Helper_Data::MINIFICATION_QUEUE_VARIABLE_CODE, serialize(array()));
    }
}