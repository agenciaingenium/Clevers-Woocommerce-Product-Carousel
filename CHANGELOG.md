# Changelog

All notable changes to **Clevers Product Carousel** are documented here.
The format follows [Keep a Changelog](https://keepachangelog.com/) and the
project adheres to [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Fixed
- `tests/bootstrap.php` now defines `WC_Product` with the WooCommerce
  surface used by `helpers-discount.php` (`is_on_sale`, `is_type`,
  `get_regular_price`, `get_sale_price`, `get_children`) so PHPUnit can
  generate mocks for discount tests.
- `do_action()` mock in `tests/bootstrap.php` now invokes the registered
  callbacks instead of being a no-op, so the
  `clevers_carousel_before_render` / `clevers_carousel_after_render`
  assertions in `HooksTest::test_before_and_after_render_actions_receive_products`
  actually fire.
- Bootstrap now `require_once`s `includes/helpers-discount.php`, which
  exposes `clevers_product_carousel_get_discount_percentage()` for the
  `RenderIntegrationTest::test_discount_percentage_returns_null_for_non_sale_product`
  case.

### Added
- `phpstan.neon` committed next to `phpstan.neon.dist` so the static
  analysis gate can run with `phpstan analyse -c phpstan.neon` locally
  without copying the dist file.

## [1.2.3] - 2026-06

### Added
- PHPStan static analysis gate at level 9 via `phpstan.neon.dist`.
- WebP support via `<picture>` element with lazy loading.
- Documentation for public filters and actions.

## [1.2.0] - 2026-04

### Added
- WooCommerce mock integration tests covering shortcodes, render
  filters and public hooks.

## [1.0.0] - 2026-02

### Added
- Initial public release of the WooCommerce product carousel plugin.
