/**
 * @category   CLS
 * @package    CustomerAdvanced
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

Captcha.prototype.refresh = function(elem, callback)
{
    formId = this.formId;
    if (elem) Element.addClassName(elem, 'refreshing');
    new Ajax.Request(this.url, {
        onSuccess: function (response) {
            if (response.responseText.isJSON()) {
                var json = response.responseText.evalJSON();
                if (!json.error && json.imgSrc) {
                    $(formId).writeAttribute('src', json.imgSrc);
                    if (elem) Element.removeClassName(elem, 'refreshing');
                } else {
                    if (elem) Element.removeClassName(elem, 'refreshing');
                }
                // Introduced this to allow for a custom callback
                if (callback) {
                    callback();
                }
            }
        },
        method: 'post',
        parameters: {
            'formId'   : this.formId
        }
    });
}



var CLS_QuickLogin;

(function(jQ) {

CLS_QuickLogin = {
    quickAccessItems: null,     // Array of all the quick access sections
    baseUrlPattern: null,       // Base URL of the site
    restrictedUrlPatterns: null, // Array of URL patterns that should be restricted to logged-in customers

    transitioning: false,       // Whether any item is currently in a transition state
    $shownSection: null,        // The section that is currently showing
    queuedShow: null,           // "Show" action waiting to be performed (if transition was in process when it was triggered)
    queuedHide: null,           // "Hide" action waiting to be performed (if transition was in process)
    lightbox: false,            // Whether there is a lightbox currently active on the page

    hideTimeout: 5000,          // How long to wait before hiding the current section
    queueTimeout: 200,          // How long to wait for re-try when transition is in process
    slideDuration: 500,         // Duration of sliding animation, in seconds
    scrollDuration: 100,        // Duration when scrolling to the proper section of page

    /**
     * Initialize functionality
     *
     * @param {array} items
     */
    init: function(items, baseUrl, restrictedUrlPatterns)
    {
        var that = this;

        this.quickAccessItems = items;
        if (baseUrl) {
            this.baseUrlPattern = baseUrl;
        }
        if (restrictedUrlPatterns) {
            this.restrictedUrlPatterns = restrictedUrlPatterns;
        }

        this.initItems();
        this.initRestrictedLinks();
    },

    /**
     * Initialize the individual quick access sections
     */
    initItems: function()
    {
        var that = this;

        jQ.each(this.quickAccessItems, function(index, item) {
            // Make sure the element was actually found
            if (item.$element.length > 0 && item.$trigger.length > 0) {
                item.$element.data('trigger', item.$trigger);

                // Move it to the parent element
                item.$trigger.parent().append(item.$element);

                // Store information about captcha
                that.initCaptcha(item.$element);

                // Set up the trigger for showing the section
                item.$trigger.addClass('quick-user-access-trigger')
                    .on('click', function() {
                        that.showSection(item.$element, true);
                        // Hide the account menu when in desktop resolutions
                        if (Modernizr.mq("screen and (min-width:" + (bp.medium + 1) + "px)")) {
                            if ($('header-account').hasClassName('skip-active')) {
                                $$('.skip-account')[0].click();
                            }
                        }
                        return false;
                    });

                // If there's an "alt link", set up the handler
                item.$element.find('.alt-access-link').on('click', function() {
                    var $link = jQuery(this);
                    var altLinkSection;
                    if (altLinkSection = $link.data('showSection')) {
                        that.showSection(altLinkSection);
                    }
                });
            }
        });

        // Register colorbox close event handler
        jQ(document).bind('cbox_cleanup', function(){
            that.hideCurrentShown();
            that.lightbox = false;
        });
    },

    /**
     * Store information about captcha to use later
     *
     * @param {object} $el
     */
    initCaptcha: function($el)
    {
        var $captchaBox = $el.find('.captcha-image');
        if ($captchaBox.length > 0) {
            var $captchaImg = $captchaBox.find('.captcha-img');
            if ($captchaImg.length > 0) {
                $captchaBox.data('captchaImg', $captchaImg);
                $el.data('captchaBox', $captchaBox);
                $el.data('captchaInitialized', false);
            }
        }
    },

    /**
     *
     */
    initRestrictedLinks: function()
    {
        var that = this;
        if (this.baseUrlPattern != null && this.restrictedUrlPatterns != null && this.restrictedUrlPatterns.length > 0) {
            this.baseUrlPattern = new RegExp('^' + this.baseUrlPattern.replace(/https?/, 'https?'));

            var patterns = this.restrictedUrlPatterns;
            var patternsLen = patterns.length;
            this.restrictedUrlPatterns = [];
            for (var x=0; x<patternsLen; x++) {
                this.restrictedUrlPatterns.push(new RegExp(patterns[x]));
            }

            jQ(document).on('click', 'a', function() {
                return that.globalLinkCheck(this);
            });
        }
    },

    /**
     * Handle any link clicked on the page
     *
     * @param {DOMElement} link
     * @return {bool}
     */
    globalLinkCheck: function(link)
    {
        var url = link.href;
        if (!this.urlIsRestricted(url)) {
            return true;
        } else {
            // Need to make sure we ignore the main Account link that just triggers the Account drop-down
            if (jQuery(link).is('.skip-account')) {
                return true;
            }

            var $sectionEl = this.getSectionById('login');

            this.setReferer($sectionEl, url);

            this.showSection($sectionEl, false, 'You must be logged in to access this section.');
            // Hide the account menu when in desktop resolutions
            if (Modernizr.mq("screen and (min-width:" + (bp.medium + 1) + "px)")) {
                if ($('header-account').hasClassName('skip-active')) {
                    $$('.skip-account')[0].click();
                }
            }
            return false;
        }
    },

    /**
     * Test to see if the URL matches a restricted pattern
     *
     * @param {string} url
     * @return {bool}
     */
    urlIsRestricted: function(url)
    {
        if (!this.baseUrlPattern.match(url)) {
            return false;
        }

        url = url.replace(this.baseUrlPattern, '');

        var patternsLen = this.restrictedUrlPatterns.length;
        for (var x=0; x<patternsLen; x++) {
            if (this.restrictedUrlPatterns[x].test(url)) {
                return true;
            }
        }
        return false;
    },

    /**
     * Set a different value for the "referer" input in a form
     *
     * @param {object} $el
     * @param {string} value
     */
    setReferer: function($el, value)
    {
        $el.find('input[name="unencoded_referer"]').val(value);
    },

    /**
     * Reset referer input to previous value
     *
     * @param {object} $el
     */
    resetReferer: function($el)
    {
        $el.find('input[name="unencoded_referer"]').val('');
    },

    /**
     * Get the quick access section element that matches an ID
     *
     * @param {string} id
     * @return {object}
     */
    getSectionById: function(id)
    {
        var result = false;
        var itemsLen = this.quickAccessItems.length;
        for (var x=0; x<itemsLen; x++) {
            if (this.quickAccessItems[x].id == id) {
                result = this.quickAccessItems[x].$element;
            }
        }
        return result;
    },

    /**
     * Show a particular section
     *
     * @param {object} $el
     * @param {bool} toggle
     * @param {string} alertMsg
     */
    showSection: function($el, toggle, alertMsg)
    {
        var that = this;

        if (typeof($el) == 'string') {
            $el = this.getSectionById($el);
            if (!$el) return;
        }

        // Cancel any other sections that were waiting to show
        clearTimeout(this.queuedShow);

        // Insert an alert message if we have one
        if (alertMsg) {
            $el.find('.quick-access-alert').html(alertMsg).addClass('show');
        }

        // As long as we're not currently in transition, hide any section that's showing already
        // That includes hiding the section that triggered this, if it was a toggle
        // If we ARE currently in transition, this entire method will get re-queued anyway
        if (!this.transitioning) {
            var $shownSection = this.$shownSection;
            if ($shownSection != $el || toggle) {
                this.hideCurrentShown(true);
            }
            if ($shownSection == $el) {
                if (!toggle) {
                    clearTimeout(this.queuedHide);
                    this.queuedHide = setTimeout(function() {that.hideCurrentShown()}, that.hideTimeout);
                }
                return;
            }
        }

        // A brand new check for whether we're transitioning, because the hide of the current section
        // might have resulted in a new transition
        if (!this.transitioning) {

            // Flag a transition and cancel any hides
            this.transitioning = true;
            clearTimeout(this.queuedHide);

            // Refresh the CAPTCHA
            this.refreshCaptcha($el);

            // Finishing steps to run once show action is completed
            $finish = function() {
                that.transitioning = false;
                that.$shownSection = $el;
                $el.find('*').filter(':input').filter('[type!="hidden"]').first().focus();
            };

            // Show the section
            if (Modernizr.mq("screen and (min-width:" + (bp.medium + 1) + "px)")) {
                // Show the actual content
                $($el.attr('id')).show();

                // Fire up colorbox
                jQuery.colorbox({
                    transition: 'none',
                    fadeOut: 0, // Prep for instant hide
                    close: '&#x2297;',
                    inline: true,
                    href: $el,
                    onComplete: function() {
                        that.lightbox = true;
                        $finish();
                    }
                });

                // Register to automatically hide the lightbox if the viewport scales down
                enquire.register('(max-width: ' + bp.medium + 'px)', {
                    match: function () {
                        if (that.lightbox == true) {
                            jQuery.colorbox.close();
                        }
                    }
                });
            } else {
                // Do the slide down effect
                new Effect.SlideDown($el.attr('id'), {
                    duration: this.slideDuration/1000,
                    afterFinish: function() {
                        jQ(document.body).animate({
                            'scrollTop': $el.offset().top
                        }, 500);
                        $finish();
                    }
                });

                // Register to automatically hide the section if the viewport scales up
                enquire.register('(min-width: ' + (bp.medium + 1) + 'px)', {
                    match: function () {
                        that.hideCurrentShown();
                    }
                });
            }

        } else {
        // If anything else is currently in transition, just queue the "show" to try again
            this.queuedShow = setTimeout(function() {that.showSection($el)}, that.queueTimeout);
        }
    },

    /**
     * Hide whatever section is currently showing
     */
    hideCurrentShown: function()
    {
        if (this.$shownSection == null) {
            return;
        }

        this.hideSection(this.$shownSection);
    },

    /**
     * Hide a specific section
     *
     * @param {object} $el
     */
    hideSection: function($el)
    {
        var that = this;

        if (typeof($el) == 'string') {
            $el = this.getSectionById($el);
            if (!$el) return;
        }

        // Cancel any hide that was already queued
        clearTimeout(this.queuedHide);

        // Proceed if we're not transitioning
        if (!this.transitioning) {
            // Flag a transition
            this.transitioning = true;

            // Finishing steps to run once hide action is completed
            $finish = function() {
                that.transitioning = false;
                that.$shownSection = null;
                $el.find('.quick-access-alert').removeClass('show');
                that.resetReferer($el);
            };

            // Hide the section
            if (this.lightbox) {
                // Hide the lightbox contents so they don't appear elsewhere
                $($el.attr('id')).hide();

                // Clean up
                $finish();
            } else {
                // Do the slide up effect
                new Effect.SlideUp($el.attr('id'), {
                    duration: this.slideDuration/1000,
                    afterFinish: function() {
                        $finish();
                    }
                });
            }
        } else {
        // If anything else was in transition, queue the "hide" action to try again
            this.queuedHide = setTimeout(function() {that.hideSection($el)}, that.queueTimeout);
        }
    },

    /**
     * Refresh the CAPTCHA in the element (necessary due to full page cache)
     *
     * @param {object} $el
     */
    refreshCaptcha: function($el)
    {
        if ($el.data('captchaInitialized')) {
            return;
        }

        var $captchaBox = $el.data('captchaBox');
        if ($captchaBox) {
            var $captchaImg = $captchaBox.data('captchaImg');
            if ($captchaImg) {
                $captchaImg.prop('captcha').refresh(null, function() {
                    if (!$captchaBox.hasClass('show')) {
                        $captchaBox.addClass('show');
                    }
                });
                $el.data('captchaInitialized', true);
            }
        }
    }
};

})(jQuery);