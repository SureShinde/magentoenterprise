CLS_NucleusCore 1.5.3 (8/24/2015)
=======================================
[ADDED] Magento version checking
[CHANGED] Nucleus version is represented directly in CLS_NucleusCore instead of keying from a file
[CHANGED] Updated Magento to CE 1.9.2.1 and EE 1.14.2.1

CLS_NucleusCore 1.5.2 (6/19/2015)
=======================================
[CHANGED] Moved doc files to root directory

CLS_NucleusCore 1.5.1 (5/19/2015)
=======================================
[FIXED] Misc minor theme fixes
[FIXED] Nucleus Elements accordion pattern not opening at certain viewports
[FIXED] Second-level categories in mega-menus not navigating on touch devices

CLS_NucleusCore 1.5.0 (4/23/2015)
=======================================
[CHANGED] Renamed to CLS_NucleusCore
[CHANGED] Full overhaul of Nucleus theme
[CHANGED] New attribute to use instead of native thumbnail attribute for sub-category listing
[ADDED] Nucleus Elements
[ADDED] "Mega Menu" functionality
[ADDED] Enhanced content for order success page
[ADDED] Support for setting admin theme from within the admin interface
[ADDED] Theme tweaks for AheadWorks modules
[ADDED] Theme support for Nucleus_Cart and Nucleus_Catalog

CLS_NucleusCore 1.4.5 (1/8/2015)
=======================================
[CHANGED] Composer identifier

CLS_NucleusCore 1.4.4 (12/19/2014)
=======================================
[CHANGED] Added event that facilitates other modules disabling the custom review JS block
[CHANGED] Re-factored review JS inclusion so that functionality is universally available, but LCP-specific setup is done only in theme
[CHANGED] Removed product tags from product view page and customer account dashboard
[REMOVED] Removed replacement jQuery include, now that the core includes it from the js directory instead of skin

CLS_NucleusCore 1.4.3 (10/16/2014)
=======================================
[FIXED] Minor style issue with admin footer

CLS_NucleusCore 1.4.2 (7/7/2014)
=======================================
[CHANGED] Re-formatted README
[FIXED] Invalid markup in sub-category listing
[FIXED] Proofed against spaces in category image filenames
[ADDED] Override of PayPal info code

CLS_NucleusCore 1.4.1 (6/6/2014)
=======================================
[CHANGED] Improved styles of the customized LCP admin login

CLS_NucleusCore 1.4.0 (5/12/2014)
=======================================
[CHANGED] Base compatibility with CE 1.9/EE 1.14
[CHANGED] Re-structured nucleus themes to match responsive
[CHANGED] Re-structured CSS into SASS structure
[CHANGED] Implemented a remove/re-add of jQuery that puts it earlier in source than the core code
[CHANGED] Default theme fallback now defined via setup script instead of default nodes in config.xml
[ADDED] Added correct config.rb files in rwd themes that correctly compile
[ADDED] Added compile.sh and compass.sh files to facilitate compiling all themes' SASS
[ADDED] Added support for a theme fallback defined in etc/theme.xml by a call to a helper method
[ADDED] Fix for core bug where additional layout files defined in etc/theme.xml aren't retained if the theme is a fallback
[ADDED] owl-carousel JS library
[ADDED] colorbox JS library
[REMOVED] jquery.cycle and jcarousel libraries

CLS_NucleusCore 1.3.0 (4/30/2014)
=======================================
[ADDED] Community compatibility
[ADDED] Product list template override to add "additional" block
[ADDED] Added text list hook for information beneath product names in product list template
[ADDED] Turned off WYSIWYG by default
[ADDED] Added license info to docblocks
[ADDED] Added logo to admin login
[ADDED] Added header with user guide and support info in admin dashboard
[CHANGED] Shifted info in System Config from CLS_Core to this module
[CHANGED] Changed sub-category listing column count to be dynamic by layout

CLS_NucleusCore 1.2.4 (3/25/2014)
=======================================
[ADDED] Set "No" as default for Redirect Customer to Account Dashboard after Logging in

CLS_NucleusCore 1.2.3 (3/9/2014)
=======================================
[FIXED] Added translate file definition to config

CLS_NucleusCore 1.2.2 (2/21/2014)
=======================================
[ADDED] Added default theme path directly in config XML

CLS_NucleusCore 1.2.1 (2/20/2014)
=======================================
[CHANGED] Removed compatibility information

CLS_NucleusCore 1.2.0 (2/19/2014)
=======================================
[ADDED] Added composer.json file for automated building

CLS_NucleusCore 1.1.0 (2/15/2014)
=======================================
[CHANGED] Official rename

CLS_NucleusCore 1.0.2 (2/5/2014)
=======================================
[CHANGED] Moved documentation files for compatibility with packager script

CLS_NucleusCore 1.0.1 (1/22/2014)
=======================================
Add jquery.cookie so frontend modules can easily access cookies.

CLS_NucleusCore 1.0.0 (1/6/2014)
=======================================
Initial release
