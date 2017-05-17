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

/**
 * Overrides of MenuManager class to accommodate different behavior for mega menus
 * (relevant only when "mega-menu-subhead" classes have been added when mega menus are enabled)
 */
MenuManager.wirePointerEvents = function() {
    var that = this;
    var pointerTarget = $j('#nav a.has-children');
    var hoverTarget = $j('#nav li');

    if(PointerManager.getPointerEventsSupported()) {
        // pointer events supported, so observe those type of events

        var enterEvent = window.navigator.pointerEnabled ? 'pointerenter' : 'mouseenter';
        var leaveEvent = window.navigator.pointerEnabled ? 'pointerleave' : 'mouseleave';
        var fullPointerSupport = window.navigator.pointerEnabled;

        hoverTarget.on(enterEvent, function(e) {
            if(e.originalEvent.pointerType === undefined // Browsers with partial PointerEvent support don't provide pointer type
                || e.originalEvent.pointerType == PointerManager.getPointerEventsInputTypes().MOUSE) {

                if(fullPointerSupport) {
                    that.mouseEnterAction(e, this);
                } else {
                    that.PartialPointerEventsSupport.mouseEnterAction(e, this);
                }
            }
        }).on(leaveEvent, function(e) {
            if(e.originalEvent.pointerType === undefined // Browsers with partial PointerEvent support don't provide pointer type
                || e.originalEvent.pointerType == PointerManager.getPointerEventsInputTypes().MOUSE) {

                if(fullPointerSupport) {
                    that.mouseLeaveAction(e, this);
                } else {
                    that.PartialPointerEventsSupport.mouseLeaveAction(e, this);
                }
            }
        });

        if(!fullPointerSupport) {
            //click event doesn't have pointer type on it.
            //observe MSPointerDown to set pointer type for click to find later

            pointerTarget.on('MSPointerDown', function(e) {
                $j(this).data('pointer-type', e.originalEvent.pointerType);
            });
        }

        pointerTarget.on('click', function(e) {
            var pointerType = fullPointerSupport ? e.originalEvent.pointerType : $j(this).data('pointer-type');

            if(pointerType === undefined || pointerType == PointerManager.getPointerEventsInputTypes().MOUSE) {
                that.mouseClickAction(e, this);
            } else {
                if(fullPointerSupport) {
                    that.touchAction(e, this);
                } else {
                    that.PartialPointerEventsSupport.touchAction(e, this);
                }
            }

            $j(this).removeData('pointer-type'); // clear pointer type hint from target, if any
        });
    } else {
        //pointer events not supported, use Apple-style events to simulate

        hoverTarget.on('mouseenter', function(e) {
            // Touch events should cancel this event if a touch pointer is used.
            // Record that this method has fired so that erroneous following
            // touch events (if any) can respond accordingly.
            that.mouseEnterEventObserved = true;
            that.cancelNextTouch = true;

            that.mouseEnterAction(e, this);
        }).on('mouseleave', function(e) {
            that.mouseLeaveAction(e, this);
        });

        $j(window).on('touchstart', function(e) {
            if(that.mouseEnterEventObserved) {
                // If mouse enter observed before touch, then device touch
                // event order is incorrect.
                that.touchEventOrderIncorrect = true;
                that.mouseEnterEventObserved = false; // Reset test
            }

            // Reset TouchScroll in order to detect scroll later.
            that.TouchScroll.reset();
        });

        pointerTarget.on('touchend', function(e) {
            $j(this).data('was-touch', true); // Note that element was invoked by touch pointer

            // EDIT: Don't prevent default if this is currently a mega menu sub-heading //
            if (!$j(this).hasClass('mega-menu-subhead')) {
                e.preventDefault(); // Prevent mouse compatibility events from firing where possible
            }

            if(that.TouchScroll.shouldCancelTouch()) {
                return; // Touch was a scroll -- don't do anything else
            }

            if(that.touchEventOrderIncorrect) {
                that.PartialTouchEventsSupport.touchAction(e, this);
            } else {
                that.touchAction(e, this);
            }
        }).on('click', function(e) {
            var link = $j(this);
            // EDIT: Don't prevent default if this is currently a mega menu sub-heading
            if(link.data('was-touch') && !link.hasClass('mega-menu-subhead')) { // Event invoked after touch
                e.preventDefault(); // Prevent following link
                return; // Prevent other behavior
            }

            that.mouseClickAction(e, this);
        });
    }
};

