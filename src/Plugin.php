<?php

namespace WokaFoka\BulkWooCommerceCategories;

use WokaFoka\BulkWooCommerceCategories\Admin\BulkCategoriesPage;
use WokaFoka\BulkWooCommerceCategories\Admin\Menu;
use WokaFoka\BulkWooCommerceCategories\Infrastructure\WooCommerce\ProductQuery;

final class Plugin {
	public function boot(): void {
		if ( ! is_admin() ) {
			return;
		}

		$page = new BulkCategoriesPage(
			new ProductQuery(),
			dirname( __DIR__ ) . '/templates/admin/bulk-categories.php'
		);

		$page->register();

		$menu = new Menu( $page );

		$menu->register();
	}
}
