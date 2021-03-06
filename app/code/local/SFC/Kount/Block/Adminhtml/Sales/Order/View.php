<?php
// @codingStandardsIgnoreStart
/**
 * StoreFront Consulting Kount Magento Extension
 *
 * PHP version 5
 *
 * @category  SFC
 * @package   SFC_Kount
 * @copyright 2009-2013 StoreFront Consulting, Inc. All Rights Reserved.
 *
 */
// @codingStandardsIgnoreEnd

class SFC_Kount_Block_Adminhtml_Sales_Order_View extends Mage_Adminhtml_Block_Sales_Order_View
{

    /**
     * Constructor
     */
    public function __construct()
    {
        // Construct
        parent::__construct();

        // Add AWC button
        $oPathHelper = new SFC_Kount_Helper_Paths();
        // -- Is Kount enabled?
        if (!Mage::getStoreConfig('kount/account/enabled')) {
            Mage::log('Kount not enabled by system configuration, skipping adding AWC button.', Zend_Log::INFO,
                SFC_Kount_Helper_Paths::KOUNT_LOG_FILE);
        }
        // -- Validate config
        else {
            if ($oPathHelper->validateConfig()) {
                // Get transaction id
                $risInfo = get_object_vars(json_decode($this->getOrder()->getPayment()->getAdditionalInformation('ris_additional_info')));
                // -- -- Add button
                $sUrl = Mage::getStoreConfig('kount/servers/awc');
                if (isset($risInfo['TRAN'])) {
                    $sUrl .= '/workflow/detail.html?id=' . $risInfo['TRAN'];
                }
                $this->_addButton('kount_awc', array(
                    'label' => Mage::helper('Sales')->__('Kount AWC'),
                    'href' => Mage::getStoreConfig('kount/servers/awc'),
                    'onclick' => "window.open('{$sUrl}')",
                    'class' => 'go'
                ), 0, 100, 'header', 'header');
            }
            // -- Not configured
            else {
                Mage::log('Kount settings not configured, skipping adding AWC button.', Zend_Log::INFO,
                    SFC_Kount_Helper_Paths::KOUNT_LOG_FILE);
            }
        }
    }

    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

}