# WooCommerce Price per Unit

This simple plugin enables the possibility to display the price per unit of a product, under the price.
| :boom: Requires [WooCommerce](https://wordpress.org/plugins/search/woocommerce/) + [Meta Box](https://wordpress.org/plugins/meta-box/) |
| --------------------------------------------- |
| :boom: May require code modification to suit your needs |
| :warning: versions prior to 1.0 have a function to display the *price per unit* only to admins. Please remove accordingly. |

## How does it work ?

On your product page (backend), you define :
* a coefficient (the net price of your product)
* a unit (kilograms, liters, whatever...)

Then it displays the *price per unit* under the product price.

**:arrow_right: What is the calculation?**
Product price * coefficient
Example : 500g of coffee priced 10€, unit is kg => 10 * 0.500 => `20,00€ / kg`.

**:arrow_right: Does it handle *variable* products?**
Yes, it uses the lowest priced variation to calculate.

**:arrow_right: Does it handle *on sale* products?**
Yes, it displays the regular price crossed out, and the sale price normally.

**:arrow_right: Does it handle *multiple currencies* shop?**
I don't know but I think it should.

**:nail_care: What about css?**
The *price per unit* lives into a div with `class="price-per-unit"`, so that you can edit your stylesheet to customize the look of this addition.