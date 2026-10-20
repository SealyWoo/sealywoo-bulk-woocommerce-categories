<?php

namespace WokaFoka\BulkWooCommerceCategories\Admin;

final class Menu {
	private const CAPABILITY = 'manage_woocommerce';
	private const SLUG       = 'wokafoka';

	private string $template_path;

	public function __construct( string $template_path ) {
		$this->template_path = $template_path;
	}

	public function register(): void {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
	}

	public function register_menu(): void {
		$page_title = __( 'Bulk Woocommerce Categories', 'wokafoka-bulk-woocommerce-categories' );

		add_menu_page(
			$page_title,
			'WokaFoka',
			self::CAPABILITY,
			self::SLUG,
			array( $this, 'render_page' ),
			'dashicons-category',
			55.4
		);

		add_submenu_page(
			self::SLUG,
			$page_title,
			$page_title,
			self::CAPABILITY,
			self::SLUG,
			array( $this, 'render_page' )
		);
	}

	public function render_page(): void {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die(
				esc_html__( 'You do not have permission to access this page.', 'wokafoka-bulk-woocommerce-categories' ),
				'',
				array( 'response' => 403 )
			);
		}

		require $this->template_path;
	}
}
