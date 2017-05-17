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

class CLS_AdminEnhance_Adminhtml_Support_BannersController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Fetch the matching locations/banners for a certain URL
     */
    public function ajaxFetchAction()
    {
        $response = array();

        try {
            if (!($url = $this->getRequest()->getParam('match_url'))) {
                Mage::throwException(Mage::helper('cls_adminenhance')->__('No URL specified in POST data'));
            }

            // Get all the locations that match the passed URL
            $locations = Mage::getModel('cls_adminenhance/location')->getCollection()
                ->addUrlFilter($url)
                ->addBanners();

            // Formulate array of banner information
            $bannersInfo = array();
            foreach ($locations as $location) {
                if (!($banner = $location->getBanner())) {
                    continue;
                }

                $locationHtmlId = 'banner_location_' . $location->getId();
                $bannerBlock = $this->getLayout()->createBlock('cls_adminenhance/adminhtml_support_banner')
                    ->setBanner($banner)
                    ->setLocationId($location->getId())
                    ->setLocationHtmlId($locationHtmlId);

                $bannersInfo[] = array(
                    'locationId' => $location->getId(),
                    'locationHtmlId' => $locationHtmlId,
                    'selector' => $location->getSelector(),
                    'selectorContext' => $location->getSelectorContext(),
                    'content' => $bannerBlock->toHtml(),
                );
            }

            $response['banners'] = $bannersInfo;
        } catch (Exception $e) {
            Mage::logException($e);
            $response['error'] = true;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Remember collapse/expand of banner for admin user
     */
    public function ajaxToggleAction()
    {
        if ($locationId = $this->getRequest()->getParam('location_id')) {
            Mage::helper('cls_adminenhance')->toggleUserBannerLocation($locationId, (bool) $this->getRequest()->getParam('collapse'));
        }
    }
}