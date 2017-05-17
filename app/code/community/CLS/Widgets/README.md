## Custom Widgets

Implements new types of widgets that are useful for many merchants.

### Widget Types

Look for the following new entries in the Type drop-down when creating a new widget instance in the Magento admin.

#### Catalog Product Carousel

Displays a rotating carousel of product entries

##### Content Dependencies

The product carousel widget requires a category containing products.

##### Widget Options

1. Parent Category: This defines the category from which products in the carousel will be drawn. A common practice would be to create a category for the express purpose of driving a carousel.
2. Carousel Display Title: This title will show on the front-end above the carousel.  (Leaving this blank will result in no title markup at all.)
3. Sort Order: Whether the products in the carousel will be sorted by their position, or randomly.
4. Auto-Start Slideshow: Whether the carousel will cycle automatically.
5. Timeout Between Scrolling: Milliseconds to pause between each transition.
6. Scroll one page at a time: Whether to scroll one full "page" with each transition, as opposed to incrementing by only one carousel item.
7. Max Number of Products: Limit on how many products will be displayed in the carousel.
8. Products to Show at a Time: The number of products to show in a single "page" of the carousel without scrolling.
9. Products to Show at a Time (on Large/Small-Desktop Screens): Number of products per "page" for large displays.
10. Products to Show at a Time (on Medium/Tablet Screens): Number of products per "page" for medium displays.
11. Products to Show at a Time (on Small/Mobile Screens): Number of products per "page" for small displays.
12. Include Pager: Whether to include "pager" controls that allow switching between specific "pages" of the carousel.
13. Display Numbers in Pagers: Whether to use the sequential slide numbers in the pager elements.
14. Include Prev/Next Controls: Whether to include controls that allow scrolling to the previous or next "page" of the carousel. NOTE: These controls will show if Auto-scroll is off and the Pager is not included, even if this option is set to No.
15. Pause on Hover: Whether the transitions should pause when the user's mouse is over the slideshow.

#### Content Slideshow

Displays an animated slideshow of arbitrary content

##### Content Dependencies

The slides for the slideshow are driven by CMS Static Blocks. Create one static block for each slide.

To take advantage of some basic default formatting for slide captions (including being positioned in the bottom right of the slideshow container),
use the following markup in your slide:

<code>
&lt;div class="copy"&gt;
&lt;h3&gt;Title&lt;/h3&gt;
&lt;p&gt;Text&lt;/p&gt;
&lt;/div&gt;
</code>

The positioning of the "copy" div is absolute, so it will automatically be displayed on top of whatever content precedes it in the slide (like an image).

##### Widget Options

1. Transition Animation: Animated effect to use when transitioning from one slide to another.
2. Auto-Start Slideshow: Whether the slides will cycle automatically.
3. Timeout Between Slides: Milliseconds to pause between each slide transition.
4. Include Pager: Whether to include "pager" controls that allow switching between specific slides.
5. Display Numbers in Pagers: Whether to use the sequential slide numbers in the pager elements.
6. Display Pager Over Slides: Whether the pager should overlap the slides, as opposed to displaying below them.
7. Include Prev/Next Controls: Whether to include controls that allow scrolling to the previous or next slide.
8. Hide Prev/Next Controls when Not Hovering: If Yes, the prev/next controls will become invisible when the user moves their mouse away from the slideshow.
9. Custom Prev Link Text: If specified this text (or HTML) will be used for the Prev control instead of the default display.
10. Custom Next Link Text: If specified, this text (or HTML) will be used for the Next control instead of the default display.
10. Pause on Hover: Whether the slide transitions should pause when the user's mouse is over the slideshow.
11. Specify Static Blocks: Choose the static blocks that are part of the slideshow.

#### Content Slideshow (Banners) (Enterprise Only)

Identical to the standard Content Slideshow, except the slide content is driven by CMS Banners

##### Widget Options

Options are identical to the standard Content Slideshow, with the following exceptions:

1. Banners to Display: Identical to the same option in the default Banner Rotator widget.
2. Restrict by Banner Types: Identical to the same option in the default Banner Rotator widget.
3. Specify Banners (instead of Specify Static Blocks): Identical to the same option in the default Banner Rotator widget.