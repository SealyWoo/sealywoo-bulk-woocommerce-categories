<?php

namespace WokaFoka\BulkWooCommerceCategories;

use WokaFoka\BulkWooCommerceCategories\Admin\Menu;

final class Plugin {
	public function boot(): void {
		if ( ! is_admin() ) {
			return;
		}

		$menu = new Menu( dirname( __DIR__ ) . '/templates/admin/bulk-categories.php' );

		$menu->register();
	}
}
