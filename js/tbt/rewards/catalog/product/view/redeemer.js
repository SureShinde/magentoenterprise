

/**
 * WDCA - Sweet Tooth
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS
 * License, which extends the Open Software License (OSL 3.0).
 * The Sweet Tooth License is available at this URL:
 *     https://www.sweettoothrewards.com/terms-of-service
 * The Open Software License is available at this URL:
 *      http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * By adding to, editing, or in any way modifying this code, WDCA is
 * not held liable for any inconsistencies or abnormalities in the
 * behaviour of this code.
 * By adding to, editing, or in any way modifying this code, the Licensee
 * terminates any agreement of support offered by WDCA, outlined in the
 * provided Sweet Tooth License.
 * Upon discovery of modified code in the process of support, the Licensee
 * is still held accountable for any and all billable time WDCA spent
 * during the support process.
 * WDCA does not guarantee compatibility with any other framework extension.
 * WDCA is not responsbile for any inconsistencies or abnormalities in the
 * behaviour of this code if caused by other framework extension.
 * If you did not receive a copy of the license, please send an email to
 * support@sweettoothrewards.com or call 1.855.699.9322, so we can send you a copy
 * immediately.
 *
 * @category   [TBT]
 * @package    [TBT_Rewards]
 * @copyright  Copyright (c) 2014 Sweet Tooth Inc. (http://www.sweettoothrewards.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
/**
 * Redemption drop down methods
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */

var rSlider;
var usesSelect;
var usesCaption;
var usesContainer;


/**
 *
 */
function getProductPriceBeforeRedemptions() {
    var priceBeforeRedemptions = optionsPrice.productPriceBeforeRedemptions;

    // if dynamic price
    if(optionsPrice.optionPrices.config != undefined) {
        if(optionsPrice.optionPrices.config.price != undefined) {
            // magento 1.4.2
            priceBeforeRedemptions += optionsPrice.optionPrices.config.price;
        } else {
            // magento < 1.4.2
            priceBeforeRedemptions += optionsPrice.optionPrices.config;
        }
    }

    // if simple product with custom options, MCE 1.7+ && MEE 1.12+
    if (optionsPrice.customPrices != undefined) {
        obj = optionsPrice.customPrices;
        for (var prop in obj) {
            if (obj.hasOwnProperty(prop) && obj[prop].hasOwnProperty('price')) {
                priceBeforeRedemptions += obj[prop].price;
            }
        }
    }

    // if simple product with custom options, MCE < 1.7 && MEE < 1.12
    if (optionsPrice.optionPrices.options != undefined) {
        priceBeforeRedemptions += optionsPrice.optionPrices.options;
    }

    // dynamic bundles
    if(optionsPrice.optionPrices.bundle != undefined) {
        if(optionsPrice.optionPrices.bundle.price != undefined) {
            priceBeforeRedemptions += optionsPrice.optionPrices.bundle.price;
        } else {
            priceBeforeRedemptions += optionsPrice.optionPrices.bundle;
        }
    }

    return priceBeforeRedemptions;
}

/**
 *
 * TODO: deprecate - this is not used
 *
 * @param rule_id
 * @return
 */
function feignPriceChange(rule_id) {

    if (typeof new_price_dom_id === 'undefined') {
        return;
    }

    var newPrice = $(new_price_dom_id);
    var oldPrice = $(old_price_dom_id);
    var numUses = getRedemptionUses();
    numUses = (numUses == "") ? 1 : parseInt(numUses);
    if(rule_id == null) {
        rule_id = $('redemption_rule').value;
    }

    if(!show_lowest_price) {
        return;
    }

    if(oldPrice == null) {
        oldPrice = newPrice.cloneNode(true);
        newPrice.up().insertBefore(oldPrice, newPrice);
        oldPrice.id = old_price_dom_id;
        oldPrice.removeClassName('price');
        oldPrice.addClassName('old-price');
        do_hide_old_price = true;
    }

    var finalPrice = getProductPriceBeforeRedemptions();

    if(rule_id == "") {
        if(do_hide_old_price) oldPrice.hide();
        optionsPrice.productPrice = finalPrice;
        optionsPrice.reload();
    } else {
        if(do_hide_old_price) oldPrice.show();
        var price_disposition = rule_options[rule_id]['price_disposition'];
        //Edited 2/24/2010 7:41:46 AM : prices that set "to_fixed" discount should also
        if(rule_options[rule_id]['discount_action'] == 'by_fixed' && rule_options[rule_id]['new_price_flt'] <= 0.0000) {
            optionsPrice.productPrice = 0;
            optionsPrice.minusDisposition = 9999999999; // hack to make sure the price stays at 0;
        } else {
            optionsPrice.productPrice = finalPrice - price_disposition * numUses;
        }
        //Edited
        //points_caption = rule_options[rule_id]['points_caption'];
        points_amount = rule_options[rule_id]['amount'];
        points_currency_id = rule_options[rule_id]['currency_id'];
        points_caption = getPointsString(points_amount*numUses, points_currency_id);

        optionsPrice.reload();

        var points_with = " " + CAPTION_WITH + " " + points_caption ;
        if(newPrice.down() != null) {
            newPrice.down().innerHTML =  newPrice.down().innerHTML  + points_with ;
        } else if(newPrice.down() == null && newPrice != null) {
            newPrice.innerHTML =  newPrice.innerHTML  + points_with;
        }
        else { /*don't know where it is so just don't show the points thing.*/    }
    }
//newPrice.toggle();
}

