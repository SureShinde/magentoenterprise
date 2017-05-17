<?php
/**
 * @category   CLS
 * @package    Theme
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

include_once 'CLS' . DS . 'Theme' . DS . 'lib' . DS . 'Minify' . DS . 'JSMin.php';
include_once 'CLS' . DS . 'Theme' . DS . 'lib' . DS . 'Minify' . DS . 'Minify' . DS . 'CSS.php';

class CLS_Theme_Helper_Data extends Mage_Core_Helper_Abstract
{
    const MINIFICATION_QUEUE_VARIABLE_CODE = 'cls-minify-file-queue';
    const MERGE_INCREMENT_VARIABLE_CODE = 'cls-merge-increment';
    
    /**
     * Schedules async minification
     *
     * @return void
     * @param string $filetype
     * @param string $sourceFile - file to minify
     * @param string $targetFile - file to append minified source
     */
    public function scheduleMinification($filetype, $sourceFile, $targetFile) {
        Varien_Profiler::start('cls_theme_scheduleMinification()');

        /* @var $variableHelper CLS_Custom_Helper_Variable */
        $variableHelper = Mage::helper('cls_core/variable');
        
        $currentQueue = $variableHelper->getVariable(self::MINIFICATION_QUEUE_VARIABLE_CODE);
        if($currentQueue === false) {
            $currentQueue = array();
        } else {
            $currentQueue = unserialize($currentQueue);
        }
        
        if(!is_array($currentQueue)) { //corrupt queue
            $currentQueue = array(); //reset
        }
        
        if(!isset($currentQueue[$targetFile])) {
        	$currentQueue[$targetFile] = array();
        }
        
        $newQueueItem = array('filetype' => $filetype, 'source_file' => $sourceFile);
        
        if(!in_array($newQueueItem, $currentQueue)) {
            $currentQueue[$targetFile][] = $newQueueItem;
        }
        
        $variableHelper->setVariable(self::MINIFICATION_QUEUE_VARIABLE_CODE, serialize($currentQueue));
        
        Varien_Profiler::stop('cls_theme_scheduleMinification()');
    }
    
    /**
     * Minifies original contents and returns minified string
     *
     * @return string
     * @param string $filetype
     * @param string $contents
     */
    public function getMinifiedFileContents($filetype, $contents) {
        Varien_Profiler::start('cls_theme_getMinifiedFileContents()');
        
        if(empty($contents)) {
            return null;
        }
        
        $minified = '';
        
        try {
            switch($filetype) {
                case 'js':
                    $minified = JSMin::minify($contents);   
                    break;
                case 'css':
                    $minified = Minify_CSS::minify($contents);
                    break;
            }
        } catch(Exception $ex) {
            Mage::logException($ex);
        }
        
        Varien_Profiler::stop('cls_theme_getMinifiedFileContents()');
        
        return $minified;
    }
    
    /**
     * Gets the current merge increment integer
     *
     * @return int
     */
    public function getMergeIncrement() {
        /* @var $variableHelper CLS_Custom_Helper_Variable */
        $variableHelper = Mage::helper('cls_core/variable');
        
        $value = $variableHelper->getVariable(self::MERGE_INCREMENT_VARIABLE_CODE);
        
        if($value === false) {
            $value = 0;
        }
        
        return $value;
    }
    
    /**
     * Increments the current merge increment integer
     *
     * @return void
     */
    public function incrementMergeIncrement() {
        /* @var $variableHelper CLS_Custom_Helper_Variable */
        $variableHelper = Mage::helper('cls_core/variable');
        
        $variableHelper->setVariable(self::MERGE_INCREMENT_VARIABLE_CODE, $this->getMergeIncrement() + 1);
    }

    /**
     * Reads file and returns contents based on type and path
     *
     * @param string $filetype
     * @param string $filename
     * @throws CLS_Theme_FileNotFoundException
     * @return string
     */
    protected function _getJsCssFileContents($filetype, $filename) {
    	if(!file_exists($filename)) {
    		throw new CLS_Theme_FileNotFoundException( sprintf('%s file at path "%s" does not exist.', $filetype, $filename) );
    	}
    	
    	return file_get_contents($filename);
    }
    
    /**
     * Writes contents to target file and type
     * 
     * @return void
     * @param string $filetype
     * @param string $filename
     * @param string $contents
     */
    protected function _putJsCssFileContents($filetype, $filename, $contents) {
    	file_put_contents($filename, $contents, LOCK_EX);
    }
    
    /**
     * Determines if this file should indeed be minified
     * 
     * @param string $filetype
     * @param string $filename
     * @return bool
     */
    protected function _shouldMinifyFile($filetype, $filename) {
    	$retval = true; //files will almost certainly need to be minified
    	
    	//filesnames containing '.min.' are considered to be already minified
    	if( strpos($filename, '.min.') !== false ) {
    		$retval = false;
    	}
    	
    	//give observers a chance to interfer
    	$responseWrapper = new Varien_object();
    	Mage::dispatchEvent(
	    					'cls_theme_should_merge_file_before',
	    					array( 
	    							'filetype' => $filetype, 
    								'filename' => $filename,
    								'current_decision' => $retval,
    								'response_wrapper' => $responseWrapper 
	    						 )
	    				   );
	    if($responseWrapper->hasShouldMinifyOverride()) {
	    	$retval = (bool)$responseWrapper->getShouldMinifyOverride();
	    }
    	
	    return $retval;
    }
    
    /**
     * finds files that need to be minified and actually minifies them
     * 
     * @param array $queue
     * @return void
     */
    public function flushMinifyQueue(array &$queue) {
    	/* @var $package Mage_Core_Model_Design_Package */
    	$package = Mage::getModel('core/design_package');
    	$minifiedContents = array(); //this will hold the minified target files in memory
    	
    	foreach($queue as $targetFile=>$files) {
    		foreach($files as $file) {
    			
				$filetype = $file['filetype'];
	    		$sourceFile = $file['source_file'];
	    		
	    		if(!isset($minifiedContents[$targetFile])) {
	    			$minifiedContents[$targetFile] = array('type' => $filetype, 'content' => '');
	    		}
	    		
	    		try {
	    			$output = $this->_getJsCssFileContents($filetype, $sourceFile);
	    			$output = $package->beforeMergeCss($sourceFile, $output); //convert CSS relative URLs to absolute skin URLs
	    			
	    			if($this->_shouldMinifyFile($filetype, $sourceFile)) {
	    				$output = $this->getMinifiedFileContents($filetype, $output); //actually perform minification
	    			}
	    			
	    			$minifiedContents[$targetFile]['content'] .= $output . "\n\n";
	    		} catch(CLS_Theme_FileNotFoundException $fnfe) {
	    			//file doesn't exist ... swallow exception?
	    		}
	    	
    		} //end looping through target file's source files
    		
    		unset($queue[$targetFile]); //remove target file from queue
    		
    	} //end looping through minification queue target files
    	
    	foreach($minifiedContents as $filename => $file) {
            $filename = substr($filename, 0, strrpos($filename, '-') + 1) . ($this->getMergeIncrement() + 1) . substr($filename, strrpos($filename, '.'));
            $this->_putJsCssFileContents($file['type'], $filename, $file['content']);
        } //end looping $minifiedContents

        // Increment Merge increment to force browsers to pull a new file and get around cdn caching the non-minified file
        $this->incrementMergeIncrement();

        $this->_putJsCssFileContents($file['type'], $filename, $file['content']);

    } //end flushMinifyQueue()
    
}