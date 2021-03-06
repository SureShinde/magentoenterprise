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
 * Rule controller image
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Block_Checkout_Cart_Rulectrlimg extends Mage_Core_Block_Template {
	
	public function getTemplate() {
		$template = 'rewards/checkout/cart/rulectrlimg.phtml';
		return $template;
	}
	
	/**
	 * Generates that the user can click on to apply or remove rules
	 * ALIAS FOR init() but also returns rendered html for this block
	 *
	 * @param int $rule_id
	 * @param bool $is_add
	 * @param bool $is_cart 
	 * @param int $item_id
	 * @return TBT_Rewards_Block_Checkout_Cart_Rulectrlimg $this
	 */
	public function genRuleCtrlImg($rule_id, $is_add = true, $is_cart = true, $item_id = 0, $redemption_instance_id = 0, $callback = "true") {
		$this->init ( $rule_id, $is_add, $is_cart, $item_id, $redemption_instance_id, $callback );
		return $this->toHtml ();
	}
	
	/**
	 * Generates that the user can click on to apply or remove rules
	 *
	 * @param int $rule_id
	 * @param bool $is_add
	 * @param bool $is_cart 
	 * @param int $item_id
	 * @return TBT_Rewards_Block_Checkout_Cart_Rulectrlimg $this
	 */
	public function init($rule_id, $is_add = true, $is_cart = true, $item_id = 0, $redemption_instance_id = 0, $callback = "true") {
		
		$this->unsetData ( "callback" );
		$this->unsetData ( "url" );
		$this->unsetData ( "src" );
		
		$rule_type = ($is_cart ? 'cart' : 'catalog');
		$src = Mage::getDesign ()->getSkinUrl ( 'images/rewards/' . ($is_add ? 'add' : 'remove') . '.gif' );
		$url_key = 'rewards/cart_redeem/' . ($is_add ? $rule_type . 'add' : $rule_type . 'remove');
		$params = array ('rids' => $rule_id );
		if (! empty ( $redemption_instance_id ))
			$params ['inst_id'] = $redemption_instance_id;
		if (! $is_cart) {
			$params ['item_id'] = $item_id;
		}
		$url = $this->getUrl ( $url_key, $params );
		
		if (empty ( $callback ))
			$callback = "true";
		$this->setCallback ( $callback );
		$this->setActionUrl ( $url );
		$this->setUrl ( $url );
		$this->setSrc ( $src );
		return $this;
	}

}
