# TrailGear Toolkit

![CI](https://github.com/Philipad1234/trailgear-toolkit/actions/workflows/ci.yml/badge.svg)

A custom WooCommerce plugin built for a fictional outdoor-gear store, demonstrating PHP/WordPress plugin development beyond drag-and-drop store configuration: custom product logic, checkout customization, a REST API endpoint, admin tooling, and automation, all built from scratch using WordPress/WooCommerce hooks and filters.

## Why this exists

This is a portfolio project. Rather than just configuring an off-the-shelf WooCommerce store, the goal here is to show hands-on PHP work: writing a proper plugin (not theme functions.php snippets), following WordPress plugin architecture conventions, and using WooCommerce's hook system the way a real client project would require.

Demo store: outdoor/hiking gear retailer ("TrailGear").

## Features

- [X] Plugin scaffold with standard WordPress plugin header
- [X] Custom checkout field (preferred delivery date, saved to order meta)
- [X] REST API endpoint (low-stock product report)
- [X] Admin settings page (WP Settings API)
- [X] Custom "Rentable Gear" product type
- [ ] WP-Cron automation (daily low-stock email digest)
- [X] Cart pricing rule (bulk-discount fee)
- [ ] Order email customization

## Requirements

- WordPress 6.0+
- WooCommerce 8.0+
- PHP 8.0+

## Installation

1. Download or clone this repository.
2. Copy (or symlink) the `trailgear-toolkit` folder into `wp-content/plugins/`.
3. In wp-admin, go to **Plugins** and activate **TrailGear Toolkit**.
4. Requires WooCommerce to be installed and active. The plugin will show an admin notice and stay dormant if WooCommerce isn't detected.

## Project structure
```
trailgear-toolkit/
├── .github/ # CI workflow (GitHub Actions)
│   └── workflows/
│       └── ci.yml
├── admin/ # Admin-only functionality (settings page)
│   └── class-tg-admin-settings.php
├── includes/ # Feature classes (checkout fields, REST API, etc.)
│   ├── class-tg-cart-pricing.php
│   ├── class-tg-checkout-fields.php
│   ├── class-tg-product-rentable.php
│   ├── class-tg-rental-product.php
│   └── class-tg-rest-api.php
├── .gitignore
├── README.md
└── trailgear-toolkit.php # Main plugin file, header + bootstrap
```

## Development notes 

### Custom checkout field 
Added a "Preferred Delivery Date" field using woocommerce_after_order_notes to render it on checkout, woocommerce_checkout_update_order_meta to sanitize and persist the value as order meta (prefixed with _ to keep it out of WordPress's generic Custom Fields UI), and woocommerce_admin_order_data_after_billing_address to surface it on the admin order screen. Input is sanitized on save (sanitize_text_field) and escaped on output (esc_html)

### Admin settings page
Built with the WordPress Settings API (register_setting, add_settings_section, add_settings_field) rather than a custom form handler, so saving/validation goes through WordPress's own options.php pipeline. Exposes two configurable values, a low-stock threshold and a rental deposit percentage, that later features (REST API, cron digest) read via get_option() instead of hardcoding numbers.

### REST API endpoint 
Registered via register_rest_route() on rest_api_init. Returns products at or below the configurable low-stock threshold (read from the settings page via get_option()), querying live data with wc_get_products() and extracting only the needed fields rather than exposing raw WC_Product objects. Protected with a permission_callback that validates a custom X-TrailGear-API-Key header against a saved API key, failing closed (denying access) if no key has been configured.

### Cart pricing rule
Hooked into `woocommerce_cart_calculate_fees` to tally both quantity and subtotal per product category in a single pass over the cart (using each cart item's `get_category_ids()` and `line_total`). When any category reaches 3+ items, applies a 10% discount fee via `WC()->cart->add_fee()`, calculated against that category's actual subtotal rather than a flat amount, so the discount scales with what's actually in the cart.

### Custom "Rentable Gear" product type (in progress)
Registered a new product type by hooking `product_type_selector` (a filter, not an action, it receives the array of type labels and must return it) to add "Rentable Gear" to the dropdown, and `woocommerce_product_class` to tell WooCommerce which PHP class to build when a product's type is `rentable`. `TG_Product_Rentable` extends `WC_Product` and overrides only `get_type()`, inheriting every normal product capability for free.

Load order mattered: `TG_Product_Rentable` contains `extends WC_Product`, which requires WooCommerce's classes to already be loaded. WordPress loads plugins alphabetically, so `trailgear-toolkit` loads before `woocommerce`, requiring the file at the top level would fatal with "Class WC_Product not found". Fixed by hooking the `require_once` to `woocommerce_loaded`.

Added a custom "Rental" tab to the product data box via `woocommerce_product_data_tabs`, and rendered a "Daily Rental Rate ($)" field inside it via `woocommerce_product_data_panels`, using `woocommerce_wp_text_input()` for consistent styling with WooCommerce's built-in fields. Saving uses the type-specific `woocommerce_process_product_meta_rentable` hook, so the save logic only runs for this product type, with `wc_format_decimal()` for sanitizing a price value on the way in.

Still to do: display the rate and an add-to-cart flow on the storefront, and apply the rental deposit percentage as a cart fee.

### Custom "Rentable Gear" product type
Registered a new product type by hooking `product_type_selector` (a filter, not an action, it receives the array of type labels and must return it) to add "Rentable Gear" to the dropdown, and `woocommerce_product_class` to tell WooCommerce which PHP class to build when a product's type is `rentable`. `TG_Product_Rentable` extends `WC_Product` and overrides `get_type()`, `get_price_html()`, and `get_price()`, inheriting every other product capability for free.

Load order mattered: `TG_Product_Rentable` contains `extends WC_Product`, which requires WooCommerce's classes to already be loaded. WordPress loads plugins alphabetically, so `trailgear-toolkit` loads before `woocommerce`, requiring the file at the top level would fatal with "Class WC_Product not found." Fixed by hooking the `require_once` to `woocommerce_loaded`.

Added a custom "Rental" tab to the product data box via `woocommerce_product_data_tabs`, and a "Daily Rental Rate ($)" field inside it via `woocommerce_product_data_panels`, using `woocommerce_wp_text_input()` for consistent styling. Saving uses the type-specific `woocommerce_process_product_meta_rentable` hook, with `wc_format_decimal()` for sanitizing the price input.

On the storefront, `get_price_html()` formats the saved rate with `wc_price()` for display, while `get_price()` separately returns the raw number used in cart and checkout math, since WooCommerce reads these two methods for different purposes and overriding only one left the cart showing $0.00. `woocommerce_is_purchasable` is filtered so a rentable product is only purchasable once a rate has been saved, and the `woocommerce_rentable_add_to_cart` action, named automatically from the product type, reuses WooCommerce's own Simple product add-to-cart template rather than hand-building form markup.

In the cart, `woocommerce_cart_calculate_fees` tallies the subtotal of every rentable line item and applies a configurable deposit (`tg_rental_deposit_percent`, read via `get_option()`) as a separate, positive "Rental Deposit" fee alongside the regular rental charge.

## Continuous Integration
This repo runs a GitHub Actions workflow on every push, checking all PHP files for syntax errors using `php -l`. See `.github/workflows/ci.yml`.

## Author

Philip Adams

## Portfolio link
https://philip-adams-portfolio.vercel.app/

## License

GPL-2.0+ (standard for WordPress plugins)
