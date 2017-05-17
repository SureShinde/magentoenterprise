<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress_Addon_ReCaptcha
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ReCaptcha_Block_Captcha extends Mage_Core_Block_Text
{
	/**
	 * Determine whether the plugin is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return Mage::helper('wordpress')->isPluginEnabled('wp-recaptcha/wp-recaptcha.php');
	}

	/**
	 * Set the ReCaptcha HTML
	 *
	 * @return $this
	 */
	public function _beforeToHtml()
	{
		if ($this->isEnabled()) {
			$this->setText($this->getRecaptchaHtml());

			return parent::_beforeToHtml();
		}
		
		return false;
	}

	/**
	 * Retrieve the ReCaptcha HTML
	 *
	 * @return string
	 */
	public function getRecaptchaHtml()
	{
		if (!$this->isEnabled() || !$this->getPublicKey()) {
			return '';
		}

		if (Mage::getSingleton('customer/session')->isLoggedIn() && $this->getPluginOption('bypass_for_registered_users')) {
			return '';
		}

		$html = sprintf('<div class="g-recaptcha" data-sitekey="%s" data-theme="standard"></div><script type="text/javascript"src="https://www.google.com/recaptcha/api.js?hl=en"></script><div id="recaptcha-submit-btn-area">&nbsp;</div>', $this->getPublicKey());
		
		$noscript = '<noscript><style type=\'text/css\'>#submit {display:none;}</style><input name="submit" type="submit" id="submit-alt" tabindex="6" value="Submit Comment"/> </noscript>	';
            
         return $html . $noscript;;
	}
	
	/**
	 * Retrieve the public key
	 *
	 * @return string
	 */
	public function getPublicKey()
	{
		return $this->getPluginOption('site_key');
	}
	
	/**
	 * Retrieve a plugin option
	 *
	 * @param string $key = null
	 * @return mixed
	 */
	public function getPluginOption($key = null)
	{
		return Mage::helper('wordpress')->getPluginOption('recaptcha_options', $key);
	}
}
