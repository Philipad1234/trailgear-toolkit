# TrailGear Toolkit

A custom WooCommerce plugin built for a fictional outdoor-gear store, demonstrating
PHP/WordPress plugin development beyond drag-and-drop store configuration —
custom product logic, checkout customization, a REST API endpoint, admin tooling,
and automation, all built from scratch using WordPress/WooCommerce hooks and filters.

## Why this exists

This is a portfolio project. Rather than just configuring an off-the-shelf
WooCommerce store, the goal here is to show hands-on PHP work: writing a
proper plugin (not theme functions.php snippets), following WordPress plugin
architecture conventions, and using WooCommerce's hook system the way a real
client project would require.

Demo store: outdoor/hiking gear retailer ("TrailGear").

## Features

- [x] Plugin scaffold with standard WordPress plugin header
- [X] Custom checkout field (preferred delivery date, saved to order meta)
- [ ] REST API endpoint (low-stock product report)
- [X] Admin settings page (WP Settings API)
- [ ] Custom "Rentable Gear" product type
- [ ] WP-Cron automation (daily low-stock email digest)
- [ ] Cart pricing rule (bulk-discount fee)
- [ ] Order email customization

## Requirements

- WordPress 6.0+
- WooCommerce 8.0+
- PHP 8.0+

## Installation

1. Download or clone this repository.
2. Copy (or symlink) the `trailgear-toolkit` folder into `wp-content/plugins/`.
3. In wp-admin, go to **Plugins** and activate **TrailGear Toolkit**.
4. Requires WooCommerce to be installed and active — the plugin will show an
   admin notice and stay dormant if WooCommerce isn't detected.

## Project structure
```
trailgear-toolkit/
├── trailgear-toolkit.php # Main plugin file, header + bootstrap
├── includes/ # Feature classes (checkout fields, REST API, etc.)
└── admin/ # Admin-only functionality (settings page)
```

## Development notes 

### Custom checkout field 
Added a "Preferred Delivery Date" field using woocommerce_after_order_notes to render it on checkout, woocommerce_checkout_update_order_meta to sanitize and persist the value as order meta (prefixed with _ to keep it out of WordPress's generic Custom Fields UI), and woocommerce_admin_order_data_after_billing_address to surface it on the admin order screen. Input is sanitized on save (sanitize_text_field) and escaped on output (esc_html)

### Admin settings page
Built with the WordPress Settings API (register_setting, add_settings_section, add_settings_field) rather than a custom form handler, so saving/validation goes through WordPress's own options.php pipeline. Exposes two configurable values, a low-stock threshold and a rental deposit percentage, that later features (REST API, cron digest) read via get_option() instead of hardcoding numbers.

## Author

Philip Adams

## Portfolio link
https://philip-adams-portfolio.vercel.app/

## License

GPL-2.0+ (standard for WordPress plugins)
