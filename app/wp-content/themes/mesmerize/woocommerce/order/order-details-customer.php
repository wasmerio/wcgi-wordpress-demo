<?php
/**
 * Order Customer Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.4
 */

defined( 'ABSPATH' ) || exit;

/** @var WC_Order $order */
$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>
<?php if ( $show_shipping ) : ?>
    <section class="woocommerce-customer-details">
        <div class="woocommerce-customer-details-card">

			<?php if ( $order->get_billing_phone() || $order->get_billing_email() ): ?>
                <div>
                    <h3><?php _e( 'Customer details', 'mesmerize' ); ?></h3>

                    <table class="woocommerce-table woocommerce-table--customer-details shop_table customer_details">

						<?php if ( $order->get_customer_note() ) : ?>
                            <tr>
                                <th><?php _e( 'Note:', 'mesmerize' ); ?></th>
                                <td><?php echo wptexturize( $order->get_customer_note() ); ?></td>
                            </tr>
						<?php endif; ?>

						<?php if ( $order->get_billing_email() ) : ?>
                            <tr>
                                <th><?php _e( 'Email:', 'mesmerize' ); ?></th>
                                <td><?php echo esc_html( $order->get_billing_email() ); ?></td>
                            </tr>
						<?php endif; ?>

						<?php if ( $order->get_billing_phone() ) : ?>
                            <tr>
                                <th><?php _e( 'Phone:', 'mesmerize' ); ?></th>
                                <td><?php echo esc_html( $order->get_billing_phone() ); ?></td>
                            </tr>
						<?php endif; ?>

						<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>

                    </table>
                </div>
			<?php endif; ?>

            <section
                    class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses">
                <div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1">
                    <h3 class="woocommerce-column__title"><?php esc_html_e( 'Billing address', 'mesmerize' ); ?></h3>
                    <address>
						<?php echo wp_kses_post( $order->get_formatted_billing_address( __( 'N/A', 'mesmerize' ) ) ); ?>
                    </address>
                </div>
                <div class="woocommerce-column woocommerce-column--2 woocommerce-column--shipping-address col-2">
                    <h3 class="woocommerce-column__title"><?php esc_html_e( 'Shipping address', 'mesmerize' ); ?></h3>
                    <address>
						<?php echo wp_kses_post( $order->get_formatted_shipping_address( __( 'N/A',
							'mesmerize' ) ) ); ?>
                    </address>
                </div>

            </section>


			<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>
        </div>
    </section>
<?php endif; ?>
