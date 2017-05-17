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

class CLS_AdminEnhance_Block_Adminhtml_Support_Videos extends Mage_Adminhtml_Block_Template
{
    const VIDEO_THUMBNAIL_BASE_URL = 'cls/adminenhance/images/thumbnails/';

    protected function _construct()
    {
        $this->setTemplate('cls/adminenhance/support/videos.phtml');

        parent::_construct();
    }

    /**
     * Get videos to list in the block
     *
     * @return CLS_AdminEnhance_Model_Resource_Video_Collection
     */
    public function getVideos()
    {
        if ($banner = $this->getBanner()) {
            // If a banner is set, get only videos for that banner
            $videos = $banner->getVideos();
        } else {
            // Otherwise, get all videos
            $videos = Mage::getModel('cls_adminenhance/video')->getCollection();
        }

        return $videos;
    }

    /**
     * Get URL to the video pop-up for a certain embed code
     *
     * @param int|CLS_AdminEnhance_Model_Video $videoId
     * return string
     */
    public function getVideoUrl($videoId)
    {
        if ($videoId instanceof CLS_AdminEnhance_Model_Video) {
            $videoId = $videoId->getId();
        }

        $params = array(
            'video' => $videoId,
        );
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/video/vimeo', $params);
    }

    /**
     * Get the URL for a particular video thumbnail image
     *
     * @param string $filename
     * @return string
     */
    public function getThumbnailUrl($filename)
    {
        return Mage::getDesign()->getSkinUrl(self::VIDEO_THUMBNAIL_BASE_URL . $filename);
    }
}