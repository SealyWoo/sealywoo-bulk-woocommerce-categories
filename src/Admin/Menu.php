<?php

namespace WokaFoka\BulkWooCommerceCategories\Admin;

final class Menu {
	public const CAPABILITY = 'manage_woocommerce';
	public const SLUG       = 'wokafoka';

	public function register(): void {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
	}

	public function register_menu(): void {
		$page_title = __( 'Bulk Woocommerce Categories', 'wokafoka-bulk-woocommerce-categories' );
		$render     = function (): void {
			$this->render_page();
		};

		add_menu_page(
			$page_title,
			'WokaFoka',
			self::CAPABILITY,
			self::SLUG,
			$render,
			'dashicons-category',
			55.4
		);

		add_submenu_page(
			self::SLUG,
			$page_title,
			$page_title,
			self::CAPABILITY,
			self::SLUG,
			$render
		);
	}

	private function render_page(): void {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die(
				esc_html__( 'You do not have permission to access this page.', 'wokafoka-bulk-woocommerce-categories' ),
				'',
				array( 'response' => 403 )
			);
		}

		require __DIR__ . '/../../templates/admin/bulk-categories.php';
	}
}
