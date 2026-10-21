<?php

namespace WokaFoka\BulkWooCommerceCategories\Admin;

final class Menu {
	private BulkCategoriesPage $page;

	public function __construct( BulkCategoriesPage $page ) {
		$this->page = $page;
	}

	public function register(): void {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
	}

	public function register_menu(): void {
		$page_title = __( 'Bulk Woocommerce Categories', 'wokafoka-bulk-woocommerce-categories' );

		$page_hook = add_menu_page(
			$page_title,
			'WokaFoka',
			BulkCategoriesPage::CAPABILITY,
			BulkCategoriesPage::SLUG,
			array( $this->page, 'render' ),
			'dashicons-category',
			55.4
		);

		add_submenu_page(
			BulkCategoriesPage::SLUG,
			$page_title,
			$page_title,
			BulkCategoriesPage::CAPABILITY,
			BulkCategoriesPage::SLUG,
			array( $this->page, 'render' )
		);

		$this->page->register_screen( $page_hook );
	}
}
