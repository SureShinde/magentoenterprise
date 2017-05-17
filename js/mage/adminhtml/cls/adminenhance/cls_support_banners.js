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
 * @category   CLS
 * @package    AdminEnhance
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

var AdminSupportBanners = new Class.create();
AdminSupportBanners.prototype = {
    fetchUrl: null,
    toggleUrl: null,
    loadedLocations: {},

    videoWindowWidth: 560,
    videoWindowHeight: 315,
    bannerTransitionDelay: 0.2,

    /**
     * @param {object} params
     */
    initialize: function(params)
    {
        if (typeof(params.fetchUrl) == 'undefined') {
            return;
        }

        this.fetchUrl = params.fetchUrl;

        if (typeof(params.toggleUrl) != 'undefined') {
            this.toggleUrl = params.toggleUrl;
        }
        if (typeof(params.videoWidth) != 'undefined') {
            this.videoWindowWidth = params.videoWidth + 20;
        }
        if (typeof(params.videoHeight) != 'undefined') {
            this.videoWindowHeight = params.videoHeight;
        }

        // Immediately fetch banners for the current URL if necessary
        if (params.initCurrentUrl) {
            this.fetchBannersForUrl(window.location.toString());
        }

        // Set up universal Ajax listener
        var fetchUrlRegex = new RegExp('^'+this.fetchUrl);
        var toggleUrlRegex = new RegExp('^'+this.toggleUrl);
        if(Ajax.Responders){
            Ajax.Responders.register({
                onComplete: function(response){
                    var url = response.url;
                    // For whatever URL was requested (except the banner fetch URL itself), fetch
                    if (!url.match(fetchUrlRegex) && !url.match(toggleUrlRegex)) {
                          this.fetchBannersForUrl(url);
                    }
                }.bind(this)
            });
        }

        // Make sure video links open in a new window/tab
        $$('.support-videos a').each(function(el) {
            el.observe('click', function(event) {
                window.open(el.href, 'video', 'width='+this.videoWindowWidth+',height='+this.videoWindowHeight+',location=no,menubar=no,scrollbars=no,resizable=no,status=no,titlebar=no');
                event.stop();
            }.bind(this));
        }.bind(this));
    },

    /**
     * Perform the Ajax request for any banners matching a URL
     *
     * @param {string} url
     */
    fetchBannersForUrl: function(url)
    {
        new Ajax.Request(this.fetchUrl, {
            parameters: {
                match_url: url
            },
            loaderArea: false,
            onSuccess: function(transport)
            {
                var response = transport.responseText.evalJSON();

                // Do nothing on error
                if (response.error || typeof(response.banners) == 'undefined') {
                    return;
                }

                this.injectBanners(response.banners);
            }.bind(this)
        });
    },

    /**
     * Add banner content to the page after fetched
     *
     * @param {array} banners
     */
    injectBanners: function(banners)
    {
        banners.each(function(banner){
            // For each banner, check and make sure we haven't already added to this location before
            if (this.loadedLocations[banner.locationId]) {
                return;
            }

            // For the first element matching the selector, inject the content
            var matchingEls = $$(banner.selector);
            if (matchingEls.length > 0) {
                var insertOpts = {};
                insertOpts[banner.selectorContext] = banner.content;
                matchingEls[0].insert(insertOpts);
            }

            // Find the content we just inserted and init
            this.initBanner($(banner.locationHtmlId));

            // Record that we've loaded banner into this location
            this.loadedLocations[banner.locationId] = true;
        }, this);
    },

    /**
     * Initialize a single banner's functionality
     *
     * @param {Element} bannerEl
     */
    initBanner: function(bannerEl)
    {
        if (!bannerEl) {
            return;
        }

        // Initialize handler for the collapse/expand toggle
        var collapseTrigger = bannerEl.select('.collapse-trigger');
        if (collapseTrigger.length > 0) {
            var bannerWrapper = bannerEl.up();
            var hideContent = bannerEl.select('.support-banner-content, .support-banner-header h4, .support-banner-header .help-links');

            collapseTrigger[0].observe('click', function(event) {
                this.toggleBannerContent(bannerWrapper, bannerEl, hideContent);
                event.stop();
            }.bind(this));
        }

        // Do stuff involved with help links
        var helpLinksCont = bannerEl.select('.help-links');
        if (helpLinksCont.length > 0) {
            helpLinksCont = helpLinksCont[0];

            // Set up handler to collapse/expand the help links
            var trigger = helpLinksCont.select('.help-links-trigger');
            if (trigger.length > 0) {
                trigger[0].observe('click', function() {
                    helpLinksCont.toggleClassName('expanded');
                }.bind(this));
            }

            // Make sure help links open in a new window/tab
            helpLinksCont.select('ul a').each(function(el) {
                el.observe('click', function(event) {
                    window.open(el.href);
                    event.stop();
                });
            });
        }

        // Make sure video links open in a new window/tab
        bannerEl.select('.support-videos a').each(function(el) {
            el.observe('click', function(event) {
                window.open(el.href, 'video', 'width='+this.videoWindowWidth+',height='+this.videoWindowHeight+',location=no,menubar=no,scrollbars=no,resizable=no,status=no,titlebar=no');
                event.stop();
            }.bind(this));
        }.bind(this));
    },

    /**
     * Expand/collapse banner content
     *
     * @param {Element} bannerWrapper
     * @param {Element} bannerEl
     * @param {Array} hideContent
     */
    toggleBannerContent: function(bannerWrapper, bannerEl, hideContent)
    {
        // Toggle the CSS class
        bannerWrapper.toggleClassName('collapsed');
        var isCollapsed = bannerWrapper.hasClassName('collapsed');

        // Toggle pieces of content that we don't use transitions on
        var hideContentLen = hideContent.length;
        if (isCollapsed) {
            for (var x=0; x<hideContentLen; x++) {
                hideContent[x].setStyle({'display':'none'});
            }
        } else {
            // The delay in showing is to avoid rendering issues with the CSS transitions on the parents
            setTimeout(function() {
                for (var x=0; x<hideContentLen; x++) {
                    hideContent[x].setStyle({'display':'block'});
                }
            }, (this.bannerTransitionDelay*1000));
        }

        // Send Ajax request that will update back-end data for the user
        var locationId = bannerEl.readAttribute('data-location-id');
        if ((this.toggleUrl != null) && locationId) {
            new Ajax.Request(this.toggleUrl, {
                parameters: {
                    location_id: locationId,
                    collapse: (isCollapsed) ? 1 : 0
                },
                loaderArea: false
            });
        }
    }
};