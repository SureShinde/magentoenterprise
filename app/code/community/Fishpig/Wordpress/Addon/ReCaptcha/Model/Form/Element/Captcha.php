<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress_Addon_ReCaptcha
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ReCaptcha_Model_Form_Element_Captcha extends Varien_Data_Form_Element_Abstract
{
	/**
	 * Retrieve the ReCaptcha HTML
	 *
	 * @return string
	 */
	public function getElementHtml()
	{
		return Mage::getSingleton('core/layout')
			->createBlock('wp_addon_recaptcha/captcha')
			->toHtml();
	}
}
