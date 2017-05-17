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
class CLS_NucleusCore_Block_Adminhtml_System_Config_Nucleus_Elements
    extends Mage_Adminhtml_Block_Template implements Varien_Data_Form_Element_Renderer_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('cls/nucleus/system/elements.phtml');
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->toHtml();
    }

    /**
     * URL for installing Elements data
     *
     * @return string
     */
    public function getInstallUrl()
    {
        return $this->getUrl('adminhtml/nucleus_elements/install');
    }

    /**
     * URL for uninstalling Elements data
     *
     * @return string
     */
    public function getUninstallUrl()
    {
        return $this->getUrl('adminhtml/nucleus_elements/uninstall');
    }
}