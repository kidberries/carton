<?php
/**
 * CartoN cart
 *
 * The CartoN cart class stores cart data and active coupons as well as handling customer sessions and some cart related urls.
 * The cart class also has a price calculation function which calls upon other classes to calculate totals.
 *
 * @class 		CTN_Cart
 * @version		2.0.0
 * @package		CartoN/Classes
 * @category	Class
 * @author 		CartonThemes
 */
class CTN_Cart {

	/** @var array contains an array of cached parameters. Product attributes parameters etc .*/
	public $cached;

	/** @var array Contains an array of cart items. */
	public $cart_contents;

	/** @var array Contains an array of coupon codes applied to the cart. */
	public $applied_coupons;

	/** @var array Contains an array of coupon code discounts after they have been applied. */
	public $coupon_discount_amounts;

	/** @var float The total cost of the cart items. */
	public $cart_contents_total;

	/** @var float The total weight of the cart items. */
	public $cart_contents_weight;

	/** @var float The total count of the cart items. */
	public $cart_contents_count;

	/** @var float The total tax for the cart items. */
	public $cart_contents_tax;

	/** @var float Cart grand total. */
	public $total;

	/** @var float Cart subtotal. */
	public $subtotal;

	/** @var float Cart subtotal without tax. */
	public $subtotal_ex_tax;

	/** @var float Total cart tax. */
	public $tax_total;

	/** @var array An array of taxes/tax rates for the cart. */
	public $taxes;

	/** @var array An array of taxes/tax rates for the shipping. */
	public $shipping_taxes;

	/** @var float Discounts before tax. */
	public $discount_cart;

	/** @var float Discounts after tax. */
	public $discount_total;

	/** @var float Total for additional fees. */
	public $fee_total;

	/** @var float Discounted Shipping cost. */
	public $shipping_total;

	/** @var float Real Shipping cost. */
	public $shipping_total_real;

	/** @var array Shipping Info. */
	public $shipping_info;

	/** @var float Shipping tax. */
	public $shipping_tax_total;

	/** @var float Shipping title/label. */
	public $shipping_label;

	/** @var CTN_Tax */
	public $tax;

	/** @var array An array of fees. */
	public $fees;

	/** @var array An array of applied discounts and its values. */
	public $discount_totals;

	/**
	 * Constructor for the cart class. Loads options and hooks in the init method.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->tax = new CTN_Tax();
		$this->prices_include_tax = ( get_option( 'carton_prices_include_tax' ) == 'yes' ) ? true : false;
		$this->tax_display_cart = get_option( 'carton_tax_display_cart' );
		$this->dp = (int) get_option( 'carton_price_num_decimals' );

		$this->display_totals_ex_tax = $this->tax_display_cart == 'excl' ? true : false;
		$this->display_cart_ex_tax   = $this->tax_display_cart == 'excl' ? true : false;

		add_action( 'init', array( $this, 'init' ), 5 ); // Get cart on init
	}


    /**
	 * Loads the cart data from the PHP session during WordPress init and hooks in other methods.
     *
     * @access public
     * @return void
     */
    public function init() {
		$this->get_cart_from_session();

		add_action('carton_check_cart_items', array( $this, 'check_cart_items' ), 1 );
		add_action('carton_check_cart_items', array( $this, 'check_cart_coupons' ), 1 );
		add_action('carton_after_checkout_validation', array( $this, 'check_customer_coupons' ), 1 );
    }

 	/*-----------------------------------------------------------------------------------*/
	/* Cart Session Handling */
	/*-----------------------------------------------------------------------------------*/

		/**
		 * Get the cart data from the PHP session and store it in class variables.
		 *
		 * @access public
		 * @return void
		 */
		public function get_cart_from_session() {
			global $carton;

			// Load the coupons
			$this->applied_coupons         = ( empty( $carton->session->coupon_codes ) ) ? array() : array_filter( (array) $carton->session->coupon_codes );
			$this->coupon_discount_amounts = ( empty( $carton->session->coupon_amounts ) ) ? array() : array_filter( (array) $carton->session->coupon_amounts );

			// Load the cart
			//unset($carton->session->cart);
			if ( isset( $carton->session->cart ) && is_array( $carton->session->cart ) ) {
				$cart = $carton->session->cart;

				foreach ( $cart as $key => $values ) {

					$_product = get_product( $values['variation_id'] ? $values['variation_id'] : $values['product_id'] );

					if ( ! empty( $_product ) && $_product->exists() && $values['quantity'] > 0 ) {

						// Put session data into array. Run through filter so other plugins can load their own session data\
					    $this->cart_contents[ $key ] = apply_filters( 'carton_get_cart_item_from_session', array(
						    'product_id'	=> $values['product_id'],
						    'variation_id'	=> $values['variation_id'],
						    'variation' 	=> $values['variation'],
						    'quantity' 		=> $values['quantity'],
						    'data'			=> $_product
					    ), $values, $key );
					}
				}

				do_action( 'carton_cart_loaded_from_session', $this );

				if ( ! is_array( $this->cart_contents ) )
					$this->cart_contents = array();

			} else {
				$this->cart_contents = array();
			}

			// Cookie
			if ( sizeof( $this->cart_contents ) > 0 )
				$carton->cart_has_contents_cookie( true );
			else
				$carton->cart_has_contents_cookie( false );

			// Load totals
			$this->cart_contents_total 	= isset( $carton->session->cart_contents_total ) ? $carton->session->cart_contents_total : 0;
			$this->cart_contents_weight	= isset( $carton->session->cart_contents_weight ) ? $carton->session->cart_contents_weight : 0;
			$this->cart_contents_count 	= isset( $carton->session->cart_contents_count ) ? $carton->session->cart_contents_count : 0;
			$this->cart_contents_tax 	= isset( $carton->session->cart_contents_tax ) ? $carton->session->cart_contents_tax : 0;
			$this->total 			= isset( $carton->session->total ) ? $carton->session->total : 0;
			$this->subtotal 		= isset( $carton->session->subtotal ) ? $carton->session->subtotal : 0;
			$this->subtotal_ex_tax 		= isset( $carton->session->subtotal_ex_tax ) ? $carton->session->subtotal_ex_tax : 0;
			$this->tax_total 		= isset( $carton->session->tax_total ) ? $carton->session->tax_total : 0;
			$this->taxes 			= isset( $carton->session->taxes ) ? $carton->session->taxes : array();
			$this->shipping_taxes		= isset( $carton->session->shipping_taxes ) ? $carton->session->shipping_taxes : array();
			$this->discount_cart 		= isset( $carton->session->discount_cart ) ? $carton->session->discount_cart : 0;
			$this->discount_total 		= isset( $carton->session->discount_total ) ? $carton->session->discount_total : 0;
			$this->shipping_tax_total 	= isset( $carton->session->shipping_tax_total ) ? $carton->session->shipping_tax_total : 0;
			$this->shipping_label		= isset( $carton->session->shipping_label ) ? $carton->session->shipping_label : '';
			$this->shipping_total 		= isset( $carton->session->shipping_total ) ? $carton->session->shipping_total : 0;
			$this->shipping_total_real	= isset( $carton->session->shipping_total_real ) ? $carton->session->shipping_total_real : 0;
			$this->shipping_info 		= isset( $carton->session->shipping_info ) ? $carton->session->shipping_info : 0;
			$this->discount_totals 		= isset( $carton->session->discount_totals ) ? $carton->session->discount_totals : 0;
			$this->cached			= array();

			// Queue re-calc if subtotal is not set
			if ( ! $this->subtotal && sizeof( $this->cart_contents ) > 0 )
				$this->calculate_totals();

		}


		/**
		 * Sets the php session data for the cart and coupons.
		 *
		 * @access public
		 * @return void
		 */
		public function set_session() {
			global $carton;

			// Set cart and coupon session data
			$cart_session = array();

			if ( $this->cart_contents ) {
				foreach ( $this->cart_contents as $key => $values ) {

					$cart_session[ $key ] = $values;

					// Unset product object
					unset( $cart_session[ $key ]['data'] );
				}
			}

			$carton->session->cart           = $cart_session;
			$carton->session->coupon_codes   = $this->applied_coupons;
			$carton->session->coupon_amounts = $this->coupon_discount_amounts;

			// Store totals to avoid re-calc on page load
			$carton->session->cart_contents_total  = $this->cart_contents_total;
			$carton->session->cart_contents_weight = $this->cart_contents_weight;
			$carton->session->cart_contents_count  = $this->cart_contents_count;
			$carton->session->cart_contents_tax    = $this->cart_contents_tax;
			$carton->session->total                = $this->total;
			$carton->session->subtotal             = $this->subtotal;
			$carton->session->subtotal_ex_tax      = $this->subtotal_ex_tax;
			$carton->session->tax_total            = $this->tax_total;
			$carton->session->shipping_taxes       = $this->shipping_taxes;
			$carton->session->taxes                = $this->taxes;
			$carton->session->discount_cart        = $this->discount_cart;
			$carton->session->discount_total       = $this->discount_total;
			$carton->session->shipping_total       = $this->shipping_total;
			$carton->session->shipping_total_real  = $this->shipping_total_real;
			$carton->session->shipping_info        = $this->shipping_info;
			$carton->session->shipping_tax_total   = $this->shipping_tax_total;
			$carton->session->shipping_label       = $this->shipping_label;

			if ( get_current_user_id() )
				$this->persistent_cart_update();

			do_action( 'carton_cart_updated' );
		}

		/**
		 * Empties the cart and optionally the persistent cart too.
		 *
		 * @access public
		 * @param bool $clear_persistent_cart (default: true)
		 * @return void
		 */
		public function empty_cart( $clear_persistent_cart = true ) {
			global $carton;

			$this->cart_contents = array();
			$this->reset();

			unset( $carton->session->order_awaiting_payment, $carton->session->coupon_codes, $carton->session->coupon_amounts, $carton->session->cart );

			if ( $clear_persistent_cart && get_current_user_id() )
				$this->persistent_cart_destroy();

			do_action( 'carton_cart_emptied' );
		}

 	/*-----------------------------------------------------------------------------------*/
	/* Persistent cart handling */
	/*-----------------------------------------------------------------------------------*/

		/**
		 * Save the persistent cart when the cart is updated.
		 *
		 * @access public
		 * @return void
		 */
		public function persistent_cart_update() {
			global $carton;

			update_user_meta( get_current_user_id(), '_carton_persistent_cart', array(
				'cart' => $carton->session->cart,
			) );
		}


		/**
		 * Delete the persistent cart permanently.
		 *
		 * @access public
		 * @return void
		 */
		public function persistent_cart_destroy() {
			delete_user_meta( get_current_user_id(), '_carton_persistent_cart' );
		}

 	/*-----------------------------------------------------------------------------------*/
	/* Cart Data Functions */
	/*-----------------------------------------------------------------------------------*/

		/**
		 * Coupons enabled function. Filterable.
		 *
		 * @access public
		 * @return void
		 */
		public function coupons_enabled() {
			$coupons_enabled = get_option( 'carton_enable_coupons' ) == 'no' ? false : true;

			return apply_filters( 'carton_coupons_enabled', $coupons_enabled );
		}

		/**
		 * Get number of items in the cart.
		 *
		 * @access public
		 * @return int
		 */
		public function get_cart_contents_count() {
			return apply_filters( 'carton_cart_contents_count', $this->cart_contents_count );
		}


