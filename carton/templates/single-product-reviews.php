<?php
/**
 * Display single product reviews (comments)
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     2.0.3
 */
global $carton, $product;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<?php if ( comments_open() ) : ?><div id="reviews"><?php

	echo '<div id="comments">';

	if ( get_option('carton_enable_review_rating') == 'yes' ) {

		$count = $product->get_rating_count();

		if ( $count > 0 ) {

			$average = $product->get_average_rating();

			echo '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';

			echo '<div class="star-rating" title="'.sprintf(__( 'Rated %s out of 5', 'carton' ), $average ).'"><span style="width:'.( ( $average / 5 ) * 100 ) . '%"><strong itemprop="ratingValue" class="rating">'.$average.'</strong> '.__( 'out of 5', 'carton' ).'</span></div>';

			echo '<h2>'.sprintf( _n('%s has %s review', '%s has %s reviews', $count, 'carton'), wptexturize($post->post_title), '<span itemprop="ratingCount" class="count">'.$count.'</span>' ).'</h2>';

			echo '</div>';

		} else {

			echo '<h2>'.__( 'Reviews', 'carton' ).'</h2>';

		}

	} else {

		echo '<h2>'.__( 'Reviews', 'carton' ).'</h2>';

	}

	$title_reply = '';

	if ( have_comments() ) :

		echo '<ol class="commentlist">';

		wp_list_comments( array( 'callback' => 'carton_comments' ) );

		echo '</ol>';

		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Previous', 'carton' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Next <span class="meta-nav">&rarr;</span>', 'carton' ) ); ?></div>
			</div>
		<?php endif;

		echo '<p class="add_review"><a href="#review_form" class="inline show_review_form button" title="' . __( 'Add Your Review', 'carton' ) . '">' . __( 'Add Review', 'carton' ) . '</a></p>';

		$title_reply = __( 'Add a review', 'carton' );

	else :

		$title_reply = __( 'Be the first to review', 'carton' ).' &ldquo;'.$post->post_title.'&rdquo;';

		echo '<p class="noreviews">'.__( 'There are no reviews yet, would you like to <a href="#review_form" class="inline show_review_form">submit yours</a>?', 'carton' ).'</p>';

	endif;

	$commenter = wp_get_current_commenter();

	echo '</div><div id="review_form_wrapper"><div id="review_form">';

	$comment_form = array(
		'title_reply' => $title_reply,
		'comment_notes_before' => '',
		'comment_notes_after' => '',
		'fields' => array(
			'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'carton' ) . '</label> ' . '<span class="required">*</span>' .
			            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></p>',
			'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'carton' ) . '</label> ' . '<span class="required">*</span>' .
			            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></p>',
		),
		'label_submit' => __( 'Submit Review', 'carton' ),
		'logged_in_as' => '',
		'comment_field' => ''
	);

	if ( get_option('carton_enable_review_rating') == 'yes' ) {

		$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="rating">' . __( 'Rating', 'carton' ) .'</label><select name="rating" id="rating">
			<option value="">'.__( 'Rate&hellip;', 'carton' ).'</option>
			<option value="5">'.__( 'Perfect', 'carton' ).'</option>
			<option value="4">'.__( 'Good', 'carton' ).'</option>
			<option value="3">'.__( 'Average', 'carton' ).'</option>
			<option value="2">'.__( 'Not that bad', 'carton' ).'</option>
			<option value="1">'.__( 'Very Poor', 'carton' ).'</option>
		</select></p>';

	}

	$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . __( 'Your Review', 'carton' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>' . $carton->nonce_field('comment_rating', true, false);

	comment_form( apply_filters( 'carton_product_review_comment_form_args', $comment_form ) );

	echo '</div></div>';

?><div class="clear"></div></div>
<?php endif; ?>