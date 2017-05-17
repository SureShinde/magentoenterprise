## Additional CSS/JavaScript Management Options

This extension supports the integration of a CDN by modifying the generation of combined CSS files.

A CDN (Content Delivery Network) improves performance on a site by loading static assets like images and CSS files from a separate, optimized server. (In Magento, after configuring a CDN service, you would typically modify certain URLs in System -> Configuration -> Web -> Unsecure to point to your CDN endpoint.)

With a CDN that reaches out to your web server to cache content directly from it, you must typically purge the CDN's cache when you want CSS changes to take effect immediately. This extension circumvents this by modifying the way CSS URLs are generated, so that the need for clearing cached information stays centralized in Magento.

This functionality requires enabling CSS combined files. Navigate to System -> Configuration -> Developer -> CSS Settings in the Magento admin and set Merge CSS Files to "Yes".

When a CSS change has been made, navigate to the Cache Management section (System -> Cache Management) and Flush JavaScript/CSS Cache before flushing the full Magento cache.

The extension also enables minification of CSS and JavaScript files (available under CSS Settings and JavaScript Settings in System -> Configuration -> Developer, once Merge Files is enabled). _Please note_ that this can have unexpected results, depending on the contents of your CSS and JavaScript files.