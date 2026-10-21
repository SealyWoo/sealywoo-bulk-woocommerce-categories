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

		$menu = new Menu();
		$menu->register();

		$page = new BulkCategoriesPage(
			new ProductQuery(),
			dirname( __DIR__ ) . '/templates/admin/products-table.php'
		);

		$page->register();
	}
}
