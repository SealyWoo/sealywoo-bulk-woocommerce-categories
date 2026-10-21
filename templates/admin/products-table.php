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
	<?php $table->display(); ?>
</form>
