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
 * @package    [TBT_Rewards]
 * @copyright  Copyright (c) 2014 Sweet Tooth Inc. (http://www.sweettoothrewards.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Transfer Reference
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Model_Tag_Reference extends TBT_Rewards_Model_Transfer_Reference_Abstract {
	const REFERENCE_TYPE_ID = 4;
	
	public function clearReferences(&$transfer) {
		if ($transfer->hasData ( 'tag_id' )) {
			$transfer->unsetData ( 'tag_id' );
		}
		return $this;
	}
	
	public function getReferenceOptions() {
		$reference_options = array (self::REFERENCE_TYPE_ID => Mage::helper ( 'rewards' )->__ ( 'Tag' ) );
		return $reference_options;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see TBT_Rewards_Model_Transfer_Reference_Abstract::loadReferenceInformation()
	 */
	public function loadReferenceInformation(&$transfer) {
		$this->loadTransferId ( $transfer );
		return $this;
	}
	
	/**
	 * 
	 * @param TBT_Rewards_Model_Transfer $transfer
	 * @param int $id
	 */
	public function loadTransferId($transfer) {
		$id = $transfer->getReferenceId ();
		$transfer->setReferenceType ( TBT_Rewards_Model_Tag_Reference::REFERENCE_TYPE_ID );
		$transfer->setReferenceId ( $id );
		$transfer->setData ( 'tag_id', $id );
		
		return $this;
	}

}