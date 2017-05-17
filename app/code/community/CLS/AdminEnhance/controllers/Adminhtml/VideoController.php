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
 * @package    AdminEnhance
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

class CLS_AdminEnhance_Adminhtml_VideoController extends Mage_Adminhtml_Controller_Action
{
    /**
     * For viewing embedded Vimeo videos
     */
    public function vimeoAction()
    {
        if (($videoId = $this->getRequest()->getParam('video'))
            && ($video = Mage::getModel('cls_adminenhance/video')->load($videoId))
            && $video->getId()) {
            Mage::register('video', $video);

        }

        $this->loadLayout();
        $this->renderLayout();
    }
}