MenuManager.PartialPointerEventsSupport.mouseEnterAction = function(event, target) {
    if(MenuManager.useSmallScreenBehavior()) {
        // fall back to normal method behavior
        MenuManager.mouseEnterAction(event, target);
        return;
    }

    // EDIT: Don't prevent default if this is currently a mega menu sub-heading
    if (!$j(target).hasClass('mega-menu-subhead')) {
        event.stopPropagation();
    }

    var jtarget = $j(target);
    if(!jtarget.hasClass('level0')) {
        this.mouseleaveLock = jtarget.parents('li').length + 1;
    }

    MenuManager.toggleMenuVisibility(target);
};

MenuManager.PartialPointerEventsSupport.touchAction = function(event, target) {
    if(MenuManager.useSmallScreenBehavior()) {
        // fall back to normal method behavior
        MenuManager.touchAction(event, target);
        return;
    }
    // EDIT: Don't prevent default if this is currently a mega menu sub-heading
    if (!$j(target).hasClass('mega-menu-subhead')) {
        event.preventDefault(); // prevent following link
    }
    this.mouseleaveLock++;
};

MenuManager.origMouseClickAction = MenuManager.mouseClickAction;
MenuManager.mouseClickAction = function(event, target) {
    if (!$j(target).hasClass('mega-menu-subhead')) {
        this.origMouseClickAction(event, target);
    }
};

MenuManager.origTouchAction = MenuManager.touchAction;
MenuManager.touchAction = function(event, target) {
    if (!$j(target).hasClass('mega-menu-subhead')) {
        this.origTouchAction(event, target);
    }
};

