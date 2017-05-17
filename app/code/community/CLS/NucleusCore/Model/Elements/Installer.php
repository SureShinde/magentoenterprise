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

class CLS_NucleusCore_Model_Elements_Installer extends Varien_Object
{
    const FILE_ELEMENTS_IMPORT = 'nucleus_elements.xml';

    protected $_importXml = null;
    protected $_messages = array();

    public function _construct()
    {
        parent::_construct();
        $this->_loadImportData();

        if (!$this->_importXml->version) {
            Mage::throwException(Mage::helper('cls_nucleuscore')->__('Nucleus Elements version is not specified in import file.'));
        }
    }

    /**
     * Get the version specified in install file
     *
     * @return string
     */
    public function getAvailableVersion()
    {
        return (string) $this->_importXml->version;
    }

    /**
     * Determine if currently installed version of Elements is up to date
     *
     * @return bool
     */
    public function versionUpToDate()
    {
        $installedVersion = Mage::helper('cls_nucleuscore/elements')->getCurrentElementsVersion();
        if (empty($installedVersion)) {
            return false;
        }

        return (version_compare($installedVersion, $this->getAvailableVersion()) >= 0);
    }

    /**
     * Import all Nucleus Elements content
     */
    public function doFullInstall()
    {
        $this->_createContent();
        $this->_deleteContent(false);

        Mage::helper('cls_nucleuscore/elements')->updateElementsVersion($this->getAvailableVersion());
    }

    /**
     * Delete all defined Nucleus Elements content
     */
    public function doFullDelete()
    {
        $this->_deleteContent(true);
        Mage::helper('cls_nucleuscore/elements')->unsetElementsVersion();
    }

    /**
     * Load the import file
     */
    protected function _loadImportData()
    {
        $helper = Mage::helper('cls_nucleuscore');
        $importFilePath = Mage::getModuleDir('etc', 'CLS_NucleusCore') . DS . self::FILE_ELEMENTS_IMPORT;

        if (!file_exists($importFilePath)) {
            Mage::throwException($helper->__('Could not find Nucleus Elements import file.'));
        }

        if (!is_readable($importFilePath)) {
            Mage::throwException($helper->__('Could not read Nucleus Elements import file.'));
        }

        $importFileData = file_get_contents($importFilePath);
        try {
            $this->_importXml = @new SimpleXmlElement($importFileData);
        } catch (Exception $e) {
            Mage::throwException($helper->__('Nucleus Elements import file was not valid XML.'));
        }
    }

    /**
     * Add to messages collection
     *
     * @param string $message
     */
    protected function _addMessage($message)
    {
        $this->_messages[] = $message;
    }

    /**
     * Get all messages that have been generated
     *
     * @param bool $clear
     * @return array
     */
    public function getMessages($clear=false)
    {
        $messages = $this->_messages;
        if ($clear) {
            $this->_messages = array();
        }
        return $messages;
    }

    /**
     * Create new pages/blocks, or update existing
     */
    protected function _createContent()
    {
        $helper = Mage::helper('cls_nucleuscore');

        if ($createXml = $this->_importXml->create) {
            if ($blocks = $createXml->blocks) {
                $blocksUpdated = 0;
                $blocksCreated = 0;
                foreach ($blocks->block as $block) {
                    if (!$block->identifier || !$block->title) {
                        continue;
                    }
                    $blockData = array(
                        'title' => (string) $block->title,
                        'identifier' => (string) $block->identifier,
                        'content' => ($block->content) ? (string) $block->content : '',
                        'is_active' => 1,
                        'stores' => array(0),
                    );

                    $block = Mage::getModel('cms/block')->load($blockData['identifier'], 'identifier');
                    $new = ($block->getId()) ? false : true;

                    $block->addData($blockData)
                        ->save();

                    if ($new) {
                        $blocksCreated++;
                    } else {
                        $blocksUpdated++;
                    }
                }

                if ($blocksUpdated > 0) {
                    $this->_addMessage($helper->__('%s Nucleus Elements block%s updated.', (string) $blocksUpdated, ($blocksUpdated > 1) ? 's' : ''));
                }

                if ($blocksCreated > 0) {
                    $this->_addMessage($helper->__('%s Nucleus Elements block%s created.', (string) $blocksCreated, ($blocksCreated > 1) ? 's' : ''));
                }
            }

            if ($pages = $createXml->pages) {
                $pagesUpdated = 0;
                $pagesCreated = 0;
                foreach ($pages->page as $page) {
                    if (!$page->identifier || !$page->title) {
                        continue;
                    }
                    $pageData = array(
                        'title' => (string) $page->title,
                        'root_template' => ($page->root_template) ? (string) $page->root_template : 'one_column',
                        'identifier' => (string) $page->identifier,
                        'content_heading' => ($page->content_heading) ? (string) $page->content_heading : null,
                        'content' => ($page->content) ? (string) $page->content : '',
                        'layout_update_xml' => ($page->layout_update_xml) ? (string) $page->layout_update_xml : null,
                        'is_active' => 1,
                        'stores' => array(0),
                    );

                    $page = Mage::getModel('cms/page')->load($pageData['identifier'], 'identifier');
                    $new = ($page->getId()) ? false : true;

                    $page->addData($pageData)
                        ->save();

                    if ($new) {
                        $pagesCreated++;
                    } else {
                        $pagesUpdated++;
                    }
                }

                if ($pagesUpdated > 0) {
                    $this->_addMessage($helper->__('%s Nucleus Elements page%s updated.', (string) $pagesUpdated, ($pagesUpdated > 1) ? 's' : ''));
                }

                if ($pagesCreated > 0) {
                    $this->_addMessage($helper->__('%s Nucleus Elements page%s created.', (string) $pagesCreated, ($pagesCreated > 1) ? 's' : ''));
                }
            }
        }
    }

    /**
     * Delete Nucleus Elements content.  (Always deletes content explicitly marked in import data. Can also delete any other content defined.)
     *
     * @param bool $all
     */
    protected function _deleteContent($all=false)
    {
        $helper = Mage::helper('cls_nucleuscore');

        $nodes = array(
            $this->_importXml->delete
        );
        if ($all) {
            $nodes[] = $this->_importXml->create;
        }

        $pagesDeleted = 0;
        $blocksDeleted = 0;
        foreach ($nodes as $deleteXml) {
            if ($deleteXml) {
                if ($pages = $deleteXml->pages) {
                    foreach ($pages->page as $page) {
                        if (!$page->identifier) {
                            continue;
                        }

                        $page = Mage::getModel('cms/page')->load((string)$page->identifier, 'identifier');
                        if ($page->getId()) {
                            $page->delete();
                            $pagesDeleted++;
                        }
                    }
                }

                if ($blocks = $deleteXml->blocks) {
                    foreach ($blocks->block as $block) {
                        if (!$block->identifier) {
                            continue;
                        }

                        $block = Mage::getModel('cms/block')->load((string)$block->identifier, 'identifier');
                        if ($block->getId()) {
                            $block->delete();
                            $blocksDeleted++;
                        }
                    }
                }
            }
        }

        if ($pagesDeleted > 0) {
            $this->_addMessage($helper->__('%s Nucleus Elements page%s deleted.', (string) $pagesDeleted, ($pagesDeleted > 1) ? 's' : ''));
        }
        if ($blocksDeleted > 0) {
            $this->_addMessage($helper->__('%s Nucleus Elements block%s deleted.', (string) $blocksDeleted, ($blocksDeleted > 1) ? 's' : ''));
        }
    }
}