		/**
		 * Check all cart items for errors.
		 *
		 * @access public
		 * @return void
		 */
		public function check_cart_items() {
			global $carton;

			// Check item stock
			$result = $this->check_cart_item_stock();

			if (is_wp_error($result))
				$carton->add_error( $result->get_error_message() );
		}


		/**
		 * Check cart coupons for errors.
		 *
		 * @access public
		 * @return void
		 */
		public function check_cart_coupons() {
			global $carton;

			if ( ! empty( $this->applied_coupons ) ) {
				foreach ( $this->applied_coupons as $key => $code ) {
					$coupon = new CTN_Coupon( $code );

					if ( is_wp_error( $coupon->is_valid() ) ) {

						$coupon->add_coupon_message( CTN_Coupon::E_CTN_COUPON_INVALID_REMOVED );

						// Remove the coupon
						unset( $this->applied_coupons[ $key ] );

						$carton->session->coupon_codes   = $this->applied_coupons;
						$carton->session->refresh_totals     = true;
					}
				}
			}
		}

		/**
		 * Get cart items quantities - merged so we can do accurate stock checks on items across multiple lines.
		 *
		 * @access public
		 * @return array
		 */
		public function get_cart_item_quantities() {
			$quantities = array();

			foreach ( $this->get_cart() as $cart_item_key => $values ) {

				if ( $values['data']->managing_stock() ) {

					if ( $values['variation_id'] > 0 ) {

						if ( $values['data']->variation_has_stock ) {

							// Variation has stock levels defined so its handled individually
							$quantities[ $values['variation_id'] ] = isset( $quantities[ $values['variation_id'] ] ) ? $quantities[ $values['variation_id'] ] + $values['quantity'] : $values['quantity'];

						} else {

							// Variation has no stock levels defined so use parents
							$quantities[ $values['product_id'] ] = isset( $quantities[ $values['product_id'] ] ) ? $quantities[ $values['product_id'] ] + $values['quantity'] : $values['quantity'];

						}

					} else {

						$quantities[ $values['product_id'] ] = isset( $quantities[ $values['product_id'] ] ) ? $quantities[ $values['product_id'] ] + $values['quantity'] : $values['quantity'];

					}

				}

			}
			return $quantities;
		}

		/**
		 * Check for user coupons (now that we have billing email). If a coupon is invalid, add an error.
		 *
		 * @access public
		 * @param array $posted
		 */
		public function check_customer_coupons( $posted ) {
			global $carton;

			if ( ! empty( $this->applied_coupons ) ) {
				foreach ( $this->applied_coupons as $key => $code ) {
					$coupon = new CTN_Coupon( $code );

					if ( ! is_wp_error( $coupon->is_valid() ) && is_array( $coupon->customer_email ) && sizeof( $coupon->customer_email ) > 0 ) {

						$coupon->customer_email = array_map( 'sanitize_email', $coupon->customer_email );

						if ( is_user_logged_in() ) {
							$current_user = wp_get_current_user();
							$check_emails[] = $current_user->user_email;
						}
						$check_emails[] = $posted['billing_email'];

						$check_emails = array_map( 'sanitize_email', array_map( 'strtolower', $check_emails ) );

						if ( 0 == sizeof( array_intersect( $check_emails, $coupon->customer_email ) ) ) {
							$coupon->add_coupon_message( CTN_Coupon::E_CTN_COUPON_NOT_YOURS_REMOVED );

							// Remove the coupon
							unset( $this->applied_coupons[ $key ] );

							$carton->session->coupon_codes   = $this->applied_coupons;
							$carton->session->refresh_totals     = true;
						}
					}
				}
			}
		}

