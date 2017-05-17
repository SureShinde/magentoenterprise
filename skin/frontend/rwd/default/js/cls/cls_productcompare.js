/**
 * @category   CLS
 * @package    ProductCompare
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

ClsProductCompare = {
    // var compareBaseUrls defined in list.phtml
    compareBaseUrls: {
        'add': null
        , 'remove': null
    }

    , init: function()
    {
        var that = this;

        // Initialize compare functionality
        this.initCompare();
    }

    /**
     * Initialize all compare functionality
     */
    , initCompare: function()
    {
        var that = this;

        // Proceed if base URLs have been defined
        if (typeof clsProductCompareAddBaseUrl != 'undefined') {
            this.compareBaseUrls.add = clsProductCompareAddBaseUrl;
        }

        if (typeof clsProductCompareRemoveBaseUrl != 'undefined') {
            this.compareBaseUrls.remove = clsProductCompareRemoveBaseUrl;
        }

        if (this.compareBaseUrls.add == null || this.compareBaseUrls.remove == null) {
            return;
        }

        //Replace compare links with checkboxes and labels.
        this.replaceCompareLinks();

        // On checkbox change
        jQuery('input.compare-checkbox').on('change', function(){
            that.compareDetailChange(this);
        });

        // Set up Remove buttons in the Compare summary
        jQuery("#product_compare_load").on('click', '.compare-remove', function(){
            that.compareSummaryRemove(this);
            return false;
        });

        // Initialize the checkboxes for products already being compared
        var compareCookie = jQuery.cookie('COMPARED_PRODUCTS');
        if (typeof compareCookie == 'string') {
            var clsCompareIds = compareCookie.split(',')
                , $clsCompareCheckbox;
            for (var i=0; i<clsCompareIds.length; i++) {
                $clsCompareCheckbox = jQuery('#compare_' + clsCompareIds[i]);
                if ($clsCompareCheckbox.length > 0) {
                    $clsCompareCheckbox.prop('checked', true);
                }
            }
        }
    }

    /**
     * Replace all compare links on the page with checkboxes.
     */
    , replaceCompareLinks: function() {
        jQuery('a.link-compare').each(function(){
            var link = jQuery(this);
            var href = link.attr('href');
            if (typeof href == 'string') {
                var matches = href.match(/\/product\/([0-9]*)/);
                if (matches.length == 2) {
                    var productId = matches[1];
                    var checkboxHtml = '';
                    checkboxHtml += '<input id="compare_' + productId + '" class="compare-checkbox" type="checkbox" rel="' + productId + '" name="compare[' + productId + ']" autocomplete="off">';
                    checkboxHtml += '<span class="loading no-display">Loading...</span>';
                    checkboxHtml += '<label class="link-compare" for="compare_' + productId + '">Compare</label>';

                    var linkParent = link.parent();
                    if (linkParent.prop('tagName') == 'LI') {
                        linkParent.addClass('compare-checkbox-wrapper');
                    }

                    link.replaceWith(jQuery(checkboxHtml));
                }
            }
        });
    }

    /**
     * Trigger addition/removal of product compare items from the item's checkbox
     *
     * @param  DOMElement el
     */
    , compareDetailChange: function(el)
    {
        var that = this;

        var $el = jQuery(el)
            , productId = $el.attr('rel')
            , add = $el.prop('checked')
            , $item = $el.parents('.item')
            , $siblings = $el.siblings().addClass('no-display')

        // Get the loading element and change its text and class
            , $loadingEl = $siblings
                .filter('.loading')
                .html(add ? "Adding..." : "Removing...")
                .removeClass('adding removing')
                .addClass(add ? "adding" : "removing")

        // Get the standard label
            , $label = $siblings
                .filter('label')
            ;

        // Send the request
        this.ajaxCompareUpdate(productId, add, {
            $loadingEl: $loadingEl

            , error: function() {
                // Change label text to error
                $label.html('<span class="error-lite">Error</span>');

                // Reverse the checkbox back to original state
                if($el.prop('checked')) {
                    $el.prop('checked', false);
                } else {
                    $el.prop('checked', true);
                }

                // Show error
                that.displayCompareError($label, {
                    // Callback for timeout after error has been shown
                    timeoutAfter: function() {
                        // Change label back to normal
                        $label.html("Compare").removeClass('no-display');
                    }
                });
            }

            , success: function() {
                // Show standard label again
                $label.removeClass('no-display');
            }
        });
    }

    /**
     * Trigger removal of product compare items from summary
     *
     * @param  DOMElement el
     */
    , compareSummaryRemove: function(el)
    {
        var that = this;

        var $el = jQuery(el)
            , productId = $el.attr('rel')
            , $parent = $el.parents('#product_compare_load')

            , $loadingEl = $parent
                .find('.loading')
                .removeClass('no-display')

            , $errorEl = $parent
                .find('.error-lite')
            ;

        // As long as we have a product ID, send the request
        if (productId) {
            this.ajaxCompareUpdate(productId, false, {
                $loadingEl: $loadingEl

                , error: function() {
                    // Show error
                    that.displayCompareError($errorEl);
                }

                , success: function() {
                    // On success, uncheck the product item's checkbox
                    var $item = jQuery('#compare_'+productId)
                        .prop('checked', false)
                        .parents('.item');
                }
            });
        }
    }

    /**
     * Send the request for adding/removing compare items
     *
     * @param  string productId
     * @param  bool add
     * @param  object options
     */
    , ajaxCompareUpdate: function(productId, add, options)
    {
        if (!options) {
            options = {};
        }

        // Get appropriate URL
        compareUrl = add ? this.compareBaseUrls.add + productId : this.compareBaseUrls.remove + productId;

        // Show loading element
        if (options.$loadingEl) {
            options.$loadingEl.removeClass('no-display');
        }

        // Send request
        jQuery.ajax({
            url: compareUrl
            , dataType: 'json'
            , statusCode: {
                404: function(){
                    options.$loadingEl.addClass('no-display');
                    if (options.error) {
                        options.error();
                    }
                }
            }
        }).done(function(data){
                options.$loadingEl.addClass('no-display');

                if(data.success) {
                    // Update the summary section
                    if (data.summary_update) {
                        jQuery('#product_compare_load').html(data.summary_update);
                    }

                    // Trigger any callback
                    if (options.success) {
                        options.success();
                    }
                } else {
                    if (options.error) {
                        options.error();
                    }
                }
            }).fail(function(data){
                options.$loadingEl.addClass('no-display');
                if (options.error) {
                    options.error();
                }
            });
    }

    /**
     * Display the appropriate error element
     *
     * @param  jQuery $el
     * @param  object options
     */
    , displayCompareError: function($el, options)
    {
        if (!options) {
            options = {};
        }

        // Show it
        $el.removeClass('no-display');

        // Set timeout to hide it again and trigger any callback
        setTimeout(function() {
            $el.addClass('no-display');
            if (options.timeoutAfter) {
                options.timeoutAfter();
            }
        }, 2000);
    }
}

jQuery(document).ready(function(){
    ClsProductCompare.init();
});
