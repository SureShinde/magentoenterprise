<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ReCaptcha_Model_Observer
{
	/**
	 * Inject the Captcha HTML/JS into the comment forms
	 *
	 * @param Varien_Event_Observer $observer
	 * @return $this
	 */
	public function injectPostCommentsCaptchaObserver(Varien_Event_Observer $observer)
	{
		$layout = Mage::getSingleton('core/layout');

		if (($form = $layout->getBlock('wp.post.view.comments.form.before_end')) !== false) {
			$form->append(
				Mage::getSingleton('core/layout')->createBlock('wp_addon_recaptcha/captcha'), 'recaptcha'
			);
		}

		return $this;
	}
}
