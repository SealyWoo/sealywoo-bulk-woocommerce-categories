<?php

defined( 'ABSPATH' ) || exit;

?>
<?php if ( $error !== null ) : ?>
	<div class="notice notice-error">
		<p><?php esc_html_e( 'Unable to load products. Please try again.', 'wokafoka-bulk-woocommerce-categories' ); ?></p>
	</div>
<?php endif; ?>
<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
	<input type="hidden" name="page" value="<?php echo esc_attr( $page_slug ); ?>">
	<input type="hidden" name="orderby" value="<?php echo esc_attr( $sorting['orderby'] ); ?>">
	<input type="hidden" name="order" value="<?php echo esc_attr( $sorting['order'] ); ?>">
	<p class="search-box">
		<label class="screen-reader-text" for="wokafoka-products-search"><?php esc_html_e( 'Search by product name or SKU', 'wokafoka-bulk-woocommerce-categories' ); ?></label>
		<input type="search" id="wokafoka-products-search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Name or SKU', 'wokafoka-bulk-woocommerce-categories' ); ?>">
		<button type="submit" class="button"><?php esc_html_e( 'Search products', 'wokafoka-bulk-woocommerce-categories' ); ?></button>
	</p>
</form>
<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
	<input type="hidden" name="page" value="<?php echo esc_attr( $page_slug ); ?>">
	<input type="hidden" name="orderby" value="<?php echo esc_attr( $sorting['orderby'] ); ?>">
	<input type="hidden" name="order" value="<?php echo esc_attr( $sorting['order'] ); ?>">
	<input type="hidden" name="s" value="<?php echo esc_attr( $search ); ?>">
	<?php $table->display(); ?>
</form>
