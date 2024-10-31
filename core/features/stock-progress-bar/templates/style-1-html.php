<?php

defined( 'ABSPATH' ) || exit;

?>

<div class="metsale-stock-status-bar-wrapper stock-progress-bar-1">
    <p class="stock-progress-bar__message"><?php echo esc_html( $message ); ?></p>
    <progress max="<?php echo esc_attr($total_stock); ?>" value="<?php echo esc_attr($total_sales); ?>"></progress>
</div>