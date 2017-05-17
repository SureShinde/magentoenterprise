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
 * Var to store minicart in global namespace
 */
var ActiveMinicart;

/**
 * Class for registering listeners to fire when Minicart is instantiated
 */
var MinicartInit = {
    listeners: [],

    /**
     *
     * Define a listener function
     *
     * @param {function} listener
     */
    registerListener: function(listener) {
        this.listeners.push(listener);
    },

    /**
     * When Minicart is instantiated, fire all listeners
     *
     * @param {object} cart
     */
    activateListeners: function(cart) {
        var listenersLen = this.listeners.length;
        for (var x=0; x<listenersLen; x++) {
            this.listeners[x](cart);
        }
    }
};

/**
 * Override of Minicart
 */
Minicart.prototype.origInit = Minicart.prototype.init;
Minicart.prototype.init = function() {
    this.origInit();

    var cart = this;

    // Cancel a timed hide on hover or click
    this.hideTimeout = null;
    this.queueHide = false;
    this.hoveringCart = false;
    $j(this.selectors.container).on('mouseover', function() {
        if (cart.hideTimeout) {
            // If there was a timed hide occurring, queue it for when hover ends
            cart.queueHide = true;
        }
        // Stop any timed hide
        clearTimeout(cart.hideTimeout);
        cart.hideTimeout = null;
        cart.hoveringCart = true;
    });
    $j(this.selectors.container).on('mouseout', function() {
        cart.hoveringCart = false;
        if (cart.queueHide) {
            // If a timed hide was queued, re-trigger it now
            cart.timedHideCart();
        }
    });
    $j(this.selectors.container).on('click', function() {
        // On a click, cancel any timed hide permanently (user must explicitly close after this)
        clearTimeout(cart.hideTimeout);
        cart.hideTimeout = null;
        cart.queueHide = false;
    });

    // Set up "sticky" position on scroll
    var $skipCart = $j('.header-minicart .skip-cart');
    var skipCartCoords = $skipCart.offset();
    this.scrollLimit = skipCartCoords.top + $skipCart.outerHeight();
    $j(window).scroll(function() {
        if ($j(window).scrollTop() > cart.scrollLimit) {
            cart.toggleSticky(true);
        } else {
            cart.toggleSticky(false);
        }
    });

    // Activate any registered init listeners
    MinicartInit.activateListeners(this);
    // Store the Minicart where it can be accessed externally
    window.ActiveMinicart = this;
};

Minicart.prototype.origUpdateCartQty = Minicart.prototype.updateCartQty;
Minicart.prototype.updateCartQty = function(qty) {
    this.origUpdateCartQty(qty);

    // Show/hide the quantity box appropriately
    if (qty > 0) {
        $j('.header-minicart .no-count').removeClass('no-count');
    } else {
        $j('.header-minicart .skip-cart').addClass('no-count');
    }
};

/**
 * Init with needed info from the page for the Ajax Add
 *
 * @param {string} ajaxAddUrl
 */
Minicart.prototype.ajaxAddInit = function(ajaxAddUrl) {
    this.ajaxAddUrl = ajaxAddUrl;
};

/**
 * Do the Ajax Add to Cart
 *
 * @param {string} submitData
 * @param {string} id
 * @param {function} doneHandler
 * @returns {boolean}
 */
Minicart.prototype.addToCart = function(submitData, id, doneHandler) {
    if (typeof(this.ajaxAddUrl) == 'undefined') {
        // Never initialized
        return false;
    }

    // Acivate only if screen size is sufficient
    var requiredScreenSize = true;
    enquire.register('(max-width: ' + bp.medium + 'px)', {
        match: function () { requiredScreenSize = false; }
    });
    if (!requiredScreenSize) {
        return false;
    }

    var cart = this;

    this.showCart();
    this.showOverlay();

    $j.ajax({
        type: 'POST',
        dataType:'json',
        url: this.ajaxAddUrl,
        data: submitData
    }).done(function(result) {

        cart.hideOverlay();

        if (result.success) {
            cart.updateContentOnUpdate(result);
            cart.updateCartQty(result.qty);

            // Set a special display class on the item in the minicart that was just added
            $j(cart.selectors.container).find('li[data-product-id="' + id + '"]').addClass('new-item');
        } else {
            cart.showMessage(result);
        }

        cart.timedHideCart();

        if (typeof(doneHandler) != 'undefined') {
            doneHandler();
        }
    }).error(function(result) {
        cart.hideOverlay();
        cart.showError(cart.defaultErrorMessage);
    });

    return true;
};

/**
 * Toggle whether the minicart is "sticky" (fixed position)
 *
 * @param {bool} on
 */
Minicart.prototype.toggleSticky = function(on) {
    if (on) {
        $j(this.selectors.container).addClass('sticky-cart');
    } else {
        $j(this.selectors.container).removeClass('sticky-cart');
    }
};

/**
 * Set up a timeout to hide the cart
 */
Minicart.prototype.timedHideCart = function() {
    var cart = this;
    // Either set timeout now or queue it, depending on whether user is hovering on minicart
    if (!this.hoveringCart) {
        this.hideTimeout = setTimeout(function () {
            cart.hideCart();
        }, 4000);
    } else {
        this.queueHide = true;
    }
};

/**
 * Show the minicart
 */
Minicart.prototype.showCart = function() {
    $j(this.selectors.container).addClass('skip-active');
};

/**
 * Hide the minicart
 */
Minicart.prototype.hideCart = function() {
    $j(this.selectors.container).removeClass('skip-active');

    // Remove special display of any items
    $j(this.selectors.container).find('li').removeClass('new-item');

    // Make sure all queued/timed hides are cleared
    clearTimeout(this.hideTimeout);
    this.hideTimeout = null;
    this.queueHide = false;
};