## Nucleus Theme

The theme component of the Nucleus product offering makes various enhancements to the standard Magento theme.

### Description of Theme Enhancements

Nucleus includes its own responsive theme that refreshes the look and feel of the core Magento theme, with general enhancements to global styles, the header, and category pages. More specific enhancements are described below.

#### Sub-category Listings

Categories can now be configured to display a list of their sub-categories instead of, or in addition to, product listings and CMS content.

When editing a category, the Display Mode option under Display Settings has new values for sub-categories and combinations of this content and others. The new "Category Listing Thumbnail" attribute defines the image shown in the sub-categories listing.

When a viewing a product that has reviews, the review summary links at the top of the page will open the Reviews tab and scroll to it, rather than navigating away from the page.

#### Main Navigation "mega menus"

If your store catalog has numerous third-level categories you want users to have easy access to, the native drop-downs in the main navigation might not be the optimal experience.

With "mega menus" enabled, drop-downs from the main nav will have a wider display, listing all second-level categories as sub-headings with their own (third-level) children listed immediately below them.

To enable mega menus, navigate to System -> Configuration -> Catalog -> Mega Menu in the Magento admin and set "Enable Mega Menu" to "Yes".

#### Order Success Page

Several enhancements give customers a richer experience and a host of valuable information after they place an order on your site.  These enhancements include:

* A summary of the order
* Easily configurable "lead time" text to let the customer know when to expect their shipping notification
* A shipping and returns policy, which opens in a lightbox
* Cross-sells associated with the items purchased
* A space for any promotion you want your customers to see

The following new related values can be configured in the Magento admin, found in System -> Configuration -> Design -> Checkout/Order

* Lead Time Notice on Order Success Page: Enter text that informs the customer when to expect shipment.
* Shipping and Returns Policy: Select a CMS static block containing your shipping and returns policy. A link on the order success page will open this content in a lightbox.

To utilize the new promotional space, create a CMS widget, select "One Page Checkout Success" as the page to display on (requires choosing "Specified Page" in the "Display On" drop-down), and look for the "Order Success Promo Area" block reference.

#### Full Viewport Content

For that piece of content you need to be particularly striking, the Nucleus theme implements a new widget location near the top of the page that allows for "full viewport width" content. Content placed here can expand beyond the site's maximum page width and fill the full space available in the user's browser.

To use this widget location, look for the block reference titled "Top Full Width Banner" when creating a CMS widget.

#### Enhanced Error Pages

The visual experience of error pages is often neglected, but the Nucleus theme has enhanced both 404 and server error pages with a fresh and friendly look.

#### Global

Product tags and site poll are removed from left and right columns site-wide.

#### Pager

The default pager element (most prominently used on product listing pages) has been modified to remove the link and dropdown styles of the pager and limiter controls in favor of a cleaner button style.

#### Product View Page

By default, product options will be displayed in the product info column instead of lower in the page beneath both columns. NOTE: This is done by changing the default value of "Display Product Options In" (see the Design tab when editing a product), and therefore this requires NucleusCore to be in place before running Magento installation. Pre-existing products will not be affected.

### Nucleus Elements

Nucleus Elements provides a library of content patterns, supported by the Nucleus theme, that give you a quick way to format your static content in appealing ways, including custom pages. With just basic HTML knowledge, you can draw from a wide range of layout options to rapidly create "wow!" experiences for your users.

Implementing an Element is as easy as copying and pasting the HTML snippet from one of these blocks, and then replacing its content with your own. Use Nucleus Elements to create a tabbed interface, a link that opens a modal window, sharply formatted hero banners, or grid content that mixes text and images. These and many other patterns can be utilized with a simple copy and paste.

Using the content patterns found in Nucleus Elements is a great way to spice up the content in CMS blocks that populate category landing pages, as well as CMS pages.

#### Using Nucleus Elements

By default, your site installation includes CMS static blocks (all titles are prefixed with "Nucleus Elements:") that each provide example HTML for a specific configuration of an Element. Open one of these static blocks and copy its HTML into a new location to use the pattern. Once copied into your own CMS static block or page, simply replace the content within the HTML structure.

CMS preview pages (also with titles prefixed with "Nucleus Elements") are also installed. These pages give you a quick reference for what the different Elements do, as well as noting exactly which CMS static block to find and copy to accomplish a particular configuration.

By navigating to System -> Configuration -> Nucleus Elements in the Magento admin, you can find a link to the beginning preview page. This is a great place to start when you want to utilize Elements.

Note that in order to use Nucleus Elements, your site must be using the Nucleus theme, or a theme that includes Nucleus in its fallback scheme. (This includes ensuring that your SCSS structure in the skin directory imports from the nucleus theme, or else contains copies of the SCSS partials found there.)

#### Updating or Uninstalling

Nucleus features the ability to install new Elements on your site as they become available, without the need for a full upgrade of your codebase.

When you have obtained a code package with an updated version of Nucleus Elements, the following steps are required to update your installation:

* Copy the files contained in the package into the parallel directories on your Magento site. If you are using a custom theme and have directly copied the files in scss/nucleus-elements from the nucleus theme into your own, you will need to copy the new versions into your theme as well.
* The code package will include updated final CSS for the nucleus theme. If you are using a custom theme that extends it, however, you will need to re-compile CSS in your own theme.
* Visit System -> Configuration -> Nucleus Elements in the Magento admin. You will see information indicating the version of Nucleus Elements you have installed, as well as the new version available.
* Click the "Update" button to install all new CMS pages and blocks.

The System Configuration page also gives you options for re-installing all Nucleus Elements CMS pages/blocks or uninstalling them.

### Support for setting admin theme

While changing the theme of the Magento admin is possible using XML configuration, the admin interface itself doesn't feature the ability to modify it as it does for the front-end theme. Nucleus adds this ability. (See System -> Configuration -> Design.)

### JavaScript libraries

To support various CLS extensions and for general use, owl.carousel and colorbox JS libraries are added to core codebase

### For Developers

The following enhancements have no front-end implications but assist development.

* "Remove" methods have been added to the customer account navigation block.
* "Footer JS" block added so that JavaScript files can be included at the bottom of the page.
* Certain core templates have been overridden with minor changes that enable powerful results: New text lists injected into these templates make it possible to extend them with other custom content while keeping the core templates clean.
* A new structure for the output of the category page allows for creating additional Display Modes in a much more intuitive way.