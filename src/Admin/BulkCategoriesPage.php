<?php

namespace WokaFoka\BulkWooCommerceCategories\Admin;

use WokaFoka\BulkWooCommerceCategories\Admin\Tables\ProductsTable;
use WokaFoka\BulkWooCommerceCategories\Infrastructure\WooCommerce\ProductQuery;

final class BulkCategoriesPage {
	public const CAPABILITY       = Menu::CAPABILITY;
	public const SLUG             = Menu::SLUG;
	public const PER_PAGE_OPTION  = 'wokafoka_products_per_page';
	public const DEFAULT_PER_PAGE = 20;
	public const MAX_PER_PAGE     = 999;

	private ProductQuery $query;
	private string $template_path;
	private ?ProductsTable $table = null;

	public function __construct( ProductQuery $query, string $template_path ) {
		$this->query         = $query;
		$this->template_path = $template_path;
	}

	public function register(): void {
		add_filter( 'set_screen_option_' . self::PER_PAGE_OPTION, array( $this, 'save_per_page' ), 10, 3 );
		add_action( 'load-toplevel_page_' . self::SLUG, array( $this, 'load' ) );
		add_action( 'wokafoka_bulk_categories_page_content', array( $this, 'render' ) );
	}

	public function save_per_page( $status, string $option, $value ) {
		if ( $option !== self::PER_PAGE_OPTION ) {
			return $status;
		}

		if ( ! current_user_can( self::CAPABILITY ) ) {
			return false;
		}

		return filter_var(
			$value,
			FILTER_VALIDATE_INT,
			array( 'options' => array( 'min_range' => 1, 'max_range' => self::MAX_PER_PAGE ) )
		);
	}

	public function load(): void {
		$this->check_access();

		add_screen_option(
			'per_page',
			array(
				'label'   => __( 'Products per page', 'wokafoka-bulk-woocommerce-categories' ),
				'default' => self::DEFAULT_PER_PAGE,
				'option'  => self::PER_PAGE_OPTION,
			)
		);

		if ( ! class_exists( '\WP_List_Table', false ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
		}

		$this->table = new ProductsTable( $this->query );
		$this->table->prepare_items();
	}

	public function render(): void {
		$this->check_access();

		if ( $this->table === null ) {
			$this->load();
		}

		$table     = $this->table;
		$error     = $table->get_error();
		$page_slug = self::SLUG;

		require $this->template_path;
	}

	private function check_access(): void {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die(
				esc_html__( 'You do not have permission to access this page.', 'wokafoka-bulk-woocommerce-categories' ),
				'',
				array( 'response' => 403 )
			);
		}
	}
}