/**
 *
 * @param rule_id
 * @param retain_value
 * @return
 */
function updateRemptionUsesSelector(rule_id, retain_value) {
    var init_value = retain_value ? retain_value : usesSelect.value ;

    if(rule_id == '') {
        usesContainer.hide();
    } else {
        usesSelect.innerHTML = '';
        var uses = 1;
        var amt = rule_options[rule_id]['amount'];
        
        var finalPrice = getProductPriceBeforeRedemptions();
        
        if (rule_options[rule_id]['points_action'] == "deduct_by_amount_spent") {
            var monetaryStep = rule_options[rule_id]['monetary_step'];

            var multiplier = Math.ceil(finalPrice / monetaryStep);
            amt = rule_options[rule_id]['points_amt'] * multiplier;
        }
        
        var priceDispositionAux = sweettooth.ProductView.CatalogRules.helper.priceAdjuster(finalPrice, rule_options[rule_id]['effect']);
        var newPriceDispositionAux = finalPrice - priceDispositionAux;
        rule_options[rule_id]['price_disposition'] = newPriceDispositionAux.toFixed(2);
        
        var curr = rule_options[rule_id]['currency_id'];
        var max_uses = rule_options[rule_id]['max_uses'];
        var relevant_customer_points = customer_points ? customer_points[curr] : default_guest_points;
        var price_disposition = rule_options[rule_id]['price_disposition'];
        var nextPrice = getProductPriceBeforeRedemptions() - price_disposition;

        usesSelect.hide();
        usesCaption.hide();
        if(max_uses == 1 || max_uses == null) {
            usesCaption.innerHTML = CAPTION_YOU_WILL_SPEND + " " + getPointsString(uses*amt, curr);
            usesCaption.show();
        } else {
            while(relevant_customer_points >= amt*uses) {
                var oOption = document.createElement("option");
                oOption.text = getPointsString(uses*amt, curr);
                if(show_discount_in_uses_selector) {
                    oOption.text = oOption.text + " ( - "+ optionsPrice.formatPrice(price_disposition*uses) +")";
                }
                oOption.value = uses;
                usesSelect.appendChild(oOption);
                uses++;
                if (nextPrice <= 0 || (uses > max_uses && max_uses != 0)) break;
                nextPrice = getProductPriceBeforeRedemptions() - price_disposition*uses;
            }
            if(retain_value) {
                if(init_value > uses) {
                    init_value = max_uses;
                }
                usesSelect.setValue(init_value);
            } else {
                usesSelect.setValue(1);
            }
            usesSelect.show();
        }
        usesContainer.show();
    }
}

var sweettooth =  typeof sweettooth !== 'undefined' ? sweettooth : window.sweettooth || {};
sweettooth.ProductView = sweettooth.ProductView || {};

/**
 * Handle Catalog Rules on Product View
 */
sweettooth.ProductView.CatalogRules = {
    /**
     * Update rules definitions based on product configured options
     * @param {string} url
     * @returns {undefined}
     */
    updateRulesOptionsOnProductOptionChange: function(url) {
        var self = this;
        
        new Ajax.Request(url, {
            parameters: self.prepareProductBuyRequest(),
            asynchronous: false,
            onSuccess: function(transport) {
                var response = transport.responseJSON;

                if (response && response.hasOwnProperty('rule_map')) {
                    rule_options = response.rule_map;
                }
            }.bind(this)
        });
    },
    /**
     * Updates points-only product points cost
     * @param {string} url
     * @returns {undefined}
     */
    updatePointsTotalProductPointsOnly: function (url) {
        var self = this;
        $('points-only-price').addClassName('points-price-wait-msg');
        
        new Ajax.Updater(
            "points-only-price", url,
            {
                method:'post',
                parameters: self.prepareProductBuyRequest(),
                asynchronous: true,
                onComplete: function() {
                    $('points-only-price').removeClassName('points-price-wait-msg');
                }
            }            
        );
    },
    /**
     * Prepare product buy request from form elements
     * @returns {Hash.toQueryString|Object.toQueryString}
     */
    prepareProductBuyRequest: function() {
        var fields = $$('.product-view').first().select('input', 'select', 'textarea');

        return Form.serializeElements(fields, true);
    },
    helper: {
        priceAdjuster: function(price, code) {
            if (code.indexOf("-") > -1) {
                if (code.indexOf("%") > -1) {
                    var fx    = 1 + code.replace("%", "") / 100;
                    price = price * fx;
                } else {
                    price = price + parseFloat(code);
                }
            } else {
                if (code.indexOf("%") > -1) {
                    var fx    = code.replace("%", "") / 100;
                    price = price * fx;
                } else {
                    price = parseFloat(code);
                }
            }

            return price;
        }
    }
};