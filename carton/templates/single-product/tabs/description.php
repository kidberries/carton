<?php
/**
 * Description tab
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton, $post;

$heading = esc_html( apply_filters('carton_product_description_heading', __( 'Product Description', 'carton' ) ) );
?>

<h2><?php echo $heading; ?></h2>

<?php the_content(); ?>