$j(document).ready(function ($) {

    // ==============================================
    // Scroll to element
    // ==============================================

    /**
    * @param selector       (string) - A CSS-like selector of the element to scroll to.
    * @param time           (int)    - Time in milliseconds.
    * @param verticalOffset (int)    - Pixel offset from actual position (positive or negative).
    *
    * @see http://www.dconnell.co.uk/blog/index.php/2012/03/12/scroll-to-any-element-using-jquery/
    */

    scrollToElement = function (selector, time, verticalOffset) {
        var time = typeof(time) != 'undefined' ? time : 1000;
        var verticalOffset = typeof(verticalOffset) != 'undefined' ? verticalOffset : 0;
        var element = jQuery(selector);
        var offset = element.offset();
        var offsetTop = offset.top + verticalOffset;

        jQuery('html, body').animate({
            scrollTop: offsetTop
        }, time);
    }



    // ==============================================
    // UI Pattern - GravDept Accordion
    // ==============================================

    // Used to trigger the display of extra content.
    // Default is hidden.

    // <div class="accordion (accordion-active)">
    //     <section class="accordion-section (toggle-active)">
    //         <h2 class="accordion-header">Trigger</h2>
    //
    //         <article class="accordion-content">Toggleable content</article>
    //     </section>
    // </div>

    /**
    * Init accordion nav for large screens
    *
    * @param {object} $accordion - The accordion to update. Select an element ID and pass as jQuery object.
    */
    function initAccordionNav ($accordion) {
        var $accordionSections = $accordion.find('.accordion-section');
        var htmlNavList = [];

        // Remove old nav
        var oldNav = $accordion.find('.accordion-nav');

        if (oldNav.length) {
            oldNav.stickyfloat('destroy');
            oldNav.remove();
        }

        // Build new nav
        $accordionSections.each(function () {
            var $self = $(this);

            htmlNavList.push('<li><a href="#' + $self.attr('id') + '" class="accordion-nav-link">' + $self.find('.accordion-header').html() + '</a></li>');
        });

        var htmlSnippet = [
            '<nav class="accordion-nav">',
                '<h3>Skip To Section</h3>',
                '<ol class="accordion-nav-list">',
                    htmlNavList.join(''),
                '</ol>',
            '</nav>'
        ];

        $accordion.prepend(htmlSnippet.join(''));

        if ($().stickyfloat) {
            $accordion.find('.accordion-nav').stickyfloat({
                duration: 200
            });
        }
    }

    var accordions = $('.accordion');

    if (accordions.length) {
        var $d = $(document);

        accordions.each(function () {
            var $this = $(this);

            // Setup accordions
            $this.addClass('accordion-active');

            // Create nav
            initAccordionNav($this);

            // Custom event: listen for accordion nav updates
            $this.on('updateAccordionNav', function (e) {
                initAccordionNav($this);
            });
        });

        // Event: toggle accordions open/shut
        $d.on('click', '.accordion-header', function (e) {
            e.preventDefault();

            if (Modernizr.mq('only screen and (max-width:770px)')) {
                $(this)
                    .parent()
                    .toggleClass('toggle-active');
            }
        });

        // Event: skip to accordion section
        $d.on('click', '.accordion-nav-link', function (e) {
            e.preventDefault();

            scrollToElement($(this).attr('href'), 500);
        });
    }



    // ==============================================
    // UI Pattern - Expandable Module
    // ==============================================

    // Used to trigger the display of extra content in a module.
    // Content is always visible until browser reloads.

    // <div class="block expand {expand-active}">
    //     <div class="block-title">
    //         <h2>Title</h2>
    //         <a class="expand-button" href="#">Expand</a>
    //     </div>
    //
    //     <div class="block-content expand-target">
    //         Lorem ipsum.
    //     </div>
    // </div>

    var expandButtons = $('.expand-button');

    expandButtons.on('click', function (e) {
        e.preventDefault();

        $(this)
            .closest('.expand')
            .addClass('expand-active');
    });



    // ==============================================
    // UI Pattern - Modal
    // ==============================================

    if ($().colorbox) {
        var modalButtons = $('[data-modal]');

        modalButtons.on('click', function (e) {
            e.preventDefault();

            var $self = $(this);
            var target = $self.attr('data-modal');
            var content = $('#' + target).html();

            $.colorbox({
                close: '&#x2297;',
                fadeOut: 0,
                html: content,
                maxWidth: '95%',
                transition: 'none'
            });
        });
    }

    // Set the modal's max-width to a sensible size.
    var modalMaxWidth = '600px';

    $(window).on('delayed-resize', function () {
        // Only run if Colorbox is open
        if ($('#colorbox').css('display') == 'block') {
            $.colorbox.resize({
                width: window.innerWidth > parseInt(modalMaxWidth) ? modalMaxWidth : '95%'
            });
        }
    });



    // ==============================================
    // UI Pattern - Tabs
    // ==============================================

    var tabs = $('.tabs');

    if (tabs.length) {
        tabs.each(function () {
            var tabsContent = $('.tabs-content').children(),
                tabsLinks = $('.tabs-nav').find('a');

            // Detect click on nav item
            tabsLinks.on('click', function (e) {
                e.preventDefault();

                // Hide all content, then show content for hash
                tabsContent
                    .hide()
                    .filter(this.hash)
                    .show();

                // Remove 'current' class from all links
                tabsLinks.removeClass('current');

                // Add 'current' class to clicked link
                $(this).addClass('current');
            }).filter(':first').click();

            // Detect if the page was linked with a tab hash and show it instead.
            if (window.location.hash) {
                var hashTarget = $('.tabs-nav').find('a[href="' + window.location.hash + '"]');

                if (hashTarget.length === 1) {
                    hashTarget.click();
                }
            }
        });
    }



    // ==============================================
    // UI Pattern - Toggle
    // ==============================================

    // Used to trigger the display of extra content.
    // Default is hidden.

    // <div data-toggle="hide|show">
    //     <div data-toggle-trigger>Trigger</div>
    //     <div data-toggle-target>Toggleable content</div>
    // </div>

    // Examples:
    //  - Catalog: list filter
    //  - Customer: account nav
    //  - FAQ

    var toggleTriggers = $('[data-toggle-trigger]');

    toggleTriggers.on('click', function (e) {
        e.preventDefault();

        var $elem = $(this).closest('[data-toggle]');

        $elem.attr('data-toggle', ($elem.attr('data-toggle') === 'show') ? 'hide' : 'show');
    });

});
