<?php
/**
 * WDCA - Sweet Tooth
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS 
 * License, which extends the Open Software License (OSL 3.0).

 * The Open Software License is available at this URL: 
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * DISCLAIMER
 * 
 * By adding to, editing, or in any way modifying this code, WDCA is 
 * not held liable for any inconsistencies or abnormalities in the 
 * behaviour of this code. 
 * By adding to, editing, or in any way modifying this code, the Licensee
 * terminates any agreement of support offered by WDCA, outlined in the 
 * provided Sweet Tooth License. 
 * Upon discovery of modified code in the process of support, the Licensee 
 * is still held accountable for any and all billable time WDCA spent 
 * during the support process.
 * WDCA does not guarantee compatibility with any other framework extension. 
 * WDCA is not responsbile for any inconsistencies or abnormalities in the
 * behaviour of this code if caused by other framework extension.
 * If you did not receive a copy of the license, please send an email to 
 * support@sweettoothrewards.com or call 1.855.699.9322, so we can send you a copy 
 * immediately.
 * 
 * @category   [TBT]
 * @package    [TBT_Reports]
 * @copyright  Copyright (c) 2014 Sweet Tooth Inc. (http://www.sweettoothrewards.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Metrics Collection Helper for Orders Repeat Purchase Count
 *
 * @category   TBT
 * @package    TBT_Reports
 * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Reports_Helper_Adminhtml_Metrics_Orders_RepeatPurchase
    extends Mage_Adminhtml_Helper_Dashboard_Abstract
{
    /**
     * Total Before Range period
     * @var int|float 
     */
    protected $_totalBeforePeriod;
    
    /**
     * Prepare Collection used for generating Graph
     */
    protected function _initCollection()
    {
        $range = $this->getParam('period');
        $this->_collection = Mage::getResourceModel('tbtreports/order_collection')
            ->prepareMetricsOrdersRepeatPurchase($range);
    }
    
    /**
     * Getter for Total Before Range period
     * @return int|float
     */
    public function getTotalBeforePeriod()
    {
        if ($this->_totalBeforePeriod) {
            return $this->_totalBeforePeriod;
        }
        
        $range = $this->getParam('period');
        
        $this->_totalBeforePeriod = Mage::getResourceModel('tbtreports/order_collection')
            ->getTotalOrdersRepeatPurchaseBeforePeriod($range);
        
        return $this->_totalBeforePeriod;
    }
    
    /**
     * Getter for Total By Date
     * @return int|float
     */
    public function getTotalByDate($startDate = null, $after = true)
    {
        return Mage::getResourceModel('tbtreports/order_collection')
            ->getTotalOrdersRepeatPurchaseByDate($startDate, $after);
    }
}