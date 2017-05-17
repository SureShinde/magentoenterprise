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

class CLS_AdminEnhance_Model_Resource_Banner extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize main table and table id field
     */
    protected function _construct()
    {
        $this->_init('cls_adminenhance/banner', 'banner_id');
    }

    /**
     * Save the links to videos
     *
     * @param CLS_AdminEnhance_Model_Banner $banner
     * @return CLS_AdminEnhance_Model_Resource_Banner
     */
    public function saveVideoLinks($banner)
    {
        // Get all the current links between this banner and videos
        $select = $this->_getReadAdapter()->select()
            ->from(
                array($this->getTable('cls_adminenhance/banner_video_link')),
                array('video_id')
            )
            ->where('banner_id = ?', $banner->getId());
        $existingVideoIds = $this->_getReadAdapter()->fetchCol($select);

        // Get the videos that are attached to the current model
        $newVideoIds = array();
        foreach ($banner->getVideos() as $video) {
            $newVideoIds[] = $video->getId();
        }

        // Compare old and new to determine which links to delete and which to insert
        $delete = array_diff($existingVideoIds, $newVideoIds);
        $insert = array_diff($newVideoIds, $existingVideoIds);

        // Delete
        if (!empty($delete)) {
            $this->_getWriteAdapter()->delete($this->getTable('cls_adminenhance/banner_video_link'),
                array(
                    'banner_id = ?' => $banner->getId(),
                    'video_id IN (?)' => $delete,
                )
            );
        }

        // Insert
        if (!empty($insert)) {
            $insertRows = array();
            foreach ($insert as $videoId) {
                $insertRows[] = array(
                    'banner_id' => $banner->getId(),
                    'video_id'  => $videoId,
                );
            }
            $this->_getWriteAdapter()->insertMultiple($this->getTable('cls_adminenhance/banner_video_link'), $insertRows);
        }

        return $this;
    }
}
