<?php
/**
 * The Template for displaying products in a product tag. Simply includes the archive template.
 *
 * Override this template by copying it to yourtheme/carton/taxonomy-product_tag.php
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

carton_get_template( 'archive-product.php' );