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
 * @package    NucleusCore
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

var NucleusProductView;

(function(jQ) {

NucleusProductView = {

    reviewUrlParams: ['limit','p']

    /**
     * Setup for the "Write a Review" links
     */
    , setupNativeReviews: function(createReviewUrl, reviewsUrl)
    {
        var that = this;

        jQ('a.write-review-trigger').on('click', function(clickEvent) { that.showWriteReview(clickEvent); });
        jQ('a.reviews-list-trigger').on('click', function(clickEvent) { that.showCustomerReviews(clickEvent); });

        var hash = window.location.hash.replace('#', '');
        var skipToReviews = false;
        var queryString = window.location.search.replace('?', '');
        jQ.each(this.reviewUrlParams, function(index, value) {
            var beginRegex = new RegExp('^' + value + '=');
            var seqRegex = new RegExp('&' + value + '=');
            if (beginRegex.test(queryString) || seqRegex.test(queryString)) {
                skipToReviews = true;
                return false;
            }
        });

        if (hash == this.writeReviewId) {
            this.showWriteReview(null);
        } else if (skipToReviews || hash == this.reviewsListId) {
            this.showCustomerReviews(null);
        }
    }

    , activateReviewSection: function()
    {
        // Due to the complexity of the tab/accordion functionality coupled with the fact that there are no class
        // that we can target directly, we are using a clunky method of selecting the elements that show the Reviews content
        jQ('.product-collateral .toggle-tabs li > span:contains("Reviews"),'
                // Specifying :not(.accordion-open) because otherwise accordion will *toggle* visibility of the Review content
                + '.product-collateral:not(.accordion-open) .collateral-tabs dt > span:contains("Reviews")'
            )
            .parent() // Find the clickable element
            .trigger('click');
    }

    , scrollToReviewSection: function()
    {
        jQ('html,body').animate({scrollTop: jQ('#customer-reviews').offset().top},800);
    }

    , showCustomerReviews: function(clickEvent)
    {
        this.activateReviewSection();

        // Only animate the scrolling if a link on this page is clicked. Otherwise the scroll behavior is odd.
        if (clickEvent) {
            this.scrollToReviewSection();
            clickEvent.preventDefault();
        } else {
            // Jump directly to review form without animation
            jQ('#' + this.reviewsListId)[0].scrollIntoView();
        }
    }
    /**
     * Show the Write a Review section
     */
    , showWriteReview: function(clickEvent)
    {
        this.activateReviewSection();

        // Only animate the scrolling if a link on this page is clicked. Otherwise the scroll behavior is odd.
        if (clickEvent) {
            // Not using this.scrollToReviewSection(), as the review content breaks beneath the review list on small viewports
            jQ('html,body').animate({scrollTop: jQ('#customer-reviews .form-add').offset().top}, 800);
            clickEvent.preventDefault();
        }
    }

    /**
     * "In stock" message is hidden; if it's present at page load, add a new class so we can hide it all the time and make sure it doesn't flicker in and out via JS
     */
    , checkInStockMsg: function()
    {
        var $productShop = jQ('.product-shop');
        var $inStockMsg = $productShop.find('.in-stock');
        if ($inStockMsg.length > 0) {
            $productShop.addClass('in-stock-shop');
        }
    }
};

})(jQuery);