## Google Universal Analytics Features

This extension adds several new features to Magento's integration with Google Universal Analytics.

### New Features

#### Universal Analytics Custom Dimensions

In addition to enabling basic page tracking for Universal Analytics accounts, more detailed information is sent using Custom Dimensions. See configuration instructions below.

#### Conversion Funnel

With a Universal Analytics integration, this extension enables tracking for each individual step of the Magento checkout, which will facilitate a conversion funnel in your Analytics account. See configuration instructions below.

#### AdWords Conversion Tracking

Integrates with AdWords to track conversions related to ad campaigns.

### Configuration

#### Magento Configuration

The following are located in the Magento admin at System -> Configuration -> Google API.

##### Dimension Indexes

_These options are only supported with a Universal Analytics integration._

The main Google API settings now contain a group of fields for dimension indexes (ex., "Page type dimension index"). These should be filled with the numerical index of the corresponding dimension in your Universal Analytics account (see Google Account Configuration below).

The Product Attribute setting allows you to choose any product attribute to associate with the corresponding dimension in Universal Analytics. On any product page, the value of this attribute for the product will be sent for this dimension in the pageview hit. (As with all dimensions, requires the appropriate index be entered for "Product Attribute dimension index.")

##### Ecommerce tracking

Navigate to Admin, then to View Settings under the appropriate view. Change "Ecommerce tracking" to "On".

##### Checkout Funnel

In order to get the full benefit from the conversion funnel tracking this extension supports, follow these steps to set up a conversion goal in your Analytics property:

1. Navigate to Admin, then to Goals in the appropriate view.
2. Create a New Goal.
3. Choose a Custom goal setup.
4. Enter any description you want (ex., "Checkout Success"), and choose Destination as the goal type.
5. For the Destination, leave the dropdown set to "Equals to" and enter the following as the URL path:  /checkout/onepage/success/
6. Turn "Funnel" on, and enter steps as specified below. None of the steps should be flagged as required.

Conversion Funnel Steps (Formatted as "Name": "Screen/Page".  Names are only suggestions):

1. Checkout: /checkout/onepage/
2. Billing Address: /checkout/onepage/billing/
3. Shipping Address: /checkout/onepage/shipping/
4. Shipping Method: /checkout/onepage/shipping_method/
5. Payment Method: /checkout/onepage/payment/
6. Order Review: /checkout/onepage/review/

##### Custom Dimensions

This extension supports sending specific custom data with pageview hits that corresponds with custom dimensions in your Universal Analytics account. You must configure these dimensions
in your Analytics account in order to utilize this feature.

1. Navigate to Admin, then to Custom Definitions -> Custom Dimensions in the appropriate property.
2. For each of the dimensions defined below, choose "New Custom Dimension," add the appropriate details for name and scope, check "Active," and click "Create" and then "Done".
3. After creating all dimensions, you will see a summary table that shows a numerical index for each. Enter the indexes into the corresponding fields in the Magento System Configuration panel (see Magento Configuration above).

Custom dimensions (Formatted as "Name": "Scope"):

1. Order Count: Hit
2. Customer Group: Visitor
3. Page Type: Hit
4. {Custom Name}: Hit _(This dimension corresponds with the Product Attribute dimension you can configure in Magento. Replace "{Custom Name}" with an appropriate name corresponding with the product attribute you've chosen.)_