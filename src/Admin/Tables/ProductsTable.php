<?php

namespace WokaFoka\BulkWooCommerceCategories\Admin\Tables;

use WokaFoka\BulkWooCommerceCategories\Admin\BulkCategoriesPage;
use WokaFoka\BulkWooCommerceCategories\Infrastructure\WooCommerce\ProductQuery;

final class ProductsTable extends \WP_List_Table {
	private ProductQuery $query;
	private ?\WP_Error $error = null;

	public function __construct( ProductQuery $query ) {
		$this->query = $query;

		parent::__construct(
			array(
				'singular' => 'wokafoka_product',
				'plural'   => 'wokafoka_products',
				'ajax'     => false,
			)
		);
	}

	public function get_columns(): array {
		return array(
			'id'     => __( 'ID', 'wokafoka-bulk-woocommerce-categories' ),
			'name'   => __( 'Name', 'wokafoka-bulk-woocommerce-categories' ),
			'sku'    => __( 'SKU', 'wokafoka-bulk-woocommerce-categories' ),
			'type'   => __( 'Type', 'wokafoka-bulk-woocommerce-categories' ),
			'status' => __( 'Status', 'wokafoka-bulk-woocommerce-categories' ),
		);
	}

	protected function get_sortable_columns(): array {
		return array(
			'id'   => array( 'id', false, '', __( 'Table ordered by ID.', 'wokafoka-bulk-woocommerce-categories' ), 'asc' ),
			'name' => array( 'name', false, '', __( 'Table ordered by name.', 'wokafoka-bulk-woocommerce-categories' ) ),
			'sku'  => array( 'sku', false, '', __( 'Table ordered by SKU.', 'wokafoka-bulk-woocommerce-categories' ) ),
		);
	}

	public function get_sorting(): array {
		$orderby = isset( $_GET['orderby'] ) && is_string( $_GET['orderby'] ) ? wp_unslash( $_GET['orderby'] ) : 'id';
		$order   = isset( $_GET['order'] ) && is_string( $_GET['order'] ) ? strtolower( wp_unslash( $_GET['order'] ) ) : 'asc';

		return array(
			'orderby' => in_array( $orderby, array( 'id', 'name', 'sku' ), true ) ? $orderby : 'id',
			'order'   => in_array( $order, array( 'asc', 'desc' ), true ) ? $order : 'asc',
		);
	}

	public function get_search(): string {
		if ( ! isset( $_GET['s'] ) || ! is_string( $_GET['s'] ) ) {
			return '';
		}

		return trim( wp_check_invalid_utf8( wp_unslash( $_GET['s'] ) ) );
	}

	public function prepare_items(): void {
		$this->items = array();
		$this->error = null;

		$per_page = max(
			1,
			min(
				BulkCategoriesPage::MAX_PER_PAGE,
				$this->get_items_per_page( BulkCategoriesPage::PER_PAGE_OPTION, BulkCategoriesPage::DEFAULT_PER_PAGE )
			)
		);

		$search      = $this->get_search();
		$total_items = $this->query->count( $search );

		if ( is_wp_error( $total_items ) ) {
			$this->error = $total_items;
			$total_items = 0;
		}

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);

		if ( $this->error !== null || $total_items === 0 ) {
			return;
		}

		$sorting = $this->get_sorting();
		$items   = $this->query->get_page(
			$per_page,
			( $this->get_pagenum() - 1 ) * $per_page,
			$sorting['orderby'],
			$sorting['order'],
			$search
		);

		if ( is_wp_error( $items ) ) {
			$this->error = $items;
			return;
		}

		$this->items = $items;
	}

	public function column_default( $item, $column_name ): string {
		switch ( $column_name ) {
			case 'id':
				return esc_html( (string) $item['id'] );
			case 'name':
				return '<strong>' . esc_html( $item['name'] ) . '</strong>';
			case 'sku':
				return esc_html( $item['sku'] !== '' ? $item['sku'] : '—' );
			case 'type':
				return esc_html( $item['type'] );
			case 'status':
				return esc_html( $item['status'] );
			default:
				return '';
		}
	}

	public function no_items(): void {
		if ( $this->error !== null ) {
			esc_html_e( 'Products could not be loaded.', 'wokafoka-bulk-woocommerce-categories' );
			return;
		}

		esc_html_e( 'No products found.', 'wokafoka-bulk-woocommerce-categories' );
	}

	public function get_error(): ?\WP_Error {
		return $this->error;
	}

	protected function get_default_primary_column_name(): string {
		return 'name';
	}
}
