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
	public function get_page( int $per_page, int $offset ) {
		$products = $this->query(
			array(
				'limit'    => $per_page,
				'offset'   => $offset,
				'paginate' => false,
				'return'   => 'objects',
			)
		);

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
		}
	}
}
