## Catalog Features

### Product Badges

Badges provide a highly visual way to communicate information about products, instantly drawing the customer's attention.

Badges are displayed as stylized text blocks overlaid on the product image on a category page, or below the product name on the product detail page.

#### Badge Types

Two special badge types exist, which are driven by native product attributes:  The "New" badge displays if the current date is between a product's "New From" and "New To" date attributes. The "Sale" badge displays if a Special Price is currently set on the product. (The "Special Price From" and "Special Price To" dates are also validated.)

You may also define product badge text for any purpose by using the provided product attribute.

#### Configuration

Custom badge text is controlled via the "Badges" multi-select product attribute.  In the Magento admin, navigate to Catalog -> Attributes -> Manage Attributes, and edit the "Badges" attribute. Use the "Manage Label/Options" tab to add any badges you'd like to use.

Make sure this attribute is in the appropriate attribute sets for the products you wish to add badges to, then edit the individual products and set the desired badges using the "Badges" multi-select input.

The following settings are available in System -> Configuration -> Catalog -> Badges:

* Enable "New" Badge
* Enable "Sale" Badge
* Enable Badges Based on "Badges" Attribute

### "Gallery" Category Display Mode

A category using the "Gallery" display mode operates similarly to the default "Static block and products" display mode, except that instead of the typical product grid and toolbar, products are displayed in a simple carousel immediately below the assigned CMS block.

#### Configuration

"Display Mode" is a setting found in the Display Settings tab when editing a category. Choose "Gallery" in this drop-down to enable the new display mode.

### Category and Product Cross-navigation

New cross-navigation settings available on categories allow for direct navigation to sibling categories of the currently viewed category, or sibling products of the currently viewed products. This navigation is placed prominently at the top of the page content.

When viewing a category with category cross-navigation enabled, the name of the _parent_ of the current category is displayed between "Previous" and "Next" navigation links that lead to other children of the same parent category.  (The "Previous" and "Next" order are determined by the categories' positions within their parent.)

The interface is similar when viewing a product whose parent category has product cross-navigation enabled. The parent category's name is displayed between "Previous" and "Next" navigation link that lead to other products assigned to that category. (Note that the relevant parent category is determined by the specific navigation route to the current product page, as represented in the URL.)

In addition, an "Image for Product Cross-navigation" can be attached to categories. With such an image attached to a category with product cross-navigation enabled, this image will be displayed instead of the category name in the product cross-navigation interface.

#### Configuration

The Display Settings tab in the category edit interface contains these new options:

* Enable Cross-navigation with Sibling Products
* Enable Cross-navigation with Sibling Categories
* Image for Product Cross-navigation (Image upload field)

Note: Product cross-navigation affects all products in the category it is enabled on. Category cross-navigation affects the category it is enabled on only, not its child categories. (Make sure to enable category cross-navigation for all sibling categories that you want to be linked by this feature.)

### Using "Gallery" and Cross-navigation Together

The "Gallery" display mode and category/product cross-navigation can be used together to create a visually appealing "Shop the Look" or "Lifestyle Image" presentation for your products.

A presentation like this focuses attention on the connection between multiple products, and starts with drawing the customer's attention to a dynamic image showing multiple products in use in their intended environment. Below are guidelines for creating an optimized structure:

1) Start with a main category to show multiple "looks." With a CMS block, this category can show each lifestyle image prominently with a link to a child category.
2) Create a child category for each "look" and enable both the "Gallery" display mode and category cross-navigation on each. Use a large version of the lifestyle image for each category in the assigned CMS block, and assign the products featured in the lifestyle image to the category.
3) Enable product cross-navigation on each child category as well, and upload a smaller version of the lifestyle image for the "Image for Product Cross-navigation" attribute.

The "Gallery" display mode on each category is an effective presentation of the lifestyle image with its companion products, and the cross-navigation on the product detail pages will keep the lifestyle image prominent while providing easy navigation between the related items.