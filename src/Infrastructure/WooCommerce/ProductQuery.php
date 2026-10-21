<?php

namespace WokaFoka\BulkWooCommerceCategories\Infrastructure\WooCommerce;

final class ProductQuery {
	/**
	 * @return int|\WP_Error
	 */
	public function count() {
		$result = $this->query(
			array(
				'limit'    => 1,
				'paginate' => true,
				'return'   => 'ids',
			)
		);

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( ! is_object( $result ) || ! isset( $result->total ) ) {
			return new \WP_Error( 'invalid_product_count' );
		}

		return (int) $result->total;
	}

	/**
	 * @return array|\WP_Error
	 */
	public function get_page( int $per_page, int $offset, string $orderby = 'id', string $order = 'asc' ) {
		$orderby = in_array( $orderby, array( 'id', 'name', 'sku' ), true ) ? $orderby : 'id';
		$order   = strtoupper( $order ) === 'DESC' ? 'DESC' : 'ASC';
		$args    = array(
			'limit'    => $per_page,
			'offset'   => $offset,
			'paginate' => false,
			'return'   => 'objects',
			'orderby'  => $orderby === 'name' ? 'title ID' : 'ID',
			'order'    => $order,
		);

		if ( $orderby === 'sku' ) {
			$args['wokafoka_sku_order'] = $order;
		}

		$products = $this->query( $args );

		if ( is_wp_error( $products ) ) {
			return $products;
		}

		if ( ! is_array( $products ) ) {
			return new \WP_Error( 'invalid_product_results' );
		}

		$types = wc_get_product_types();
		$items = array();

		foreach ( $products as $product ) {
			if ( ! $product instanceof \WC_Product || ! current_user_can( 'read_post', $product->get_id() ) ) {
				return new \WP_Error( 'product_not_readable' );
			}

			$type   = $product->get_type();
			$status = get_post_status_object( $product->get_status() );

			$items[] = array(
				'id'     => $product->get_id(),
				'name'   => $product->get_name(),
				'sku'    => $product->get_sku(),
				'type'   => $types[ $type ] ?? $type,
				'status' => $status !== null ? $status->label : $product->get_status(),
			);
		}

		return $items;
	}

	/**
	 * @return array|object|\WP_Error
	 */
	private function query( array $args ) {
		if ( ! function_exists( 'wc_get_products' ) ) {
			return new \WP_Error( 'woocommerce_unavailable' );
		}

		$statuses = array( 'publish' );

		if ( current_user_can( 'edit_others_products' ) ) {
			$statuses = array_merge( $statuses, array( 'draft', 'pending', 'future' ) );
		}

		if ( current_user_can( 'read_private_products' ) ) {
			$statuses[] = 'private';
		}

		$sku_sorting = null;

		if ( isset( $args['wokafoka_sku_order'] ) ) {
			$sku_sorting = $this->get_sku_sorting_filter();
			add_filter( 'posts_clauses', $sku_sorting, 10, 2 );
		}

		try {
			return wc_get_products(
				array_merge(
					array(
						'status'  => $statuses,
						'orderby' => 'ID',
						'order'   => 'ASC',
					),
					$args
				)
			);
		} catch ( \Exception $exception ) {
			return new \WP_Error( 'product_query_failed' );
		} finally {
			if ( $sku_sorting !== null ) {
				remove_filter( 'posts_clauses', $sku_sorting, 10 );
			}
		}
	}

	private function get_sku_sorting_filter(): \Closure {
		return static function ( array $clauses, \WP_Query $query ): array {
			global $wpdb;

			$order = $query->get( 'wokafoka_sku_order' );

			if ( ! in_array( $order, array( 'ASC', 'DESC' ), true ) ) {
				return $clauses;
			}

			if ( strpos( $clauses['join'], ' AS wokafoka_product_sorting ' ) === false ) {
				$clauses['join'] .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} AS wokafoka_product_sorting"
					. " ON ({$wpdb->posts}.ID = wokafoka_product_sorting.product_id)";
			}

			$clauses['orderby'] = "wokafoka_product_sorting.sku {$order}, {$wpdb->posts}.ID {$order}";

			return $clauses;
		};
	}
}
