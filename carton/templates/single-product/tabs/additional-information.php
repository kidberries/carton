<?php
/**
 * Additional Information tab
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton, $post, $product;

$heading = apply_filters( 'carton_product_additional_information_heading', __( 'Additional Information', 'carton' ) );
?>

<h2><?php echo $heading; ?></h2>

<?php $product->list_attributes(); ?>