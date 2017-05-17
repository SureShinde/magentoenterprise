/**
 * Crius
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt
 *
 * @category   Crius
 * @package    Crius_Rac
 * @copyright  Copyright (c) 2011 Crius (http://www.criuscommerce.com)
 * @license    http://www.criuscommerce.com/CRIUS-LICENSE.txt
 */
var RegisterAfterCheckout = Class.create();
RegisterAfterCheckout.prototype = {
    initialize: function(form, registerUrl) {
        this.form = form;
        this.registerUrl = registerUrl;
        // Stop normal submit action and handle with AJAX instead
        if ($(this.form)) {
            $(this.form).observe('submit', function(event){this.register();Event.stop(event);}.bind(this));
        }
        // Set function handles for AJAX response
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
        this.onSuccess = this.afterRegisterResponse.bindAsEventListener(this);
        this.loadWaiting = false;
    },
    
    /**
     * Show/hide spinner and disable/enable submit button when AJAX is in
     * progress (wait=true) or finished (wait=false)
     */
    setLoadWaiting: function(wait) {
        var container = $('register-buttons-container');
        if (wait) {
            container.addClassName('disabled');
            container.setStyle({opacity:0.5});
            Element.show('register-please-wait');
        } else {
            if (this.loadWaiting) {
                container.removeClassName('disabled');
                container.setStyle({opacity:1});
                Element.hide('register-please-wait');
            }
        }
        this.loadWaiting = wait;
    },
    
    resetLoadWaiting: function(transport) {
        this.setLoadWaiting(false);
    },
    
    register: function() {
        // Abort if register is already in progress
        if (this.loadWaiting!=false) return;
        
        // Validate password
        var validator = new Validation(this.form);
        if (validator.validate()) {
            this.setLoadWaiting(true);
            // Submit password to AJAX controller
            var request = new Ajax.Request(
                this.registerUrl,
                {
                    method: 'post',
                    onComplete: this.onComplete,
                    onSuccess: this.onSuccess,
                    parameters: Form.serialize(this.form)
                }
            );
        }
    },
    
    /**
     * Handle AJAX response, including success and error
     */
    afterRegisterResponse: function(transport) {
        // Parse response object
        if (transport && transport.responseText) {
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                response = {};
            }
        }
        // If error response, show error msg
        if (response.error) {
            if ((typeof response.message) == 'string') {
                alert(response.message);
            } else {
                alert(response.message.join("\n"));
            }
            return;
        }
        // If success, redirect to success URL
        if (response.redirect) {
            location.href = response.redirect;
            return;
        }
    }
}
