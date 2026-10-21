<?php

defined( 'ABSPATH' ) || exit;

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Bulk Woocommerce Categories', 'wokafoka-bulk-woocommerce-categories' ); ?></h1>
	<?php if ( $error !== null ) : ?>
		<div class="notice notice-error">
			<p><?php esc_html_e( 'Unable to load products. Please try again.', 'wokafoka-bulk-woocommerce-categories' ); ?></p>
		</div>
	<?php endif; ?>
	<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
		<input type="hidden" name="page" value="<?php echo esc_attr( $page_slug ); ?>">
		<?php $table->display(); ?>
	</form>
</div>