		/**
		 * Looks through the cart to check each item is in stock. If not, add an error.
		 *
		 * @access public
		 * @return bool
		 */
		public function check_cart_item_stock() {
			global $carton, $wpdb;

			$error = new WP_Error();

			$product_qty_in_cart = $this->get_cart_item_quantities();

			// First stock check loop
			foreach ( $this->get_cart() as $cart_item_key => $values ) {

				$_product = $values['data'];

				/**
				 * Check stock based on inventory
				 */
				if ( $_product->managing_stock() ) {

					/**
					 * Check the stock for this item individually
					 */
					if ( ! $_product->is_in_stock() || ! $_product->has_enough_stock( $values['quantity'] ) ) {
						$error->add( 'out-of-stock', sprintf(__( 'Sorry, we do not have enough "%s" in stock to fulfill your order (%s in stock). Please edit your cart and try again. We apologise for any inconvenience caused.', 'carton' ), $_product->get_title(), $_product->stock ) );
						return $error;
					}

					// For later on...
					$key     = '_product_id';
					$value   = $values['product_id'];
					$in_cart = $values['quantity'];

					/**
					 * Next check entire cart quantities
					 */
					if ( $values['variation_id'] && $_product->variation_has_stock && isset( $product_qty_in_cart[ $values['variation_id'] ] ) ) {

						$key     = '_variation_id';
						$value   = $values['variation_id'];
						$in_cart = $product_qty_in_cart[ $values['variation_id'] ];

						if ( ! $_product->has_enough_stock( $product_qty_in_cart[ $values['variation_id'] ] ) ) {
							$error->add( 'out-of-stock', sprintf(__( 'Sorry, we do not have enough "%s" in stock to fulfil your order (%s in stock). Please edit your cart and try again. We apologise for any inconvenience caused.', 'carton' ), $_product->get_title(), $_product->stock ) );
							return $error;
						}

					} elseif ( isset( $product_qty_in_cart[ $values['product_id'] ] ) ) {

						$in_cart = $product_qty_in_cart[ $values['product_id'] ];

						if ( ! $_product->has_enough_stock( $product_qty_in_cart[ $values['product_id'] ] ) ) {
							$error->add( 'out-of-stock', sprintf(__( 'Sorry, we do not have enough "%s" in stock to fulfil your order (%s in stock). Please edit your cart and try again. We apologise for any inconvenience caused.', 'carton' ), $_product->get_title(), $_product->stock ) );
							return $error;
						}

					}

					/**
					 * Finally consider any held stock, from pending orders
					 */
					if ( get_option( 'carton_hold_stock_minutes' ) > 0 && ! $_product->backorders_allowed() ) {

						$order_id = isset( $carton->session->order_awaiting_payment ) ? absint( $carton->session->order_awaiting_payment ) : 0;

						$held_stock = $wpdb->get_var( $wpdb->prepare( "
							SELECT SUM( order_item_meta.meta_value::numeric ) AS held_qty

							FROM {$wpdb->posts} AS posts

							LEFT JOIN {$wpdb->prefix}carton_order_items as order_items ON posts.\"ID\" = order_items.order_id
							LEFT JOIN {$wpdb->prefix}carton_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
							LEFT JOIN {$wpdb->prefix}carton_order_itemmeta as order_item_meta2 ON order_items.order_item_id = order_item_meta2.order_item_id
							LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.\"ID\"=rel.object_ID
							LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
							LEFT JOIN {$wpdb->terms} AS term USING( term_id )

							WHERE 	order_item_meta.meta_key   = '_qty'
							AND 	order_item_meta2.meta_key  = %s AND order_item_meta2.meta_value  = %d
							AND 	posts.post_type            = 'shop_order'
							AND 	posts.post_status          = 'publish'
							AND 	tax.taxonomy               = 'shop_order_status'
							AND	term.slug			       IN ('pending')
							AND	posts.\"ID\"                   != %d
						", $key, $value, $order_id ) );

						if ( $_product->stock < ( $held_stock + $in_cart ) ) {
							$error->add( 'out-of-stock', sprintf(__( 'Sorry, we do not have enough "%s" in stock to fulfil your order right now. Please try again in %d minutes or edit your cart and try again. We apologise for any inconvenience caused.', 'carton' ), $_product->get_title(), get_option( 'carton_hold_stock_minutes' ) ) );
							return $error;
						}
					}

				/**
				 * Check stock based on stock-status
				 */
				} else {
					if ( ! $_product->is_in_stock() ) {
						$error->add( 'out-of-stock', sprintf(__( 'Sorry, "%s" is not in stock. Please edit your cart and try again. We apologise for any inconvenience caused.', 'carton' ), $_product->get_title() ) );
						return $error;
					}
				}
			}

			return true;
		}

		/**
		 * Gets and formats a list of cart item data + variations for display on the frontend.
		 *
		 * @access public
		 * @param array $cart_item
		 * @param bool $flat (default: false)
		 * @return string
		 */
		public function get_item_data( $cart_item, $flat = false ) {
			global $carton;

			$return = '';
			$has_data = false;

			if ( ! $flat ) $return .= '<dl class="variation">';

			// Variation data
			if ( ! empty( $cart_item['data']->variation_id ) ) {
				$variation_list = array();
                
                if( isset( $this->cached['product'] ) ) {
                    if( isset( $this->cached['product'][ $cart_item['product_id'] ] ) ) {
                        if( isset( $this->cached['product'][ $cart_item['product_id'] ]['_product_attributes'] ) ) {
                            // nothing to do
                        } else {
                            $this->cached['product'][ $cart_item['product_id'] ]['_product_attributes'] = $cart_item['data']->get_attributes();
                        }
                    } else {
                        $this->cached['product'][ $cart_item['product_id'] ] = array(
                            '_product_attributes' => $cart_item['data']->get_attributes()
                        );
                    }
                } else {
                    $this->cached['product'] = array(
                        $cart_item['product_id'] => array(
                            '_product_attributes' => $cart_item['data']->get_attributes()
                        )
                    );
                }
                $attributes = $this->cached['product'][ $cart_item['product_id'] ]['_product_attributes'];

				foreach ( $attributes as $attribute ) {
                
					if ( ! ($attribute['is_variation'] or ($attribute['is_changeable'] and $attribute['is_visible'] ) ) )
                        continue;

                    $name = 'attribute_' . mb_strtolower ( urlencode( $attribute['name'] ), 'utf-8' );
                    if( ! isset( $cart_item['data']->variation_data[$name] ) )
                        continue;

                    $value = $cart_item['data']->variation_data[ $name ];

					// If this is a term slug, get the term's nice name
		            if ( taxonomy_exists( $attribute['name'] ) ) {
		            	$term = get_term_by( 'slug', $value, $attribute['name'] );
		            	if ( ! is_wp_error( $term ) && $term->name )
		            		$value = $term->name;

		            // If this is a custom option slug, get the options name
		            } else {
		            	$value = apply_filters( 'carton_variation_option_name', $value );
					}

					if ( $flat )
						$variation_list[] = $carton->attribute_label( $attribute['name'] ) . ': ' . $value;
					else
						$variation_list[] = '<dt>' . $carton->attribute_label( $attribute['name'] ) . ':</dt><dd>' . $value . '</dd>'; 

				}

				if ($flat)
					$return .= implode( ", \n", $variation_list );
				else
					$return .= implode( '', $variation_list );

				$has_data = true;            
            }

			// Other data - returned as array with name/value values
			$other_data = apply_filters( 'carton_get_item_data', array(), $cart_item );

			if ( $other_data && is_array( $other_data ) && sizeof( $other_data ) > 0 ) {

				$data_list = array();

				foreach ($other_data as $data ) {
					// Set hidden to true to not display meta on cart.
					if ( empty( $data['hidden'] ) ) {
						$display_value = !empty($data['display']) ? $data['display'] : $data['value'];

						if ($flat)
							$data_list[] = $data['name'].': '.$display_value;
						else
							$data_list[] = '<dt>'.$data['name'].':</dt><dd>'.$display_value.'</dd>';
					}
				}

				if ($flat)
					$return .= implode(', ', $data_list);
				else
					$return .= implode('', $data_list);

				$has_data = true;

			}

			if ( ! $flat )
				$return .= '</dl>';

			if ( $has_data )
				return $return;
		}

		/**
		 * Gets cross sells based on the items in the cart.
		 *
		 * @return array cross_sells (item ids)
		 */
		public function get_cross_sells() {
			$cross_sells = array();
			$in_cart = array();
			if ( sizeof( $this->cart_contents) > 0 ) {
				foreach ( $this->cart_contents as $cart_item_key => $values ) {
					if ( $values['quantity'] > 0 ) {
						$cross_sells = array_merge( $values['data']->get_cross_sells(), $cross_sells );
						$in_cart[] = $values['product_id'];
					}
				}
			}
			$cross_sells = array_diff( $cross_sells, $in_cart );
			return $cross_sells;
		}

		/**
		 * Gets the url to the cart page.
		 *
		 * @return string url to page
		 */
		public function get_cart_url() {
			$cart_page_id = carton_get_page_id('cart');
			if ( $cart_page_id ) return apply_filters( 'carton_get_cart_url', get_permalink( $cart_page_id ) );
		}

		/**
		 * Gets the url to the checkout page.
		 *
		 * @return string url to page
		 */
		public function get_checkout_url() {
			$checkout_page_id = carton_get_page_id('checkout');
			if ( $checkout_page_id ) {
				if ( is_ssl() )
					return str_replace( 'http:', 'https:', get_permalink($checkout_page_id) );
				else
					return apply_filters( 'carton_get_checkout_url', get_permalink($checkout_page_id) );
			}
		}

		/**
		 * Gets the url to remove an item from the cart.
		 *
		 * @return string url to page
		 */
		public function get_remove_url( $cart_item_key ) {
			global $carton;
			$cart_page_id = carton_get_page_id('cart');
			if ($cart_page_id)
				return apply_filters( 'carton_get_remove_url', $carton->nonce_url( 'cart', add_query_arg( 'remove_item', $cart_item_key, get_permalink($cart_page_id) ) ) );
		}

		/**
		 * Returns the contents of the cart in an array.
		 *
		 * @return array contents of the cart
		 */
		public function get_cart() {
			return array_filter( (array) $this->cart_contents );
		}

		/**
		 * Returns the cart and shipping taxes, merged.
		 *
		 * @return array merged taxes
		 */
		public function get_taxes() {
			$merged_taxes = array();

			// Merge
			foreach ( array_keys( $this->taxes + $this->shipping_taxes ) as $key ) {
				$merged_taxes[ $key ] = ( isset( $this->shipping_taxes[ $key ] ) ? $this->shipping_taxes[ $key ] : 0 ) + ( isset( $this->taxes[ $key ] ) ? $this->taxes[ $key ] : 0 );
			}

			return $merged_taxes;
		}

		/**
		 * Returns the cart and shipping taxes, merged & formatted.
		 *
		 * @return array merged taxes
		 */
		public function get_formatted_taxes() {

			$taxes = $this->get_taxes();

			foreach ( $taxes as $key => $tax )
				if ( is_numeric( $tax ) )
					$taxes[ $key ] = carton_price( $tax );

			return apply_filters( 'carton_cart_formatted_taxes', $taxes, $this );
		}

	/*-----------------------------------------------------------------------------------*/
	/* Add to cart handling */
	/*-----------------------------------------------------------------------------------*/

		/**
	     * Check if product is in the cart and return cart item key.
	     *
	     * Cart item key will be unique based on the item and its properties, such as variations.
	     *
	     * @param mixed id of product to find in the cart
	     * @return string cart item key
	     */
	    public function find_product_in_cart( $cart_id = false ) {
	        if ( $cart_id !== false )
	        	foreach ( $this->cart_contents as $cart_item_key => $cart_item )
	        		if ( $cart_item_key == $cart_id )
	        			return $cart_item_key;
	    }

		/**
	     * Generate a unique ID for the cart item being added.
	     *
	     * @param int $product_id - id of the product the key is being generated for
	     * @param int $variation_id of the product the key is being generated for
	     * @param array $variation data for the cart item
	     * @param array $cart_item_data other cart item data passed which affects this items uniqueness in the cart
	     * @return string cart item key
	     */
	    public function generate_cart_id( $product_id, $variation_id = '', $variation = '', $cart_item_data = array() ) {
        
            $sep = '_';

	        $id_parts = array( $product_id );

	        if ( $variation_id ) $id_parts[] = $variation_id;

	        if ( is_array( $variation ) ) {
	            $variation_key = '';
	            foreach ( $variation as $key => $value ) {
	                $variation_key .= trim( $key ) . trim( $value );
	            }
	            $id_parts[] = $variation_key;
	        }

	        if ( is_array( $cart_item_data ) && ! empty( $cart_item_data ) ) {
	            $cart_item_data_key = '';
	            foreach ( $cart_item_data as $key => $value ) {
	            	if ( is_array( $value ) || is_object( $value ) ) $value = http_build_query( $value );
	                $cart_item_data_key .= trim($key) . trim($value);
	            }
	            $id_parts[] = $cart_item_data_key;
	        }

            $cart_item_key = md5( implode( $sep, $id_parts ) );
            $idx = 1;

            if( isset( $this->cart_contents[ $cart_item_key . $sep . $idx ] ) ) {
                while( $idx < 1000 ) { // 1000 is max now
                    if ( ! isset( $this->cart_contents[ $cart_item_key . $sep . $idx ] ) )
                        break;
                    $idx++;
                }
            }
			return $cart_item_key . $sep . $idx;
	    }

		/**
		 * Add a product to the cart.
		 *
		 * @param string $product_id contains the id of the product to add to the cart
		 * @param string $quantity contains the quantity of the item to add
		 * @param int $variation_id
		 * @param array $variation attribute values
		 * @param array $cart_item_data extra cart item data we want to pass into the item
		 * @return bool
		 */
		public function add_to_cart( $product_id, $quantity = 1, $variation_id = '', $variation = '', $cart_item_data = array() ) {
			global $carton;
            
			if ( $quantity <= 0 ) return false;

			// Load cart item data - may be added by other plugins
			$cart_item_data = (array) apply_filters( 'carton_add_cart_item_data', $cart_item_data, $product_id, $variation_id );

			// Generate a ID based on product ID, variation ID, variation data, and other cart item data
			$cart_id = $this->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );

			// See if this product and its options is already in the cart
			$cart_item_key = $this->find_product_in_cart( $cart_id );

			$product_data = get_product( $variation_id ? $variation_id : $product_id );

			if ( ! $product_data )
				return false;

			// Force quantity to 1 if sold individually
			if ( $product_data->is_sold_individually() )
				$quantity = 1;

			// Check product is_purchasable
			if ( ! $product_data->is_purchasable() ) {
				$carton->add_error( sprintf( __( 'Sorry, &quot;%s&quot; cannot be purchased.', 'carton' ), $product_data->get_title() ) );
				return false;
			}

			// Stock check - only check if we're managing stock and backorders are not allowed
			if ( ! $product_data->is_in_stock() ) {

				$carton->add_error( sprintf( __( 'You cannot add &quot;%s&quot; to the cart because the product is out of stock.', 'carton' ), $product_data->get_title() ) );

				return false;
			} elseif ( ! $product_data->has_enough_stock( $quantity ) ) {

				$carton->add_error( sprintf(__( 'You cannot add that amount of &quot;%s&quot; to the cart because there is not enough stock (%s remaining).', 'carton' ), $product_data->get_title(), $product_data->get_stock_quantity() ));

				return false;

			}

			// Downloadable/virtual qty check
			if ( $product_data->is_sold_individually() ) {
				$in_cart_quantity = $cart_item_key ? $this->cart_contents[$cart_item_key]['quantity'] : 0;

				// If its greater than 0, its already in the cart
				if ( $in_cart_quantity > 0 ) {
					$carton->add_error( sprintf('<a href="%s" class="button">%s</a> %s', get_permalink(carton_get_page_id('cart')), __( 'View Cart &rarr;', 'carton' ), __( 'You already have this item in your cart.', 'carton' ) ) );
					return false;
				}
			}

			// Stock check - this time accounting for whats already in-cart
			$product_qty_in_cart = $this->get_cart_item_quantities();

			if ( $product_data->managing_stock() ) {

				// Variations
				if ( $variation_id && $product_data->variation_has_stock ) {

					if ( isset( $product_qty_in_cart[ $variation_id ] ) && ! $product_data->has_enough_stock( $product_qty_in_cart[ $variation_id ] + $quantity ) ) {
						$carton->add_error( sprintf(__( '<a href="%s" class="button">%s</a> You cannot add that amount to the cart &mdash; we have %s in stock and you already have %s in your cart.', 'carton' ), get_permalink(carton_get_page_id('cart')), __( 'View Cart &rarr;', 'carton' ), $product_data->get_stock_quantity(), $product_qty_in_cart[ $variation_id ] ));
						return false;
					}

				// Products
				} else {
					if ( isset( $product_qty_in_cart[ $product_id ] ) && ! $product_data->has_enough_stock( $product_qty_in_cart[ $product_id ] + $quantity ) ) {
						$carton->add_error( sprintf(__( '<a href="%s" class="button">%s</a> You cannot add that amount to the cart &mdash; we have %s in stock and you already have %s in your cart.', 'carton' ), get_permalink(carton_get_page_id('cart')), __( 'View Cart &rarr;', 'carton' ), $product_data->get_stock_quantity(), $product_qty_in_cart[ $product_id ] ));
						return false;
					}

				}

			}

			// If cart_item_key is set, the item is already in the cart
			if ( $cart_item_key ) {

				$new_quantity = $quantity + $this->cart_contents[$cart_item_key]['quantity'];

				$this->set_quantity( $cart_item_key, $new_quantity );

			} else {

				$cart_item_key = $cart_id;

				// Add item after merging with $cart_item_data - hook to allow plugins to modify cart item
				$this->cart_contents[$cart_item_key] = apply_filters( 'carton_add_cart_item', array_merge( $cart_item_data, array(
					'product_id'	=> $product_id,
					'variation_id'	=> $variation_id,
					'variation' 	=> $variation,
					'quantity' 		=> $quantity,
					'data'			=> $product_data
				) ), $cart_item_key );

			}

			do_action( 'carton_add_to_cart', $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data );

			$carton->cart_has_contents_cookie( true );

			$this->calculate_totals();

			return true;
		}

		/**
		 * Set the quantity for an item in the cart.
		 *
		 * @param   string	cart_item_key	contains the id of the cart item
		 * @param   string	quantity	contains the quantity of the item
		 */
		public function set_quantity( $cart_item_key, $quantity = 1 ) {

			if ( $quantity == 0 || $quantity < 0 ) {
				do_action( 'carton_before_cart_item_quantity_zero', $cart_item_key );
				unset( $this->cart_contents[$cart_item_key] );
			} else {
                $diff = $quantity - $this->cart_contents[$cart_item_key]['quantity'];
                if( $diff > 0 ) {
                    while( $diff-- > 0 ) {
                        $this->add_to_cart(
                            $this->cart_contents[$cart_item_key]['product_id'],
                            1,
                            $this->cart_contents[$cart_item_key]['variation_id'] ? $this->cart_contents[$cart_item_key]['variation_id'] : '',
                            $this->cart_contents[$cart_item_key]['variation'] ? $this->cart_contents[$cart_item_key]['variation'] : '',
                            $this->cart_contents[$cart_item_key]
                        );
                    }
                } else {
                    $this->cart_contents[$cart_item_key]['quantity'] = $quantity;
                }

				do_action( 'carton_after_cart_item_quantity_update', $cart_item_key, $quantity );
			}

			$this->calculate_totals();
		}

    /*-----------------------------------------------------------------------------------*/
	/* Cart Calculation Functions */
	/*-----------------------------------------------------------------------------------*/

		/**
		 * Reset cart totals and clear sessions.
		 *
		 * @access private
		 * @return void
		 */
		private function reset() {
			global $carton;

			$this->total = $this->cart_contents_total = $this->cart_contents_weight = $this->cart_contents_count = $this->cart_contents_tax = $this->tax_total = $this->shipping_tax_total = $this->subtotal = $this->subtotal_ex_tax = $this->discount_total = $this->discount_cart = $this->shipping_total = $this->shipping_total_real = $this->fee_total = 0;
			$this->shipping_taxes = $this->taxes = $this->coupon_discount_amounts = array();

			unset( $carton->session->cart_contents_total, $carton->session->cart_contents_weight, $carton->session->cart_contents_count, $carton->session->cart_contents_tax, $carton->session->total, $carton->session->subtotal, $carton->session->subtotal_ex_tax, $carton->session->tax_total, $carton->session->taxes, $carton->session->shipping_taxes, $carton->session->discount_cart, $carton->session->discount_total, $carton->session->shipping_total, $carton->session->shipping_total_real, $carton->session->shipping_tax_total, $carton->session->shipping_label );
		}

		/**
		 * Function to apply discounts to a product and get the discounted price (before tax is applied).
		 *
		 * @access public
		 * @param mixed $values
		 * @param mixed $price
		 * @param bool $add_totals (default: false)
		 * @return float price
		 */
		public function get_discounted_price( $values, $price, $add_totals = false ) {
			global $wpdb;

			if ( ! $price ) return $price;

			if ( ! empty( $this->applied_coupons ) ) {
				foreach ( $this->applied_coupons as $code ) {
					$coupon = new CTN_Coupon( $code );

					if ( $coupon->apply_before_tax() && $coupon->is_valid() ) {

						switch ( $coupon->type ) {

							case "fixed_product" :
							case "percent_product" :

								$this_item_is_discounted = false;

								$product_cats = wp_get_post_terms( $values['product_id'], 'product_cat', array("fields" => "ids") );
								$product_ids_on_sale = carton_get_product_ids_on_sale();

								// Specific products get the discount
								if ( sizeof( $coupon->product_ids ) > 0 ) {

									if ( in_array( $values['product_id'], $coupon->product_ids ) || in_array( $values['variation_id'], $coupon->product_ids ) || in_array( $values['data']->get_parent(), $coupon->product_ids ) )
										$this_item_is_discounted = true;

								// Category discounts
								} elseif ( sizeof($coupon->product_categories ) > 0 ) {

									if ( sizeof( array_intersect( $product_cats, $coupon->product_categories ) ) > 0 )
										$this_item_is_discounted = true;

								} else {

									// No product ids - all items discounted
									$this_item_is_discounted = true;

								}

								// Specific product ID's excluded from the discount
								if ( sizeof( $coupon->exclude_product_ids ) > 0 )
									if ( in_array( $values['product_id'], $coupon->exclude_product_ids ) || in_array( $values['variation_id'], $coupon->exclude_product_ids ) || in_array( $values['data']->get_parent(), $coupon->exclude_product_ids ) )
										$this_item_is_discounted = false;

								// Specific categories excluded from the discount
								if ( sizeof( $coupon->exclude_product_categories ) > 0 )
									if ( sizeof( array_intersect( $product_cats, $coupon->exclude_product_categories ) ) > 0 )
										$this_item_is_discounted = false;

								// Sale Items excluded from discount
								if ( $coupon->exclude_sale_items == 'yes' )
									if ( in_array( $values['product_id'], $product_ids_on_sale, true ) || in_array( $values['variation_id'], $product_ids_on_sale, true ) || in_array( $values['data']->get_parent(), $product_ids_on_sale, true ) )
										$this_item_is_discounted = false;

								// Apply filter
								$this_item_is_discounted = apply_filters( 'carton_item_is_discounted', $this_item_is_discounted, $values, $before_tax = true, $coupon );

								// Apply the discount
								if ( $this_item_is_discounted ) {
									if ( $coupon->type=='fixed_product' ) {

										if ( $price < $coupon->amount ) {
											$discount_amount = $price;
										} else {
											$discount_amount = $coupon->amount;
										}

										$price = $price - $coupon->amount;

										if ( $price < 0 ) $price = 0;

										if ( $add_totals ) {
											$this->discount_cart = $this->discount_cart + ( $discount_amount * $values['quantity'] );
											$this->increase_coupon_discount_amount( $code, $discount_amount * $values['quantity'] );
										}

									} elseif ( $coupon->type == 'percent_product' ) {

										$percent_discount = ( $values['data']->get_price() / 100 ) * $coupon->amount;

										if ( $add_totals ) {
											$this->discount_cart = $this->discount_cart + ( $percent_discount * $values['quantity'] );
											$this->increase_coupon_discount_amount( $code, $percent_discount * $values['quantity'] );
										}

										$price = $price - $percent_discount;

									}
								}

							break;

							case "fixed_cart" :

								/**
								 * This is the most complex discount - we need to divide the discount between rows based on their price in
								 * proportion to the subtotal. This is so rows with different tax rates get a fair discount, and so rows
								 * with no price (free) don't get discount too.
								 */

								// Get item discount by dividing item cost by subtotal to get a %
								if ( $this->subtotal_ex_tax )
									$discount_percent = ( $values['data']->get_price_excluding_tax() * $values['quantity'] ) / $this->subtotal_ex_tax;
								else
									$discount_percent = 0;

								// Use pence to help prevent rounding errors
								$coupon_amount_pence = $coupon->amount * 100;

								// Work out the discount for the row
								$item_discount = $coupon_amount_pence * $discount_percent;

								// Work out discount per item
								$item_discount = $item_discount / $values['quantity'];

								// Pence
								$price = $price * 100;

								// Check if discount is more than price
								if ( $price < $item_discount )
									$discount_amount = $price;
								else
									$discount_amount = $item_discount;

								// Take discount off of price (in pence)
								$price = $price - $discount_amount;

								// Back to pounds
								$price = $price / 100;

								// Cannot be below 0
								if ( $price < 0 )
									$price = 0;

								// Add coupon to discount total (once, since this is a fixed cart discount and we don't want rounding issues)
								if ( $add_totals ) {
									$this->discount_cart = $this->discount_cart + ( ( $discount_amount * $values['quantity'] ) / 100 );
									$this->increase_coupon_discount_amount( $code, ( $discount_amount * $values['quantity'] ) / 100 );
								}

							break;

							case "percent" :

								$percent_discount = round( ( $values['data']->get_price() / 100 ) * $coupon->amount, $this->dp );

								if ( $add_totals ) {
									$this->discount_cart = $this->discount_cart + ( $percent_discount * $values['quantity'] );
									$this->increase_coupon_discount_amount( $code, $percent_discount * $values['quantity'] );
								}

								$price = $price - $percent_discount;

							break;

						}
					}
				}
			}
            return apply_filters( 'carton_get_discounted_price', $price, $values, $this );
		}

		/**
		 * Function to apply product discounts after tax.
		 *
		 * @access public
		 * @param mixed $values
		 * @param mixed $price
		 */
		public function apply_product_discounts_after_tax( $values, $price ) {

			if ( ! empty( $this->applied_coupons) ) {
				foreach ( $this->applied_coupons as $code ) {
					$coupon = new CTN_Coupon( $code );

					do_action( 'carton_product_discount_after_tax_' . $coupon->type, $coupon, $values, $price );

					if ( ! $coupon->is_valid() ) continue;

					if ( $coupon->type != 'fixed_product' && $coupon->type != 'percent_product' ) continue;

					if ( ! $coupon->apply_before_tax() ) {

						$product_cats = wp_get_post_terms( $values['product_id'], 'product_cat', array("fields" => "ids") );
						$product_ids_on_sale = carton_get_product_ids_on_sale();

						$this_item_is_discounted = false;

						// Specific products get the discount
						if ( sizeof( $coupon->product_ids ) > 0 ) {

							if (in_array($values['product_id'], $coupon->product_ids) || in_array($values['variation_id'], $coupon->product_ids) || in_array($values['data']->get_parent(), $coupon->product_ids))
								$this_item_is_discounted = true;

						// Category discounts
						} elseif ( sizeof( $coupon->product_categories ) > 0 ) {

							if ( sizeof( array_intersect( $product_cats, $coupon->product_categories ) ) > 0 )
								$this_item_is_discounted = true;

						} else {

							// No product ids - all items discounted
							$this_item_is_discounted = true;

						}

						// Specific product ID's excluded from the discount
						if ( sizeof( $coupon->exclude_product_ids ) > 0 )
							if ( in_array( $values['product_id'], $coupon->exclude_product_ids ) || in_array( $values['variation_id'], $coupon->exclude_product_ids ) || in_array( $values['data']->get_parent(), $coupon->exclude_product_ids ) )
								$this_item_is_discounted = false;

						// Specific categories excluded from the discount
						if ( sizeof( $coupon->exclude_product_categories ) > 0 )
							if ( sizeof( array_intersect( $product_cats, $coupon->exclude_product_categories ) ) > 0 )
								$this_item_is_discounted = false;

						// Sale Items excluded from discount
						if ( $coupon->exclude_sale_items == 'yes' )
							if ( in_array( $values['product_id'], $product_ids_on_sale, true ) || in_array( $values['variation_id'], $product_ids_on_sale, true ) || in_array( $values['data']->get_parent(), $product_ids_on_sale, true ) )
								$this_item_is_discounted = false;

						// Apply filter
						$this_item_is_discounted = apply_filters( 'carton_item_is_discounted', $this_item_is_discounted, $values, $before_tax = false, $coupon );

						// Apply the discount
						if ( $this_item_is_discounted ) {
							if ( $coupon->type == 'fixed_product' ) {

								if ( $price < $coupon->amount )
									$discount_amount = $price;
								else
									$discount_amount = $coupon->amount;

								$this->discount_total = $this->discount_total + ( $discount_amount * $values['quantity'] );
								$this->increase_coupon_discount_amount( $code, $discount_amount * $values['quantity'] );

							} elseif ( $coupon->type == 'percent_product' ) {
								$this->discount_total = $this->discount_total + round( ( $price / 100 ) * $coupon->amount, $this->dp );
								$this->increase_coupon_discount_amount( $code, round( ( $price / 100 ) * $coupon->amount, $this->dp ) );
							}
						}
					}
				}
			}

		}

		/**
		 * Function to apply cart discounts after tax.
		 *
		 * @access public
		 */
		public function apply_cart_discounts_after_tax() {

			$pre_discount_total = number_format( $this->cart_contents_total + $this->tax_total + $this->shipping_tax_total + $this->shipping_total + $this->fee_total, $this->dp, '.', '' );

			if ( $this->applied_coupons ) {
				foreach ( $this->applied_coupons as $code ) {
					$coupon = new CTN_Coupon( $code );

					do_action( 'carton_cart_discount_after_tax_' . $coupon->type, $coupon );

					if ( ! $coupon->apply_before_tax() && $coupon->is_valid() ) {

						switch ( $coupon->type ) {

							case "fixed_cart" :

								if ( $coupon->amount > $pre_discount_total )
									$coupon->amount = $pre_discount_total;

								$pre_discount_total = $pre_discount_total - $coupon->amount;

								$this->discount_total = $this->discount_total + $coupon->amount;

								$this->increase_coupon_discount_amount( $code, $coupon->amount );

							break;

							case "percent" :

								$percent_discount = round( ( round( $this->cart_contents_total + $this->tax_total, $this->dp ) / 100 ) * $coupon->amount, $this->dp );

								if ( $coupon->amount > $percent_discount )
									$coupon->amount = $percent_discount;

								$pre_discount_total = $pre_discount_total - $percent_discount;

								$this->discount_total = $this->discount_total + $percent_discount;

								$this->increase_coupon_discount_amount( $code, $percent_discount );

							break;

						}

					}
				}
			}
		}

		/**
		 * Store how much discount each coupon grants.
		 *
		 * @access private
		 * @param mixed $code
		 * @param mixed $amount
		 * @return void
		 */
		private function increase_coupon_discount_amount( $code, $amount ) {
			if ( empty( $this->coupon_discount_amounts[ $code ] ) )
				$this->coupon_discount_amounts[ $code ] = 0;

			$this->coupon_discount_amounts[ $code ] += $amount;
		}

		/**
		 * Calculate totals for the items in the cart.
		 *
		 * @access public
		 */
		public function calculate_totals() {
			global $carton;

			$this->reset();
            
            // Discounts
            $this->apply_cart_discouts_rules();

			do_action( 'carton_before_calculate_totals', $this );

			// Get count of all items + weights + subtotal (we may need this for discounts)
			if ( sizeof( $this->cart_contents ) > 0 ) {
				foreach ( $this->cart_contents as $cart_item_key => $values ) {

					$_product = $values['data'];

					$this->cart_contents_weight = $this->cart_contents_weight + ( $_product->get_weight() * $values['quantity'] );
					$this->cart_contents_count 	= $this->cart_contents_count + $values['quantity'];

					// Base Price (inclusive of tax for now)
					$row_base_price 		= $_product->get_price() * $values['quantity'];
					$base_tax_rates 		= $this->tax->get_shop_base_rate( $_product->tax_class );
					$tax_amount				= 0;

					if ( $this->prices_include_tax ) {

						if ( $_product->is_taxable() ) {

							$tax_rates			 	= $this->tax->get_rates( $_product->get_tax_class() );

							// ADJUST BASE if tax rate is different (different region or modified tax class)
							if ( $tax_rates !== $base_tax_rates ) {
								$base_taxes     = $this->tax->calc_tax( $row_base_price, $base_tax_rates, true, true );
								$modded_taxes   = $this->tax->calc_tax( $row_base_price - array_sum( $base_taxes ), $tax_rates, false );
								$row_base_price = ( $row_base_price - array_sum( $base_taxes ) ) + array_sum( $modded_taxes );
							}

							$taxes      = $this->tax->calc_tax( $row_base_price, $tax_rates, true );
							$tax_amount = get_option('carton_tax_round_at_subtotal') == 'no' ? $this->tax->get_tax_total( $taxes ) : array_sum( $taxes );

						}

						// Sub total is based on base prices (without discounts)
						$this->subtotal        = $this->subtotal + $row_base_price;
						$this->subtotal_ex_tax = $this->subtotal_ex_tax + ( $row_base_price - $tax_amount);

					} else {

						if ( $_product->is_taxable() ) {
							$tax_rates  = $this->tax->get_rates( $_product->get_tax_class() );
							$taxes      = $this->tax->calc_tax( $row_base_price, $tax_rates, false );
							$tax_amount = get_option('carton_tax_round_at_subtotal') == 'no' ? $this->tax->get_tax_total( $taxes ) : array_sum( $taxes );
						}

						// Sub total is based on base prices (without discounts)
						$this->subtotal        = $this->subtotal + $row_base_price + $tax_amount;
						$this->subtotal_ex_tax = $this->subtotal_ex_tax + $row_base_price;

					}
				}
			}

			// Now calc the main totals, including discounts
			if ( $this->prices_include_tax ) {

				/**
				 * Calculate totals for items
				 */
				if ( sizeof($this->cart_contents) > 0 ) {

                foreach ($this->cart_contents as $cart_item_key => $values ) {

						/**
						 * Prices include tax
						 *
						 * To prevent rounding issues we need to work with the inclusive price where possible
						 * otherwise we'll see errors such as when working with a 9.99 inc price, 20% VAT which would
						 * be 8.325 leading to totals being 1p off
						 *
						 * Pre tax coupons come off the price the customer thinks they are paying - tax is calculated
						 * afterwards.
						 *
						 * e.g. $100 bike with $10 coupon = customer pays $90 and tax worked backwards from that
						 *
						 * Used this excellent article for reference:
						 *	http://developer.practicalecommerce.com/articles/1473-Coding-for-Tax-Calculations-Everything-You-Never-Wanted-to-Know-Part-2
						 */
						$_product = $values['data'];

						// Base Price (inclusive of tax for now)
						$base_price 			= $_product->get_price();

						// Base Price Adjustment
						if ( $_product->is_taxable() ) {

							// Get rates
							$tax_rates			 	= $this->tax->get_rates( $_product->get_tax_class() );

							/**
							 * ADJUST TAX - Calculations when customer is OUTSIDE the shop base country/state and prices INCLUDE tax
							 * 	OR
							 * ADJUST TAX - Calculations when a tax class is modified
							 */
							if ( ( $carton->customer->is_customer_outside_base() && ( defined('CARTON_CHECKOUT') || $carton->customer->has_calculated_shipping() ) ) || ( $_product->get_tax_class() !== $_product->tax_class ) ) {

								// Get tax rate for the store base, ensuring we use the unmodified tax_class for the product
								$base_tax_rates 		= $this->tax->get_shop_base_rate( $_product->tax_class );

								// Work out new price based on region
								$row_base_price 		= $base_price * $values['quantity'];
								$base_taxes				= $this->tax->calc_tax( $row_base_price, $base_tax_rates, true, true ); // Unrounded
								$taxes					= $this->tax->calc_tax( $row_base_price - array_sum($base_taxes), $tax_rates, false );

								// Tax amount
								$tax_amount				= array_sum( $taxes );

								// Line subtotal + tax
								$line_subtotal_tax 		= get_option('carton_tax_round_at_subtotal') == 'no' ? $this->tax->round( $tax_amount ) : $tax_amount;
								$line_subtotal			= $row_base_price - $this->tax->get_tax_total( $base_taxes );

								// Adjusted price
								$adjusted_price 		= ( $row_base_price - array_sum( $base_taxes ) + array_sum( $taxes ) ) / $values['quantity'];

								// Apply discounts
								$discounted_price 		= $this->get_discounted_price( $values, $adjusted_price, true );

								$discounted_taxes		= $this->tax->calc_tax( $discounted_price * $values['quantity'], $tax_rates, true );
								$discounted_tax_amount	= array_sum( $discounted_taxes ); // Sum taxes

							/**
							 * Regular tax calculation (customer inside base and the tax class is unmodified
							 */
							} else {

								// Base tax for line before discount - we will store this in the order data
								$tax_amount				= array_sum( $this->tax->calc_tax( $base_price * $values['quantity'], $tax_rates, true ) );

								// Line subtotal + tax
								$line_subtotal_tax 		= get_option('carton_tax_round_at_subtotal') == 'no' ? $this->tax->round( $tax_amount ) : $tax_amount;
								$line_subtotal			= ( $base_price * $values['quantity'] ) - $this->tax->round( $line_subtotal_tax );

								// Calc prices and tax (discounted)
								$discounted_price 		= $this->get_discounted_price( $values, $base_price, true );
								$discounted_taxes		= $this->tax->calc_tax( $discounted_price * $values['quantity'], $tax_rates, true );
								$discounted_tax_amount	= array_sum( $discounted_taxes ); // Sum taxes

							}

							// Tax rows - merge the totals we just got
							foreach ( array_keys( $this->taxes + $discounted_taxes ) as $key ) {
							    $this->taxes[ $key ] = ( isset( $discounted_taxes[ $key ] ) ? $discounted_taxes[ $key ] : 0 ) + ( isset( $this->taxes[ $key ] ) ? $this->taxes[ $key ] : 0 );
							}

						} else {

							// Discounted Price (price with any pre-tax discounts applied)
							$discounted_price 		= $this->get_discounted_price( $values, $base_price, true );
							$discounted_tax_amount 	= 0;
							$tax_amount 			= 0;
							$line_subtotal_tax		= 0;
							$line_subtotal			= $base_price * $values['quantity'];

						}

						// Line prices
						$line_tax 		= get_option('carton_tax_round_at_subtotal') == 'no' ? $this->tax->round( $discounted_tax_amount ) : $discounted_tax_amount;
						$line_total 	= ( $discounted_price * $values['quantity'] ) - $this->tax->round( $line_tax );

						// Add any product discounts (after tax)
						$this->apply_product_discounts_after_tax( $values, $line_total + $discounted_tax_amount );

						// Cart contents total is based on discounted prices and is used for the final total calculation
						$this->cart_contents_total 	= $this->cart_contents_total + $line_total;

						// Store costs + taxes for lines
						$this->cart_contents[ $cart_item_key ]['line_total'] 		= $line_total;
						$this->cart_contents[ $cart_item_key ]['line_tax'] 			= $line_tax;
						$this->cart_contents[ $cart_item_key ]['line_subtotal'] 	= $line_subtotal;
						$this->cart_contents[ $cart_item_key ]['line_subtotal_tax'] = $line_subtotal_tax;

					}
				}

			} else {

				if ( sizeof( $this->cart_contents ) > 0 ) {
					foreach ( $this->cart_contents as $cart_item_key => $values ) {

						/**
						 * Prices exclude tax
						 *
						 * This calculation is simpler - work with the base, untaxed price.
						 */
						$_product = $values['data'];

						// Base Price (i.e. no tax, regardless of region)
						$base_price 				= $_product->get_price();

						// Discounted Price (base price with any pre-tax discounts applied
						$discounted_price 			= $this->get_discounted_price( $values, $base_price, true );
                        if( isset( $values['line_total_discounted'] ) )
                            $discounted_price       = $this->get_discounted_price( $values, $values['line_total_discounted'], true );

						// Tax Amount (For the line, based on discounted, ex.tax price)
						if ( $_product->is_taxable() ) {

							// Get tax rates
							$tax_rates 				= $this->tax->get_rates( $_product->get_tax_class() );

							// Base tax for line before discount - we will store this in the order data
							$tax_amount				= array_sum( $this->tax->calc_tax( $base_price * $values['quantity'], $tax_rates, false ) );

							// Now calc product rates
							$discounted_taxes		= $this->tax->calc_tax( $discounted_price * $values['quantity'], $tax_rates, false );
							$discounted_tax_amount	= array_sum( $discounted_taxes );

							// Tax rows - merge the totals we just got
							foreach ( array_keys( $this->taxes + $discounted_taxes ) as $key ) {
							    $this->taxes[ $key ] = ( isset( $discounted_taxes[ $key ] ) ? $discounted_taxes[ $key ] : 0 ) + ( isset( $this->taxes[ $key ] ) ? $this->taxes[ $key ] : 0 );
							}

						} else {
							$discounted_tax_amount 	= 0;
							$tax_amount 			= 0;
						}

						// Line prices
						$line_subtotal_tax	= $tax_amount;
						$line_tax			= $discounted_tax_amount;
						$line_subtotal		= $base_price * $values['quantity'];
						$line_total 		= $discounted_price * $values['quantity'];

						// Add any product discounts (after tax)
						$this->apply_product_discounts_after_tax( $values, $line_total + $line_tax );

						// Cart contents total is based on discounted prices and is used for the final total calculation
						$this->cart_contents_total 	= $this->cart_contents_total + $line_total;

						// Store costs + taxes for lines
						$this->cart_contents[ $cart_item_key ]['line_total'] 		= $line_total;
						$this->cart_contents[ $cart_item_key ]['line_tax'] 			= $line_tax;
						$this->cart_contents[ $cart_item_key ]['line_subtotal'] 	= $line_subtotal;
						$this->cart_contents[ $cart_item_key ]['line_subtotal_tax'] = $line_subtotal_tax;
					}
				}
			}

			// Add fees
			foreach ( $this->get_fees() as $fee ) {
				$this->fee_total += $fee->amount;

				if ( $fee->taxable ) {
					// Get tax rates
					$tax_rates 				= $this->tax->get_rates( $fee->tax_class );
					$fee_taxes				= $this->tax->calc_tax( $fee->amount, $tax_rates, false );

					// Store
					$fee->tax 				= array_sum( $fee_taxes );

					// Tax rows - merge the totals we just got
					foreach ( array_keys( $this->taxes + $fee_taxes ) as $key ) {
					    $this->taxes[ $key ] = ( isset( $fee_taxes[ $key ] ) ? $fee_taxes[ $key ] : 0 ) + ( isset( $this->taxes[ $key ] ) ? $this->taxes[ $key ] : 0 );
					}
				}
			}

			// Set tax total to sum of all tax rows
			$this->tax_total = $this->tax->get_tax_total( $this->taxes );

			// VAT exemption done at this point - so all totals are correct before exemption
			if ( $carton->customer->is_vat_exempt() ) {
				$this->shipping_tax_total = $this->tax_total = 0;
				$this->taxes = $this->shipping_taxes = array();

				foreach ( $this->cart_contents as $cart_item_key => $item )
					$this->cart_contents[ $cart_item_key ]['line_subtotal_tax'] = $this->cart_contents[ $cart_item_key ]['line_tax'] = 0;
			}

			// Cart Discounts (after tax)
			$this->apply_cart_discounts_after_tax();

			// Only calculate the grand total + shipping if on the cart/checkout
			if ( is_checkout() || is_cart() || defined('CARTON_CHECKOUT') || defined('CARTON_CART') ) {

				// Cart Shipping
				$this->calculate_shipping();

				// VAT exemption for shipping
				if ( $carton->customer->is_vat_exempt() ) {
					$this->shipping_tax_total = 0;
					$this->shipping_taxes = array();
				}

				// Round cart/shipping tax rows
				$this->taxes = array_map( array( $this->tax, 'round' ), $this->taxes );
				$this->shipping_taxes = array_map( array( $this->tax, 'round' ), $this->shipping_taxes );

				// Allow plugins to hook and alter totals before final total is calculated
				do_action( 'carton_calculate_totals', $this );

				/**
				 * Grand Total
				 *
				 * Based on discounted product prices, discounted tax, shipping cost + tax, and any discounts to be added after tax (e.g. store credit)
				 */
				$this->total = apply_filters( 'carton_calculated_total', number_format( $this->cart_contents_total + $this->tax_total + $this->shipping_tax_total + $this->shipping_total - $this->discount_total + $this->fee_total, $this->dp, '.', '' ), $this );

				if ( $this->total < 0 )
					$this->total = 0;

			}

			$this->set_session();
		}

		/**
		 * looks at the totals to see if payment is actually required.
		 *
		 * @return bool
		 */
		public function needs_payment() {
			$needs_payment = ( $this->total > 0 ) ? true : false;
			return apply_filters( 'carton_cart_needs_payment', $needs_payment, $this );
		}

    /*-----------------------------------------------------------------------------------*/
	/* Shipping related functions */
	/*-----------------------------------------------------------------------------------*/

		/**
		 * Uses the shipping class to calculate shipping then gets the totals when its finished.
		 *
		 * @access public
		 * @return void
		 */
		public function calculate_shipping() {
			global $carton;

			if ( $this->needs_shipping() && $this->show_shipping() ) {
				$carton->shipping->calculate_shipping( $this->get_shipping_packages() );
			} else {
				$carton->shipping->reset_shipping();
			}

			// Get totals for the chosen shipping method
			$this->shipping_total 		= $carton->shipping->shipping_total;	// Shipping Total
			$this->shipping_total_real	= $carton->shipping->shipping_total_real;	// Shipping Real Total
			$this->shipping_info 		= $carton->shipping->shipping_info;	// Shipping Info
			$this->shipping_label 		= $carton->shipping->shipping_label;	// Shipping Label
			$this->shipping_taxes		= $carton->shipping->shipping_taxes;	// Shipping Taxes
			$this->shipping_tax_total 	= $this->tax->get_tax_total( $this->shipping_taxes );	// Shipping tax amount
		}

		/**
		 * Get packages to calculate shipping for.
		 *
		 * This lets us calculate costs for carts that are shipped to multiple locations.
		 *
		 * Shipping methods are responsible for looping through these packages.
		 *
		 * By default we pass the cart itself as a package - plugins can change this
		 * through the filter and break it up.
		 *
		 * @since 1.5.4
		 * @access public
		 * @return array of cart items
		 */
		public function get_shipping_packages() {
			global $carton;

			// Packages array for storing 'carts'
			$packages = array();

			$packages[0]['contents']                 = $this->get_cart();		// Items in the package
			$packages[0]['contents_cost']            = 0;						// Cost of items in the package, set below
			$packages[0]['applied_coupons']          = $this->applied_coupons; 	// Applied coupons - some, like free shipping, affect costs
			$packages[0]['destination']['country']   = $carton->customer->get_shipping_country();
			$packages[0]['destination']['state']     = $carton->customer->get_shipping_state();
			$packages[0]['destination']['postcode']  = $carton->customer->get_shipping_postcode();
			$packages[0]['destination']['city']      = $carton->customer->get_shipping_city();
			$packages[0]['destination']['address']   = $carton->customer->get_shipping_address();
			$packages[0]['destination']['address_2'] = $carton->customer->get_shipping_address_2();

			foreach ( $this->get_cart() as $item )
				if ( $item['data']->needs_shipping() )
					$packages[0]['contents_cost'] += $item['line_total'];

			return apply_filters( 'carton_cart_shipping_packages', $packages );
		}

		/**
		 * Looks through the cart to see if shipping is actually required.
		 *
		 * @return bool whether or not the cart needs shipping
		 */
		public function needs_shipping() {
			if ( get_option('carton_calc_shipping')=='no' ) return false;
			if ( ! is_array( $this->cart_contents ) ) return false;

			$needs_shipping = false;

			foreach ( $this->cart_contents as $cart_item_key => $values ) {
				$_product = $values['data'];
				if ( $_product->needs_shipping() ) {
					$needs_shipping = true;
				}
			}

			return apply_filters( 'carton_cart_needs_shipping', $needs_shipping );
		}

		/**
		 * Sees if the customer has entered enough data to calc the shipping yet.
		 *
		 * @return bool
		 */
		public function show_shipping() {
			global $carton;

			if ( get_option('carton_calc_shipping')=='no' ) return false;
			if ( ! is_array( $this->cart_contents ) ) return false;

			if ( get_option( 'carton_shipping_cost_requires_address' ) == 'yes' ) {
				if ( ! $carton->customer->has_calculated_shipping() ) {
					if ( ! $carton->customer->get_shipping_country() || ( ! $carton->customer->get_shipping_state() && ! $carton->customer->get_shipping_postcode() ) ) return false;
				}
			}

			$show_shipping = true;

			return apply_filters( 'carton_cart_ready_to_calc_shipping', $show_shipping );

		}

		/**
		 * Sees if we need a shipping address.
		 *
		 * @return bool
		 */
		public function ship_to_billing_address_only() {
			if ( get_option('carton_ship_to_billing_address_only') == 'yes' ) return true; else return false;
		}

		/**
		 * Gets the shipping total (after calculation).
		 *
		 * @return mixed price or string for the shipping total
		 */
		public function get_cart_shipping_total() {
			global $carton;

			if ( isset( $this->shipping_label ) ) {
				if ( $this->shipping_total > 0 ) {

					// Display varies depending on settings
					if ( $this->tax_display_cart == 'excl' ) {

						$return = carton_price( $this->shipping_total );

						if ( $this->shipping_tax_total > 0 && $this->prices_include_tax ) {
							$return .= ' <small>' . $carton->countries->ex_tax_or_vat() . '</small>';
						}

						return $return;

					} else {

						$return = carton_price( $this->shipping_total + $this->shipping_tax_total );

						if ( $this->shipping_tax_total > 0 && ! $this->prices_include_tax ) {
							$return .= ' <small>' . $carton->countries->inc_tax_or_vat() . '</small>';
						}

						return $return;

					}

				} else {
					return __( 'Free!', 'carton' );
				}
			}
		}

		/**
		 * Gets title of the chosen shipping method.
		 *
		 * @return string shipping method title
		 */
		public function get_cart_shipping_title() {
			if ( isset( $this->shipping_label ) ) {
				return __( 'via', 'carton' ) . ' ' . $this->shipping_label;
			}
			return false;
		}

    /*-----------------------------------------------------------------------------------*/
	/* Coupons/Discount related functions */
	/*-----------------------------------------------------------------------------------*/

		/**
		 * Returns whether or not a discount has been applied.
		 *
		 * @return bool
		 */
		public function has_discount( $code ) {
			if ( in_array( $code, $this->applied_coupons ) ) return true;
			return false;
		}

		/**
		 * Applies a coupon code passed to the method.
		 *
		 * @param string $coupon_code - The code to apply
		 * @return bool	True if the coupon is applied, false if it does not exist or cannot be applied
		 */
		public function add_discount( $coupon_code ) {
			global $carton;

			// Coupons are globally disabled
			if ( ! $carton->cart->coupons_enabled() )
				return false;

			$the_coupon = new CTN_Coupon( $coupon_code );

			if ( $the_coupon->id ) {

				// Check it can be used with cart
				if ( ! $the_coupon->is_valid() ) {
					$carton->add_error( $the_coupon->get_error_message() );
					return false;
				}

				// Check if applied
				if ( $carton->cart->has_discount( $coupon_code ) ) {
					$the_coupon->add_coupon_message( CTN_Coupon::E_CTN_COUPON_ALREADY_APPLIED );
					return false;
				}

				// If its individual use then remove other coupons
				if ( $the_coupon->individual_use == 'yes' ) {
					$this->applied_coupons = apply_filters( 'carton_apply_individual_use_coupon', array(), $the_coupon, $this->applied_coupons );
				}

				if ( $this->applied_coupons ) {
					foreach ( $this->applied_coupons as $code ) {

						$existing_coupon = new CTN_Coupon( $code );

						if ( $existing_coupon->individual_use == 'yes' && false === apply_filters( 'carton_apply_with_individual_use_coupon', false, $the_coupon, $existing_coupon, $this->applied_coupons ) ) {

							// Reject new coupon
							$existing_coupon->add_coupon_message( CTN_Coupon::E_CTN_COUPON_ALREADY_APPLIED_INDIV_USE_ONLY );

							return false;
						}
					}
				}

				$this->applied_coupons[] = $coupon_code;

				// Choose free shipping
				if ( $the_coupon->enable_free_shipping() ) {
					$carton->session->chosen_shipping_method = 'free_shipping';
				}

				$this->calculate_totals();

				$the_coupon->add_coupon_message( CTN_Coupon::CTN_COUPON_SUCCESS );

				do_action( 'carton_applied_coupon', $coupon_code );

				return true;

			} else {
				$the_coupon->add_coupon_message( CTN_Coupon::E_CTN_COUPON_NOT_EXIST );
				return false;
			}
			return false;
		}

		/**
		 * Gets the array of applied coupon codes.
		 *
		 * @return array of applied coupons
		 */
		public function get_applied_coupons() {
			return (array) $this->applied_coupons;
		}

		/**
		 * Remove coupons from the cart of a defined type. Type 1 is before tax, type 2 is after tax.
		 *
		 * @params int type - 0 for all, 1 for before tax, 2 for after tax
		 */
		public function remove_coupons( $type = 0 ) {
			global $carton;

			if ( 1 == $type ) {
				if ( $this->applied_coupons ) {
					foreach ( $this->applied_coupons as $index => $code ) {
						$coupon = new CTN_Coupon( $code );
						if ( $coupon->is_valid() && $coupon->apply_before_tax() ) unset( $this->applied_coupons[ $index ] );
					}
				}

				$carton->session->coupon_codes   = $this->applied_coupons;
			} elseif ( $type == 2 ) {
				if ( $this->applied_coupons ) {
					foreach ( $this->applied_coupons as $index => $code ) {
						$coupon = new CTN_Coupon( $code );
						if ( $coupon->is_valid() && ! $coupon->apply_before_tax() ) unset( $this->applied_coupons[ $index ] );
					}
				}

				$carton->session->coupon_codes   = $this->applied_coupons;
			} else {
				unset( $carton->session->coupon_codes, $carton->session->coupon_amounts );
				$this->applied_coupons = array();
			}
		}

 	/*-----------------------------------------------------------------------------------*/
	/* Fees API to add additonal costs to orders */
	/*-----------------------------------------------------------------------------------*/

		/**
		 * add_fee function.
		 *
		 * @access public
		 * @param mixed $name
		 * @param mixed $amount
		 * @param bool $taxable (default: false)
		 * @param string $tax_class (default: '')
		 * @return void
		 */
		public function add_fee( $name, $amount, $taxable = false, $tax_class = '' ) {

			if ( empty( $this->fees ) )
				$this->fees = array();

			$new_fee 			= new stdClass();
			$new_fee->id 		= sanitize_title( $name );
			$new_fee->name 		= esc_attr( $name );
			$new_fee->amount	= (float) esc_attr( $amount );
			$new_fee->tax_class	= $tax_class;
			$new_fee->taxable	= $taxable ? true : false;
			$new_fee->tax		= 0;

			$this->fees[] 		= $new_fee;
		}

		/**
		 * get_fees function.
		 *
		 * @access public
		 * @return void
		 */
		public function get_fees() {
			return (array) $this->fees;
		}

    /*-----------------------------------------------------------------------------------*/
	/* Get Formatted Totals */
	/*-----------------------------------------------------------------------------------*/

		/**
		 * Get the total of all order discounts (after tax discounts).
		 *
		 * @return float
		 */
		public function get_order_discount_total() {
			return $this->discount_total;
		}

		/**
		 * Get the total of all cart discounts (before tax discounts).
		 *
		 * @return float
		 */
		public function get_cart_discount_total() {
			return $this->discount_cart;
		}

		/**
		 * Gets the order total (after calculation).
		 *
		 * @return string formatted price
		 */
		public function get_total() {
			return apply_filters( 'carton_cart_total', carton_price( $this->total ) );
		}

		/**
		 * Gets the total excluding taxes.
		 *
		 * @return string formatted price
		 */
		public function get_total_ex_tax() {
			$total = $this->total - $this->tax_total - $this->shipping_tax_total;
			if ( $total < 0 ) $total = 0;
			return apply_filters( 'carton_cart_total_ex_tax', carton_price( $total ) );
		}

		/**
		 * Gets the cart contents total (after calculation).
		 *
		 * @return string formatted price
		 */
		public function get_cart_total() {
			if ( ! $this->prices_include_tax ) {
				$cart_contents_total = carton_price( $this->cart_contents_total );
			} else {
				$cart_contents_total = carton_price( $this->cart_contents_total + $this->tax_total );
			}

			return apply_filters( 'carton_cart_contents_total', $cart_contents_total );
		}

		/**
		 * Gets the sub total (after calculation).
		 *
		 * @params bool whether to include compound taxes
		 * @return string formatted price
		 */
		public function get_cart_subtotal( $compound = false ) {
			global $carton;
			// If the cart has compound tax, we want to show the subtotal as
			// cart + shipping + non-compound taxes (after discount)
			if ( $compound ) {
				$cart_subtotal = carton_price( $this->cart_contents_total + $this->shipping_total + $this->get_taxes_total( false ) - $this->get_cart_actions_discount_total() );

			// Otherwise we show cart items totals only (before discount)
			} else {

				// Display varies depending on settings
				if ( $this->tax_display_cart == 'excl' ) {

					$cart_subtotal = carton_price( $this->subtotal_ex_tax  - $this->get_cart_actions_discount_total() );

					if ( $this->tax_total > 0 && $this->prices_include_tax ) {
						$cart_subtotal .= ' <small>' . $carton->countries->ex_tax_or_vat() . '</small>';
					}

				} else {

					$cart_subtotal = carton_price( $this->subtotal  - $this->get_cart_actions_discount_total() );

					if ( $this->tax_total > 0 && !$this->prices_include_tax ) {
						$cart_subtotal .= ' <small>' . $carton->countries->inc_tax_or_vat() . '</small>';
					}

				}
			}

			return apply_filters( 'carton_cart_subtotal', $cart_subtotal, $compound, $this );
		}

		/**
		 * Get the product row subtotal.
		 *
		 * Gets the tax etc to avoid rounding issues.
		 *
		 * When on the checkout (review order), this will get the subtotal based on the customer's tax rate rather than the base rate
		 *
		 * @params object product
		 * @params int quantity
		 * @return string formatted price
		 */
		public function get_product_subtotal( $_product, $quantity ) {
			global $carton;

			$price 			= $_product->get_price();
			$taxable 		= $_product->is_taxable();
			$base_tax_rates		= $this->tax->get_shop_base_rate( $_product->tax_class );
			$tax_rates 		= $this->tax->get_rates( $_product->get_tax_class() ); // This will get the base rate unless we're on the checkout page

			// Taxable
			if ( $taxable ) {

				if ( $this->tax_display_cart == 'excl' ) {

					$row_price        = $_product->get_price_excluding_tax( $quantity );
					$product_subtotal = carton_price( $row_price );

					if ( $this->prices_include_tax && $this->tax_total > 0 )
						$product_subtotal .= ' <small class="tax_label">' . $carton->countries->ex_tax_or_vat() . '</small>';

				} else {

					$row_price        = $_product->get_price_including_tax( $quantity );
					$product_subtotal = carton_price( $row_price );

					if ( ! $this->prices_include_tax && $this->tax_total > 0 )
						$product_subtotal .= ' <small class="tax_label">' . $carton->countries->inc_tax_or_vat() . '</small>';

				}

			// Non-taxable
			} else {

				$row_price        = $price * $quantity;
				$product_subtotal = carton_price( $row_price );

			}
			return apply_filters( 'carton_cart_product_subtotal', $product_subtotal, $_product, $quantity, $this );
		}

		/**
		 * Gets the cart tax (after calculation).
		 *
		 * @return string formatted price
		 */
		public function get_cart_tax() {
			$return = false;
			$cart_total_tax = $this->tax_total + $this->shipping_tax_total;
			if ( $cart_total_tax > 0 ) $return = carton_price( $cart_total_tax );
			return apply_filters( 'carton_get_cart_tax', $return );
		}

		/**
		 * Get tax row amounts with or without compound taxes includes.
		 *
		 * @return float price
		 */
		public function get_taxes_total( $compound = true ) {
			$total = 0;
			foreach ( $this->taxes as $key => $tax ) {
				if ( ! $compound && $this->tax->is_compound( $key ) ) continue;
				$total += $tax;
			}
			foreach ( $this->shipping_taxes as $key => $tax ) {
				if ( ! $compound && $this->tax->is_compound( $key ) ) continue;
				$total += $tax;
			}
			return $total;
		}

		/**
		 * Gets the total (product) discount amount - these are applied before tax.
		 *
		 * @return mixed formatted price or false if there are none
		 */
		public function get_discounts_before_tax() {
			if ( $this->discount_cart ) {
				$discounts_before_tax = carton_price( $this->discount_cart );
			} else {
				$discounts_before_tax = false;
			}
			return apply_filters( 'carton_cart_discounts_before_tax', $discounts_before_tax, $this );
		}

		/**
		 * Gets the order discount amount - these are applied after tax.
		 *
		 * @return mixed formatted price or false if there are none
		 */
		public function get_discounts_after_tax() {
			if ( $this->discount_total ) {
				$discounts_after_tax = carton_price( $this->discount_total );
			} else {
				$discounts_after_tax = false;
			}
			return apply_filters( 'carton_cart_discounts_after_tax', $discounts_after_tax, $this );
		}

		/**
		 * Gets the total discount amount - both kinds.
		 *
		 * @return mixed formatted price or false if there are none
		 */
		public function get_total_discount() {
			if ( $this->discount_total || $this->discount_cart ) {
				$total_discount = carton_price( $this->discount_total + $this->discount_cart );
			} else {
				$total_discount = false;
			}
			return apply_filters( 'carton_cart_total_discount', $total_discount, $this );
		}
        
        public function get_cart_actions_discount_total() {
            $actions_discount_total = 0;
            foreach ( $this->discount_totals as $discount_name => $discount_value ) {
                $actions_discount_total += $this->discount_totals[ $discount_name ];
            }
            return $actions_discount_total;
        }
        
        public function get_cart_actions_discounts() {
            return apply_filters( 'carton_cart_actions_discounts', $this->discount_totals, $this );
        }
        
        public function apply_cart_discouts_rules() {
            global $wpdb;
		$cart_has_items = 0;
            
            if ( empty( $this->discount_totals ) )
				$this->discount_totals = array();
            
			foreach ( $this->cart_contents as $cart_item_key => $item ) {
				$cart_has_items++;
				break;
			}

			if ( $cart_has_items ) {
                // Now get discounts rules which may be apply at this moment (now)
                $shop_discounts = $wpdb->get_results( "SELECT * FROM (
                      SELECT \"ID\", post_excerpt, post_title, 
                        _text_to_time( time_from.meta_value) AS time_from,
                        _text_to_time( time_to.meta_value) AS time_to,
                        _text_to_date( date_from.meta_value) AS date_from,
                        _text_to_date( date_to.meta_value) AS date_to
                      FROM {$wpdb->posts} p, {$wpdb->postmeta} date_from, {$wpdb->postmeta} date_to, {$wpdb->postmeta} time_from, {$wpdb->postmeta} time_to
                      WHERE
                        p.post_type = 'shop_discount' AND p.post_status = 'publish' AND
                        date_from.meta_key = 'date_from' AND date_from.post_id = p.\"ID\" AND
                        date_to.meta_key   = 'date_to'   AND date_to.post_id   = p.\"ID\" AND
                        time_from.meta_key = 'time_from' AND time_from.post_id = p.\"ID\" AND
                        time_to.meta_key   = 'time_to'   AND time_to.post_id   = p.\"ID\"
                    ) AS discounts
                    WHERE
                      (time_from <= now()::time OR time_from IS NULL) AND
                      (time_to   >= now()::time OR time_to   IS NULL) AND
                      (date_from <= now()::date OR date_from IS NULL) AND
                      (date_to   >= now()::date OR date_to   IS NULL)"
                );
                $discounts = array();

                // Complete Discount Each Rule Options
                foreach ( $shop_discounts as $shop_discount ) {
                    
                    $shop_discount_meta = $wpdb->get_results( $wpdb->prepare( "SELECT * from {$wpdb->postmeta} WHERE post_id = %d", $shop_discount->ID) );
                    if ( sizeof($shop_discount_meta) > 0 ) {
                        $discounts[ $shop_discount->ID ] = array( 'shop_discount' => $shop_discount );
                        foreach ( $shop_discount_meta as $meta ) {
                            $discounts[ $shop_discount->ID ][ $meta->meta_key ] = maybe_unserialize( $meta->meta_value );
                        }
                    }
                  }

                //Now let's do the The Job
                foreach ( $discounts as $discount ) {
                    // All Products is candidats for discount
                    $_candidats = array();

                    foreach ( $this->cart_contents as $cart_item_key => $item ) {
                        $_candidat = $item;
						$_candidat['_item_key'] = $cart_item_key;
                        $_candidat['_product_on_sale'] = get_product( $item['variation_id'] ? $item['variation_id'] : $item['product_id'] )->is_on_sale();

						for ( $i=0; $i<$_candidat['quantity']; $i++ )
							$_candidats[] = $_candidat;
					}
                    $sizeof  = sizeof($_candidats);

					// Check for "exclude_sale_items" 
					if( $discount['exclude_sale_items'] == 'yes' ) {
                        $i = 0;
                        foreach ( $_candidats as  $_candidat ) {
                            if( $_candidats[$i]['_product_on_sale'] ) {
                                // It isn't candidat rule. Let's delete the candidat.
                                unset( $_candidats[$i] );
                            }
                            else{
                                //echo  "Yeah! " . $discount["shop_discount"]->ID . " is exclude_sale_items rule for " . $_candidats[$i]['product_id'] . "!<br/>" ;
                            }
                            $i++;
                        }
                    }

					// Check for product enum
					$product_ids = split(',', $discount['product_ids'] );
					if(  sizeof( $product_ids ) > 0 ) {
                        $i = 0;
                        foreach ( $_candidats as  $_candidat ) {
							if( ! in_array ( $_candidats[$i]['product_id'], $product_ids ) ) {
                                // It isn't candidat rule. Let's delete the candidat.
                                // unset( $_candidats[$i] );
							}
							else{
								//echo  "Yeah! " . $discount["shop_discount"]->ID . " is product_ids rule for " . $_candidats[$i]['product_id'] . "!<br/>" ;
							}
                            $i++;
						}
					}

					// Check for "product_categories"
                    if( sizeof( $discount['product_categories'] ) > 0 ) {
                        $i = 0;
                        foreach ( $_candidats as  $_candidat ) {
                            if( ! $wpdb->get_var(
								"SELECT DISTINCT 1
								FROM {$wpdb->term_relationships} r, {$wpdb->term_taxonomy} t
								WHERE
								  r.object_id=" . $_candidats[$i]['product_id'] . " AND
								  r.term_taxonomy_id = t.term_taxonomy_id AND
								  t.taxonomy = 'product_cat' AND
								  t.term_id IN (" . implode(',', $discount['product_categories']) . ")
							") ) {
                                // It isn't candidat rule. Let's delete the candidat.
                                unset( $_candidats[$i] );
                            }
							else{
								//echo  "Yeah! " . $discount["shop_discount"]->ID . " is product_categories rule for " . $_candidats[$i]['product_id'] . "!<br/>" ;
							}
                            $i++;
                        }
                    }

                    // Check for "product_categories_accompaining" is in cart
                    if( sizeof( $discount['product_categories_accompaining'] ) > 0 ) {
						$products_ids = array();
						foreach ( $_candidats as  $_candidat )
							$products_ids[] = $_candidat['product_id'];
			if( sizeof( $products_ids ) ) {
	                    if( ! $wpdb->get_var(
									"SELECT DISTINCT 1
									FROM {$wpdb->term_relationships} r, {$wpdb->term_taxonomy} t
									WHERE
									  r.object_id IN (". implode(',', $products_ids ) . ") AND
									  r.term_taxonomy_id = t.term_taxonomy_id AND
									  t.taxonomy = 'product_cat' AND
									  t.term_id IN (" . implode(',', $discount['product_categories_accompaining']) . ")
								") ) {
	                            // It isn't our rule. Let's check next one
	                        continue;
	                        }
			}
                        //echo  "Yeah! " . $discount["shop_discount"]->ID . " is product_categories_accompaining rule. Needed" . $_candidats[$i]['product_id'] . " in the cart!<br/>" ;
                    }
					
					// Check for "product_attributes" IT DOESNT IMPLEMENTED YET !!!
					if( sizeof( $discount['product_attributes'] ) > 0 ) {
                        $i = 0;
                        foreach ( $_candidats as  $_candidat ) {
                            if( 0 ) {
                                // It isn't candidat rule. Let's delete the candidat.
                                unset( $_candidats[$i] );
                            }
							else{
								//echo  "Yeah! " . $discount["shop_discount"]->ID . " is product_attributes rule for " . $_candidats[$i]['product_id'] . "!<br/>" ;
							}
                            $i++;
                        }
                    }

                    
                    // OK. We got the colection and processing now
                    $counter = 0;

                    // Aliases:
                    $real_size_candidats = 0;
                    for ( $i = 0; $i < $sizeof; $i++ ) {
                        if ( isset( $_candidats[$i] ) )
                            $real_size_candidats++;
                    }

                    $times       = ($discount['times_the_amount'] > 0) ? $discount['times_the_amount'] : ($real_size_candidats ? $real_size_candidats : 1);
                    $value       = $discount['discount_amount'];
                    $type        = $discount['discount_type'];
                    
                    if( $sizeof > 0 && $sizeof >= $times ) {
                        $cart_item_keys = array();
                        
                        for ( $i = 0; $i < $sizeof; $i++ ) {
                            if ( isset( $_candidats[$i] ) ) {

                            $cart_item_keys[] = $_candidats[$i]['_item_key'];
                                if( ( ($counter+1)%$times ) == 0 ) {
                                    foreach ( $cart_item_keys as $item_key ) {

                                        if ( isset ( $this->cart_contents[ $item_key ]['_processed_discount'] ) ) {
                                            if( in_array ( $discount["shop_discount"]->ID, $this->cart_contents[ $item_key ]['_processed_discount'] ) )
                                                continue; //Discoutt shall be not double
                                        } else {
                                            $this->cart_contents[ $item_key ]['_processed_discount'] = array();
                                        }

                                        if ( ! isset($this->cart_contents[ $item_key ]['line_total_discount']) )
                                            $this->cart_contents[ $item_key ]['line_total_discount'] = 0;

                                        if( isset( $this->cart_contents[ $item_key ]['line_total_discounted'] ) )
                                            $price = $this->cart_contents[ $item_key ]['line_total_discounted'];
                                        else
                                            $price = $this->cart_contents[ $item_key ]['data']->get_price();

                                        if ( ! isset($this->cart_contents[ $item_key ]['line_total_discounted']) )
                                            $this->cart_contents[ $item_key ]['line_total_discounted'] = $price;

                                        $discount_value = 0;

                                        switch ($type) {
                                            case 'percent_discout':
                                                if( $discount['for_shipping'] )
                                                    $discount_value = 0;
                                                else
                                                    $discount_value = (float) ( $price * ($value/100) );
                                                break;
                                            case 'total_discout':
                                                $discount_value = (float) ( $value / $times );
                                                break;
                                            case 'fixed_total':
                                                if( $discount['for_shipping'] )
                                                    $discount_value = ($discout['discount_amount'] - ( $value / $times ));
                                                else
                                                    $discount_value = (float) ($price - ( $value / $times ));
                                                break;

                                            case 'total_shipping_discout':
                                                $discount_value = (float) ( $value / $times );
                                                break;
                                            case 'fixed_shipping_total':
                                                $discount_value = (float) ($discout['discount_amount'] - ( $value / $times ));
                                                break;
                                        }
                                        // Correct Discount value ( We not need for negative price )
                                        if( $discount_value > $this->cart_contents[ $item_key ]['line_total_discounted'] )
                                            $discount_value = $this->cart_contents[ $item_key ]['line_total_discounted'];
                                        
                                        // Applying discount value
                                        if( $discount['for_shipping'] == 'yes' or ($type == 'total_shipping_discout' or $type == 'fixed_shipping_total') ) {
                                            $for_methods  = maybe_unserialize( $discount['shipping'] );
                                            foreach( $for_methods as $method ) {
                                                $this->cart_contents[ $item_key ]['line_shipping_discount'][ $method ] += $discount_value;
                                            }
                                        } else {
                                            $this->cart_contents[ $item_key ]['line_total_discounted'] -= $discount_value; //discounted price
                                            $this->cart_contents[ $item_key ]['line_total_discount']   += $discount_value;

                                            $discount_title = $discount["shop_discount"]->post_excerpt ? $discount["shop_discount"]->post_excerpt : $discount["shop_discount"]->post_title;                                            
                                            if( ! isset( $this->discount_totals[ $discount_title ] ) ) {
                                                $this->discount_totals[ $discount_title ] = $discount_value;
                                            } else {
                                                $this->discount_totals[ $discount_title ] += $discount_value;
                                            }
                                        }

                                        // Store Dicount ID 
                                        $this->cart_contents[ $item_key ]['_processed_discount'][] = $discount["shop_discount"]->ID;
                                    }
                                    $cart_item_keys = array();
                                }
                                $counter++;
                            }
                        }
                    }
                }
            }
        }
}
