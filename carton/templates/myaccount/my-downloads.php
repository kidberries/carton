<?php
/**
 * My Orders
 *
 * Shows recent orders on the account page
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton;

if ( $downloads = $carton->customer->get_downloadable_products() ) : ?>

	<h2><?php echo apply_filters( 'carton_my_account_my_downloads_title', __( 'Available downloads', 'carton' ) ); ?></h2>

	<ul class="digital-downloads">
		<?php foreach ( $downloads as $download ) : ?>
			<li>
				<?php
					do_action( 'carton_available_download_start', $download );

					if ( is_numeric( $download['downloads_remaining'] ) )
						echo apply_filters( 'carton_available_download_count', '<span class="count">' . sprintf( _n( '%s download remaining', '%s downloads remaining', $download['downloads_remaining'], 'carton' ), $download['downloads_remaining'] ) . '</span> ', $download );

					echo apply_filters( 'carton_available_download_link', '<a href="' . esc_url( $download['download_url'] ) . '">' . $download['download_name'] . '</a>', $download );

					do_action( 'carton_available_download_end', $download );
				?>
			</li>
		<?php endforeach; ?>
	</ul>

<?php endif; ?>