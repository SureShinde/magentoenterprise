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
 * @category   Nucleus
 * @package    Cart
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

/**
 * Extension of VarienForm specifically to replace product detail page Add to Cart form, for added Ajax capability
 */
var AjaxAddCartForm = Class.create();
AjaxAddCartForm.prototype = Object.extend(new VarienForm(), {
    /**
     * Initialize needed info from the page
     *
     * @param {string} standardUrl
     * @param {string} id
     */
    ajaxInit : function(standardUrl, id) {
        this.standardUrl = standardUrl;
        this.productId = id;
    },

    /**
     * Redefine submit to allow for Ajax
     *
     * @param {object} button
     * @param {string} url
     */
    submit : function(button, url) {
       if(this.validator.validate()) {
           var buttonExists = false;
           if (button && button != 'undefined') {
               buttonExists = true;
               button.disabled = true;
           }

           // Determine what URL to test against
           var testUrl = url;
           if (buttonExists && button.tagName.toLowerCase() == 'a' && button.href) {
               // This is specifically to accommodate the PayPal buttons that should continue to operate as normal
               testUrl = button.href;
           }
           if (!testUrl) {
               testUrl = this.form.action;
           }

           var ajaxAdded = false;
           // We test to see if the URL we got is the same as the standard Add to Cart URL.
           // If not, then adding to cart isn't what this submit is doing, so we leave it alone.
           if (typeof(ActiveMinicart) != 'undefined' && this.standardUrl && testUrl == this.standardUrl) {
               ajaxAdded = ActiveMinicart.addToCart(this.form.serialize(), this.productId, function () {
                   if (buttonExists) {
                       button.disabled = false;
                   }

                   // After adding is complete, we need to clear out the Related Products checkboxes
                   var checkboxes = $$('.related-checkbox');
                   for (var i = 0; i < checkboxes.length; i++) {
                       checkboxes[i].checked = false;
                   }
                   if (typeof(window.addRelatedToProduct) != 'undefined') {
                       addRelatedToProduct();
                   }
               });
           }

           // If Ajax Add didn't happen (not an Add to Cart operation, or Ajax Add isn't enabled), perform the standard overridden submit
           if (!ajaxAdded) {
               var form = this.form;
               var oldUrl = form.action;

               if (url) {
                   form.action = url;
               }
               var e = null;
               try {
                   this.form.submit();
               } catch (e) {
               }
               this.form.action = oldUrl;
               if (e) {
                   throw e;
               }
           }
       }
    }
});