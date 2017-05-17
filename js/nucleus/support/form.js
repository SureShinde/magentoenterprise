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
 * @package    Support
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

var NucleusSupportTicket = Class.create({
    
    initialize: function(config) {        
       this.config = config;  
        
       Event.observe($(this.config.button), 'click', function() {
            $(this.config.button).hide();
            $(this.config.form).show();
       }.bind(this));
       
       Event.observe($(this.config.close), 'click', function() {
            $(this.config.button).show();
            $(this.config.form).hide();
       }.bind(this));
       
       Event.observe($(this.config.detailSign), 'mouseover', function() {
            $(this.config.details).show();
       }.bind(this));
       
       Event.observe($(this.config.detailSign), 'mouseout', function() {
            $(this.config.details).hide();
       }.bind(this));
       
       $(this.config.formHtml).onsubmit = function() {
           
           var validate = true;;  
           if(config.subject.value.blank()) {
               config.hintSubject.show();
               config.subject.style.borderColor = '#C00';
               validate = false;
           }  
          
           if(config.message.value.blank()) {
               config.hint.show();
               config.message.style.borderColor = '#C00';
               validate = false;
           }
           
           if(!validate) {
               return false;
           }
          
           $('loading-mask').show();
           $(config.form).hide();
           
           return true;
       }
      
    }
});