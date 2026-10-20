<?php
/**
 * Plugin Name: WokaFoka Bulk WooCommerce Categories
 * Description: Adds the WokaFoka admin menu and Bulk Woocommerce Categories page.
 * Version: 0.1.0
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * Text Domain: wokafoka-bulk-woocommerce-categories
 */

use WokaFoka\BulkWooCommerceCategories\Plugin;

defined( 'ABSPATH' ) || exit;

if ( ! is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	add_action(
		'admin_notices',
		static function (): void {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return;
			}
			?>
			<div class="notice notice-error">
				<p><?php esc_html_e( 'WokaFoka Bulk WooCommerce Categories: run composer dump-autoload in the plugin directory to generate the autoloader.', 'wokafoka-bulk-woocommerce-categories' ); ?></p>
			</div>
			<?php
		}
	);

	return;
}

require_once __DIR__ . '/vendor/autoload.php';

( new Plugin() )->boot();
