## Customer Functionality Enhancements

Quick Login/Register:  Login and registration are done directly through pop-out/drop-down forms in the header, rather than needing to navigate to another page to complete the form. This can be disabled (see "Configuration"). When enabled, clicks on login-restricted links can be intercepted and the Login quick form shown immediately (instead of navigating to the chosen page only to be redirected).

### Configuration

System -> Configuration -> Customer Configuration -> Quick Login/Register Options contains the following configuration items:

* Enable "Quick Register" Interface:  Use the quick form for user registration
* Enable "Quick Login" Interface:  Use the quick form for user login
* Login-restricted URL Patterns: These regular expressions are defined one per line and should match URL paths relative to the site root. (Ex., "^customer/(account/edit|address)" matches "customer/account/edit" or "customer/address")  Matched URLs will trigger the Quick Login form when a link is clicked by an non-logged user.