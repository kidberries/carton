<?php
/**
 * Plugin Name: CartoN
 * Plugin URI: http://www.carton-ecommerce.com/carton/
 * Description: An e-commerce toolkit that helps you sell anything. Beautifully.
 * Version: 2.0.5
 * Author: CartonThemes
 * Author URI: http://carton-ecommerce.com
 * Requires at least: 3.5
 * Tested up to: 3.5
 *
 * Text Domain: carton
 * Domain Path: /i18n/languages/
 *
 * @package CartoN
 * @category Core
 * @author CartonThemes
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Carton' ) ) {

/**
 * Main CartoN Class
 *
 * Contains the main functions for CartoN, stores variables, and handles error messages
 *
 * @class Carton
 * @version	2.0.0
 * @since 1.4
 * @package	CartoN
 * @author CartonThemes
 */
class Carton {

	/**
	 * @var string
	 */
	public $version = '2.0.5';

	/**
	 * @var string
	 */
	public $plugin_url;

	/**
	 * @var string
	 */
	public $plugin_path;

	/**
	 * @var string
	 */
	public $template_url;

	/**
	 * @var array
	 */
	public $errors = array();

	/**
	 * @var array
	 */
	public $messages = array();

	/**
	 * @var CTN_Query
	 */
	public $query;

	/**
	 * @var CTN_Customer
	 */
	public $customer;

	/**
	 * @var CTN_Product_Factory
	 */
	public $product_factory;

	/**
	 * @var CTN_Cart
	 */
	public $cart;

	/**
	 * @var CTN_Countries
	 */
	public $countries;

	/**
	 * @var CTN_Email
	 */
	public $carton_email;

	/**
	 * @var CTN_Checkout
	 */
	public $checkout;

	/**
	 * @var CTN_Integrations
	 */
	public $integrations;

	/**
	 * @var array
	 */
	private $_body_classes = array();

	/**
	 * @var string
	 */
	private $_inline_js = '';


	/**
	 * CartoN Constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// Auto-load classes on demand
		spl_autoload_register( array( $this, 'autoload' ) );

		// Define version constant
		define( 'CARTON_VERSION', $this->version );

		// Installation
		register_activation_hook( __FILE__, array( $this, 'activate' ) );

		// Updates
		add_action( 'admin_init', array( $this, 'update' ), 5 );

		// Include required files
		$this->includes();

		// Init API
		$this->api = new CTN_API();

		// Hooks
		add_filter( 'carton_shipping_methods', array( $this, 'core_shipping' ) );
		add_filter( 'carton_payment_gateways', array( $this, 'core_gateways' ) );

		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'init', array( $this, 'include_template_functions' ), 25 );
		add_action( 'after_setup_theme', array( $this, 'compatibility' ) );

		// Loaded action
		do_action( 'carton_loaded' );
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 *
	 * @access public
	 * @param mixed $key
	 * @return mixed
	 */
	public function __get( $key ) {

		if ( 'payment_gateways' == $key ) {
			return $this->payment_gateways();
		}

		elseif ( 'shipping' == $key ) {
			return $this->shipping();
		}

		return false;
	}

	/**
	 * Auto-load WC classes on demand to reduce memory consumption.
	 *
	 * @access public
	 * @param mixed $class
	 * @return void
	 */
	public function autoload( $class ) {

		$class = strtolower( $class );

		if ( strpos( $class, 'ctn_gateway_' ) === 0 ) {

			$path = $this->plugin_path() . '/classes/gateways/' . trailingslashit( substr( str_replace( '_', '-', $class ), 11 ) );
			$file = 'class-' . str_replace( '_', '-', $class ) . '.php';

			if ( is_readable( $path . $file ) ) {
				include_once( $path . $file );
				return;
			}

		} elseif ( strpos( $class, 'ctn_shipping_' ) === 0 ) {

			$path = $this->plugin_path() . '/classes/shipping/' . trailingslashit( substr( str_replace( '_', '-', $class ), 12 ) );
			$file = 'class-' . str_replace( '_', '-', $class ) . '.php';

			if ( is_readable( $path . $file ) ) {
				include_once( $path . $file );
				return;
			}

		} elseif ( strpos( $class, 'ctn_shortcode_' ) === 0 ) {

			$path = $this->plugin_path() . '/classes/shortcodes/';
			$file = 'class-' . str_replace( '_', '-', $class ) . '.php';

			if ( is_readable( $path . $file ) ) {
				include_once( $path . $file );
				return;
			}
		}

		if ( strpos( $class, 'ctn_' ) === 0 ) {

			$path = $this->plugin_path() . '/classes/';
			$file = 'class-' . str_replace( '_', '-', $class ) . '.php';

			if ( is_readable( $path . $file ) ) {
				include_once( $path . $file );
				return;
			}
		}
	}


	/**
	 * activate function.
	 *
	 * @access public
	 * @return void
	 */
	public function activate() {
		if ( carton_get_page_id( 'shop' ) < 1 )
			update_option( '_ctn_needs_pages', 1 );
		$this->install();
	}

	/**
	 * update function.
	 *
	 * @access public
	 * @return void
	 */
	public function update() {
		if ( ! defined( 'IFRAME_REQUEST' ) && ( get_option( 'carton_version' ) != $this->version || get_option( 'carton_db_version' ) != $this->version ) )
			$this->install();
	}

	/**
	 * upgrade function.
	 *
	 * @access public
	 * @return void
	 */
	function install() {
		include_once( 'admin/carton-admin-install.php' );
		set_transient( '_ctn_activation_redirect', 1, 60 * 60 );
		do_install_carton();
	}


	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @access public
	 * @return void
	 */
	function includes() {
		if ( is_admin() )
			$this->admin_includes();
		if ( defined('DOING_AJAX') )
			$this->ajax_includes();
		if ( ! is_admin() || defined('DOING_AJAX') )
			$this->frontend_includes();

		// Functions
		include_once( 'carton-core-functions.php' );					// Contains core functions for the front/back end

		// API Class
		include_once( 'classes/class-wc-api.php' );

		// Include abstract classes
		include_once( 'classes/abstracts/abstract-wc-product.php' );			// Products
		include_once( 'classes/abstracts/abstract-wc-settings-api.php' );	// Settings API (for gateways, shipping, and integrations)
		include_once( 'classes/abstracts/abstract-wc-shipping-method.php' );	// A Shipping method
		include_once( 'classes/abstracts/abstract-wc-payment-gateway.php' ); // A Payment gateway
		include_once( 'classes/abstracts/abstract-wc-integration.php' );		// An integration with a service

		// Classes (used on all pages)
		include_once( 'classes/class-wc-product-factory.php' );				// Product factory
		include_once( 'classes/class-wc-countries.php' );					// Defines countries and states
		include_once( 'classes/class-wc-integrations.php' );					// Loads integrations

		// Include Core Integrations - these are included sitewide
		include_once( 'classes/integrations/google-analytics/class-wc-google-analytics.php' );
		include_once( 'classes/integrations/sharethis/class-wc-sharethis.php' );
		include_once( 'classes/integrations/shareyourcart/class-wc-shareyourcart.php' );
		include_once( 'classes/integrations/sharedaddy/class-wc-sharedaddy.php' );
	}


	/**
	 * Include required admin files.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_includes() {
		include_once( 'admin/carton-admin-init.php' );			// Admin section
	}


	/**
	 * Include required ajax files.
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_includes() {
		include_once( 'carton-ajax.php' );						// Ajax functions for admin and the front-end
	}


	/**
	 * Include required frontend files.
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_includes() {
		// Functions
		include_once( 'carton-hooks.php' );						// Template hooks used on the front-end
		include_once( 'carton-functions.php' );					// Contains functions for various front-end events

		// Classes
		include_once( 'classes/class-wc-query.php' );				// The main store queries
		include_once( 'classes/class-wc-cart.php' );					// The main cart class
		include_once( 'classes/class-wc-tax.php' );					// Tax class
		include_once( 'classes/class-wc-customer.php' ); 			// Customer class
		include_once( 'classes/abstracts/abstract-wc-session.php' ); // Abstract for session implementations
		include_once( 'classes/class-wc-session-handler.php' );   	// WC Session class
		include_once( 'classes/class-wc-shortcodes.php' );			// Shortcodes class
	}


	/**
	 * Function used to Init CartoN Template Functions - This makes them pluggable by plugins and themes.
	 *
	 * @access public
	 * @return void
	 */
	public function include_template_functions() {
		include_once( 'carton-template.php' );
	}


	/**
	 * core_gateways function.
	 *
	 * @access public
	 * @param mixed $methods
	 * @return void
	 */
	function core_gateways( $methods ) {
		$methods[] = 'CTN_Gateway_BACS';
		$methods[] = 'CTN_Gateway_Cheque';
		$methods[] = 'CTN_Gateway_COD';
		$methods[] = 'CTN_Gateway_Mijireh';
		$methods[] = 'CTN_Gateway_Paypal';
		return $methods;
	}


	/**
	 * core_shipping function.
	 *
	 * @access public
	 * @param mixed $methods
	 * @return void
	 */
	function core_shipping( $methods ) {
		$methods[] = 'CTN_Shipping_Flat_Rate';
		$methods[] = 'CTN_Shipping_Free_Shipping';
		$methods[] = 'CTN_Shipping_Free_Local_Pickup';
		$methods[] = 'CTN_Shipping_International_Delivery';
		$methods[] = 'CTN_Shipping_Local_Delivery';
		$methods[] = 'CTN_Shipping_Local_Pickup';
		$methods[] = 'CTN_Shipping_Russianpost';

		return $methods;
	}


	/**
	 * register_widgets function.
	 *
	 * @access public
	 * @return void
	 */
	function register_widgets() {
		// Include - no need to use autoload as WP loads them anyway
		include_once( 'classes/widgets/class-wc-widget-cart.php' );
		include_once( 'classes/widgets/class-wc-widget-featured-products.php' );
		include_once( 'classes/widgets/class-wc-widget-layered-nav.php' );
		include_once( 'classes/widgets/class-wc-widget-layered-nav-filters.php' );
		include_once( 'classes/widgets/class-wc-widget-price-filter.php' );
		include_once( 'classes/widgets/class-wc-widget-product-categories.php' );
		include_once( 'classes/widgets/class-wc-widget-product-search.php' );
		include_once( 'classes/widgets/class-wc-widget-product-tag-cloud.php' );
		include_once( 'classes/widgets/class-wc-widget-recent-products.php' );
		include_once( 'classes/widgets/class-wc-widget-top-rated-products.php' );
		include_once( 'classes/widgets/class-wc-widget-recent-reviews.php' );
		include_once( 'classes/widgets/class-wc-widget-recently-viewed.php' );
		include_once( 'classes/widgets/class-wc-widget-best-sellers.php' );
		include_once( 'classes/widgets/class-wc-widget-onsale.php' );
		include_once( 'classes/widgets/class-wc-widget-random-products.php' );
//		include_once( 'classes/widgets/class-wc-widget-upsale-products.php' );

		// Register widgets
		register_widget( 'CTN_Widget_Recent_Products' );
		register_widget( 'CTN_Widget_Featured_Products' );
		register_widget( 'CTN_Widget_Product_Categories' );
		register_widget( 'CTN_Widget_Product_Tag_Cloud' );
		register_widget( 'CTN_Widget_Cart' );
		register_widget( 'CTN_Widget_Layered_Nav' );
		register_widget( 'CTN_Widget_Layered_Nav_Filters' );
		register_widget( 'CTN_Widget_Price_Filter' );
		register_widget( 'CTN_Widget_Product_Search' );
		register_widget( 'CTN_Widget_Top_Rated_Products' );
		register_widget( 'CTN_Widget_Recent_Reviews' );
		register_widget( 'CTN_Widget_Recently_Viewed' );
		register_widget( 'CTN_Widget_Best_Sellers' );
		register_widget( 'CTN_Widget_Onsale' );
		register_widget( 'CTN_Widget_Random_Products' );
//		register_widget( 'CTN_Widget_Upsale_Products' );
	}


	/**
	 * Init CartoN when WordPress Initialises.
	 *
	 * @access public
	 * @return void
	 */
	public function init() {
		//Before init action
		do_action( 'before_carton_init' );

		// Set up localisation
		$this->load_plugin_textdomain();

		// Variables
		$this->template_url			= apply_filters( 'carton_template_url', 'carton/' );

		// Load class instances
		$this->product_factory 		= new CTN_Product_Factory();     // Product Factory to create new product instances
		$this->countries 			= new CTN_Countries();			// Countries class
		$this->integrations			= new CTN_Integrations();		// Integrations class

		// Classes/actions loaded for the frontend and for ajax requests
		if ( ! is_admin() || defined('DOING_AJAX') ) {

			// Session class, handles session data for customers - can be overwritten if custom handler is needed
			$session_class = apply_filters( 'carton_session_handler', 'CTN_Session_Handler' );
			$this->session = new $session_class();

			// Class instances
			$this->cart 			= new CTN_Cart();				// Cart class, stores the cart contents
			$this->customer 		= new CTN_Customer();			// Customer class, handles data such as customer location
			$this->query			= new CTN_Query();				// Query class, handles front-end queries and loops
			$this->shortcodes		= new CTN_Shortcodes();			// Shortcodes class, controls all frontend shortcodes

			// Load messages
			$this->load_messages();

			// Hooks
			add_action( 'get_header', array( $this, 'init_checkout' ) );
			add_filter( 'template_include', array( $this, 'template_loader' ) );
			add_filter( 'comments_template', array( $this, 'comments_template_loader' ) );
			add_filter( 'wp_redirect', array( $this, 'redirect' ), 1, 2 );
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
			add_action( 'wp_print_scripts', array( $this, 'check_jquery' ), 25 );
			add_action( 'wp_head', array( $this, 'generator' ) );
			add_action( 'wp_head', array( $this, 'wp_head' ) );
			add_filter( 'body_class', array( $this, 'output_body_class' ) );
			add_filter( 'post_class', array( $this, 'post_class' ), 20, 3 );
			add_action( 'wp_footer', array( $this, 'output_inline_js' ), 25 );

			// HTTPS urls with SSL on
			$filters = array( 'post_thumbnail_html', 'widget_text', 'wp_get_attachment_url', 'wp_get_attachment_image_attributes', 'wp_get_attachment_url', 'option_siteurl', 'option_homeurl', 'option_home', 'option_url', 'option_wpurl', 'option_stylesheet_url', 'option_template_url', 'script_loader_src', 'style_loader_src', 'template_directory_uri', 'stylesheet_directory_uri', 'site_url' );

			foreach ( $filters as $filter )
				add_filter( $filter, array( $this, 'force_ssl' ) );
		}

		// Actions
		add_action( 'the_post', array( $this, 'setup_product_data' ) );
		add_action( 'admin_footer', array( $this, 'output_inline_js' ), 25 );

		// Email Actions
		$email_actions = array( 'carton_low_stock', 'carton_no_stock', 'carton_product_on_backorder', 'carton_order_status_pending_to_processing', 'carton_order_status_pending_to_completed', 'carton_order_status_pending_to_on-hold', 'carton_order_status_failed_to_processing', 'carton_order_status_failed_to_completed', 'carton_order_status_pending_to_processing', 'carton_order_status_pending_to_on-hold', 'carton_order_status_completed', 'carton_new_customer_note' );

		foreach ( $email_actions as $action )
			add_action( $action, array( $this, 'send_transactional_email') );

		// Register globals for WC environment
		$this->register_globals();

		// Init CartoN taxonomies
		$this->init_taxonomy();

		// Init Images sizes
		$this->init_image_sizes();

		// Init action
		do_action( 'carton_init' );
	}


	/**
	 * During checkout, ensure gateways and shipping classes are loaded so they can hook into the respective pages.
	 *
	 * @access public
	 * @return void
	 */
	public function init_checkout() {
		if ( is_checkout() || is_order_received_page() ) {
			$this->payment_gateways();
			$this->shipping();
		}
	}


	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 *
	 * @access public
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'carton' );
		$formal = 'yes' == get_option( 'carton_informal_localisation_type' ) ? 'informal' : 'formal';

		load_textdomain( 'carton', WP_LANG_DIR . "/carton/carton-$locale.mo" );

		// Load admin specific MO files
		if ( is_admin() ) {
			load_textdomain( 'carton', WP_LANG_DIR . "/carton/carton-admin-$locale.mo" );
			load_textdomain( 'carton', $this->plugin_path() . "/i18n/languages/carton-admin-$locale.mo" );
		}

		load_plugin_textdomain( 'carton', false, dirname( plugin_basename( __FILE__ ) ) . "/i18n/languages/$formal" );
		load_plugin_textdomain( 'carton', false, dirname( plugin_basename( __FILE__ ) ) . "/i18n/languages" );
	}


	/**
	 * Load a template.
	 *
	 * Handles template usage so that we can use our own templates instead of the themes.
	 *
	 * Templates are in the 'templates' folder. carton looks for theme
	 * overrides in /theme/carton/ by default
	 *
	 * For beginners, it also looks for a carton.php template first. If the user adds
	 * this to the theme (containing a carton() inside) this will be used for all
	 * carton templates.
	 *
	 * @access public
	 * @param mixed $template
	 * @return string
	 */
	public function template_loader( $template ) {

		$find = array( 'carton.php' );
		$file = '';

		if ( is_single() && get_post_type() == 'product' ) {

			$file 	= 'single-product.php';
			$find[] = $file;
			$find[] = $this->template_url . $file;

		} elseif ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) {

			$term = get_queried_object();

			$file 		= 'taxonomy-' . $term->taxonomy . '.php';
			$find[] 	= 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] 	= $this->template_url . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] 	= $file;
			$find[] 	= $this->template_url . $file;

		} elseif ( is_post_type_archive( 'product' ) || is_page( carton_get_page_id( 'shop' ) ) ) {

			$file 	= 'archive-product.php';
			$find[] = $file;
			$find[] = $this->template_url . $file;

		}

		if ( $file ) {
			$template = locate_template( $find );
			if ( ! $template ) $template = $this->plugin_path() . '/templates/' . $file;
		}

		return $template;
	}


	/**
	 * comments_template_loader function.
	 *
	 * @access public
	 * @param mixed $template
	 * @return string
	 */
	public function comments_template_loader( $template ) {
		if ( get_post_type() !== 'product' )
			return $template;

		if ( file_exists( STYLESHEETPATH . '/' . $this->template_url . 'single-product-reviews.php' ))
			return STYLESHEETPATH . '/' . $this->template_url . 'single-product-reviews.php';
		else
			return $this->plugin_path() . '/templates/single-product-reviews.php';
	}


	/**
	 * Register WC environment globals.
	 *
	 * @access public
	 * @return void
	 */
	public function register_globals() {
		$GLOBALS['product'] = null;
	}


	/**
	 * When the_post is called, get product data too.
	 *
	 * @access public
	 * @param mixed $post
	 * @return CTN_Product
	 */
	public function setup_product_data( $post ) {
		if ( is_int( $post ) ) $post = get_post( $post );
		if ( $post->post_type !== 'product' ) return;
		unset( $GLOBALS['product'] );
		$GLOBALS['product'] = get_product( $post );
		return $GLOBALS['product'];
	}


	/**
	 * Add Compatibility for various bits.
	 *
	 * @access public
	 * @return void
	 */
	public function compatibility() {
		// Post thumbnail support
		if ( ! current_theme_supports( 'post-thumbnails', 'product' ) ) {
			add_theme_support( 'post-thumbnails' );
			remove_post_type_support( 'post', 'thumbnail' );
			remove_post_type_support( 'page', 'thumbnail' );
		} else {
			add_post_type_support( 'product', 'thumbnail' );
		}

		// IIS
		if ( ! isset($_SERVER['REQUEST_URI'] ) ) {
			$_SERVER['REQUEST_URI'] = substr( $_SERVER['PHP_SELF'], 1 );
			if ( isset( $_SERVER['QUERY_STRING'] ) )
				$_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING'];
		}

		// NGINX Proxy
		if ( ! isset( $_SERVER['REMOTE_ADDR'] ) && isset( $_SERVER['HTTP_REMOTE_ADDR'] ) )
			$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_REMOTE_ADDR'];

		if ( ! isset( $_SERVER['HTTPS'] ) && ! empty( $_SERVER['HTTP_HTTPS'] ) )
			$_SERVER['HTTPS'] = $_SERVER['HTTP_HTTPS'];

		// Support for hosts which don't use HTTPS, and use HTTP_X_FORWARDED_PROTO
		if ( ! isset( $_SERVER['HTTPS'] ) && ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' )
			$_SERVER['HTTPS'] = '1';
	}


	/**
	 * Output generator to aid debugging.
	 *
	 * @access public
	 * @return void
	 */
	public function generator() {
		echo "\n\n" . '<!-- CartoN Version -->' . "\n" . '<meta name="generator" content="CartoN ' . esc_attr( $this->version ) . '" />' . "\n\n";
	}


	/**
	 * Add body classes.
	 *
	 * @access public
	 * @return void
	 */
	public function wp_head() {

		if ( is_carton() ) {
			$this->add_body_class( 'carton' );
			$this->add_body_class( 'carton-page' );
			return;
		}

		if ( is_checkout() || is_order_received_page() ) {
			$this->add_body_class( 'carton-checkout' );
			$this->add_body_class( 'carton-page' );
			return;
		}

		if ( is_cart() ) {
			$this->add_body_class( 'carton-cart' );
			$this->add_body_class( 'carton-page' );
			return;
		}

		if ( is_account_page() ) {
			$this->add_body_class( 'carton-account' );
			$this->add_body_class( 'carton-page' );
			return;
		}

	}


	/**
	 * Init CartoN taxonomies.
	 *
	 * @access public
	 * @return void
	 */
	public function init_taxonomy() {

		if ( post_type_exists('product') )
			return;

		/**
		 * Slugs
		 **/
		$permalinks 	= get_option( 'carton_permalinks' );
		$shop_page_id 	= carton_get_page_id( 'shop' );

		// Base slug is also used for the product post type archive
		$base_slug 		= $shop_page_id > 0 && get_page( $shop_page_id ) ? get_page_uri( $shop_page_id ) : 'shop';

		// Get bases
		$product_category_slug 	= empty( $permalinks['category_base'] ) ? _x( 'product-category', 'slug', 'carton' ) : $permalinks['category_base'];
		$product_tag_slug 		= empty( $permalinks['tag_base'] ) ? _x( 'product-tag', 'slug', 'carton' ) : $permalinks['tag_base'];
		$product_attribute_base	= empty( $permalinks['attribute_base'] ) ? '' : trailingslashit( $permalinks['attribute_base'] );
		$product_permalink 		= empty( $permalinks['product_base'] ) ? _x( 'product', 'slug', 'carton' ) : $permalinks['product_base'];

		if ( $product_permalink )
			$rewrite =  array( 'slug' => untrailingslashit( $product_permalink ), 'with_front' => false, 'feeds' => true );
		else
			$rewrite = false;

		$show_in_menu = current_user_can( 'manage_carton' ) ? 'carton' : true;

		/**
		 * Taxonomies
		 **/
		do_action( 'carton_register_taxonomy' );

		$admin_only_query_var = is_admin();

		register_taxonomy( 'product_type',
	        apply_filters( 'carton_taxonomy_objects_product_type', array('product') ),
	        apply_filters( 'carton_taxonomy_args_product_type', array(
	            'hierarchical' 			=> false,
	            'update_count_callback' => '_update_post_term_count',
	            'show_ui' 				=> false,
	            'show_in_nav_menus' 	=> false,
	            'query_var' 			=> $admin_only_query_var,
	            'rewrite'				=> false
	        ) )
	    );
		register_taxonomy( 'product_cat',
	        apply_filters( 'carton_taxonomy_objects_product_cat', array('product') ),
	        apply_filters( 'carton_taxonomy_args_product_cat', array(
	            'hierarchical' 			=> true,
	            'update_count_callback' => '_carton_term_recount',
	            'label' 				=> __( 'Product Categories', 'carton'),
	            'labels' => array(
	                    'name' 				=> __( 'Product Categories', 'carton'),
	                    'singular_name' 	=> __( 'Product Category', 'carton'),
						'menu_name'			=> _x( 'Categories', 'Admin menu name', 'carton' ),
	                    'search_items' 		=> __( 'Search Product Categories', 'carton'),
	                    'all_items' 		=> __( 'All Product Categories', 'carton'),
	                    'parent_item' 		=> __( 'Parent Product Category', 'carton'),
	                    'parent_item_colon' => __( 'Parent Product Category:', 'carton'),
	                    'edit_item' 		=> __( 'Edit Product Category', 'carton'),
	                    'update_item' 		=> __( 'Update Product Category', 'carton'),
	                    'add_new_item' 		=> __( 'Add New Product Category', 'carton'),
	                    'new_item_name' 	=> __( 'New Product Category Name', 'carton')
	            	),
	            'show_ui' 				=> true,
	            'query_var' 			=> true,
	            'capabilities'			=> array(
	            	'manage_terms' 		=> 'manage_product_terms',
					'edit_terms' 		=> 'edit_product_terms',
					'delete_terms' 		=> 'delete_product_terms',
					'assign_terms' 		=> 'assign_product_terms',
	            ),
	            'rewrite' 				=> array(
	            	'slug' => $product_category_slug,
	            	'with_front' => false,
	            	'hierarchical' => true,
	            	//'ep_mask' => EP_CATEGORIES
	            ),
	        ) )
	    );

	    register_taxonomy( 'product_tag',
	        apply_filters( 'carton_taxonomy_objects_product_tag', array('product') ),
	        apply_filters( 'carton_taxonomy_args_product_tag', array(
	            'hierarchical' 			=> false,
	            'update_count_callback' => '_carton_term_recount',
	            'label' 				=> __( 'Product Tags', 'carton'),
	            'labels' => array(
	                    'name' 				=> __( 'Product Tags', 'carton'),
	                    'singular_name' 	=> __( 'Product Tag', 'carton'),
						'menu_name'			=> _x( 'Tags', 'Admin menu name', 'carton' ),
	                    'search_items' 		=> __( 'Search Product Tags', 'carton'),
	                    'all_items' 		=> __( 'All Product Tags', 'carton'),
	                    'parent_item' 		=> __( 'Parent Product Tag', 'carton'),
	                    'parent_item_colon' => __( 'Parent Product Tag:', 'carton'),
	                    'edit_item' 		=> __( 'Edit Product Tag', 'carton'),
	                    'update_item' 		=> __( 'Update Product Tag', 'carton'),
	                    'add_new_item' 		=> __( 'Add New Product Tag', 'carton'),
	                    'new_item_name' 	=> __( 'New Product Tag Name', 'carton')
	            	),
	            'show_ui' 				=> true,
	            'query_var' 			=> true,
				'capabilities'			=> array(
					'manage_terms' 		=> 'manage_product_terms',
					'edit_terms' 		=> 'edit_product_terms',
					'delete_terms' 		=> 'delete_product_terms',
					'assign_terms' 		=> 'assign_product_terms',
				),
	            'rewrite' 				=> array( 'slug' => $product_tag_slug, 'with_front' => false ),
	        ) )
	    );

		register_taxonomy( 'product_shipping_class',
	        apply_filters( 'carton_taxonomy_objects_product_shipping_class', array('product', 'product_variation') ),
	        apply_filters( 'carton_taxonomy_args_product_shipping_class', array(
	            'hierarchical' 			=> true,
	            'update_count_callback' => '_update_post_term_count',
	            'label' 				=> __( 'Shipping Classes', 'carton'),
	            'labels' => array(
	                    'name' 				=> __( 'Shipping Classes', 'carton'),
	                    'singular_name' 	=> __( 'Shipping Class', 'carton'),
						'menu_name'			=> _x( 'Shipping Classes', 'Admin menu name', 'carton' ),
	                    'search_items' 		=> __( 'Search Shipping Classes', 'carton'),
	                    'all_items' 		=> __( 'All Shipping Classes', 'carton'),
	                    'parent_item' 		=> __( 'Parent Shipping Class', 'carton'),
	                    'parent_item_colon' => __( 'Parent Shipping Class:', 'carton'),
	                    'edit_item' 		=> __( 'Edit Shipping Class', 'carton'),
	                    'update_item' 		=> __( 'Update Shipping Class', 'carton'),
	                    'add_new_item' 		=> __( 'Add New Shipping Class', 'carton'),
	                    'new_item_name' 	=> __( 'New Shipping Class Name', 'carton')
	            	),
	            'show_ui' 				=> true,
	            'show_in_nav_menus' 	=> false,
	            'query_var' 			=> $admin_only_query_var,
				'capabilities'			=> array(
					'manage_terms' 		=> 'manage_product_terms',
					'edit_terms' 		=> 'edit_product_terms',
					'delete_terms' 		=> 'delete_product_terms',
					'assign_terms' 		=> 'assign_product_terms',
				),
	            'rewrite' 				=> false,
	        ) )
	    );

	    register_taxonomy( 'shop_order_status',
	        apply_filters( 'carton_taxonomy_objects_shop_order_status', array('shop_order') ),
	        apply_filters( 'carton_taxonomy_args_shop_order_status', array(
	            'hierarchical' 			=> false,
	            'update_count_callback' => '_update_post_term_count',
	            'show_ui' 				=> false,
	            'show_in_nav_menus' 	=> false,
	            'query_var' 			=> $admin_only_query_var,
	            'rewrite' 				=> false,
	        ) )
	    );

	    $attribute_taxonomies = $this->get_attribute_taxonomies();
		if ( $attribute_taxonomies ) {
			foreach ($attribute_taxonomies as $tax) {

		    	$name = $this->attribute_taxonomy_name( $tax->attribute_name );
		    	$hierarchical = true;
		    	if ($name) {

		    		$label = ( isset( $tax->attribute_label ) && $tax->attribute_label ) ? $tax->attribute_label : $tax->attribute_name;

					$show_in_nav_menus = apply_filters( 'carton_attribute_show_in_nav_menus', false, $name );

		    		register_taxonomy( $name,
				        apply_filters( 'carton_taxonomy_objects_' . $name, array('product') ),
				        apply_filters( 'carton_taxonomy_args_' . $name, array(
				            'hierarchical' 				=> $hierarchical,
	            			'update_count_callback' 	=> '_update_post_term_count',
				            'labels' => array(
				                    'name' 						=> $label,
				                    'singular_name' 			=> $label,
				                    'search_items' 				=> __( 'Search', 'carton') . ' ' . $label,
				                    'all_items' 				=> __( 'All', 'carton') . ' ' . $label,
				                    'parent_item' 				=> __( 'Parent', 'carton') . ' ' . $label,
				                    'parent_item_colon' 		=> __( 'Parent', 'carton') . ' ' . $label . ':',
				                    'edit_item' 				=> __( 'Edit', 'carton') . ' ' . $label,
				                    'update_item' 				=> __( 'Update', 'carton') . ' ' . $label,
				                    'add_new_item' 				=> __( 'Add New', 'carton') . ' ' . $label,
				                    'new_item_name' 			=> __( 'New', 'carton') . ' ' . $label
				            	),
				            'show_ui' 					=> false,
				            'query_var' 				=> true,
				            'capabilities'			=> array(
				            	'manage_terms' 		=> 'manage_product_terms',
								'edit_terms' 		=> 'edit_product_terms',
								'delete_terms' 		=> 'delete_product_terms',
								'assign_terms' 		=> 'assign_product_terms',
				            ),
				            'show_in_nav_menus' 		=> $show_in_nav_menus,
				            'rewrite' 					=> array( 'slug' => $product_attribute_base . sanitize_title( $tax->attribute_name ), 'with_front' => false, 'hierarchical' => $hierarchical ),
				        ) )
				    );

		    	}
		    }
		}

	    /**
		 * Post Types
		 **/
		do_action( 'carton_register_post_type' );

		register_post_type( "product",
			apply_filters( 'carton_register_post_type_product',
				array(
					'labels' => array(
							'name' 					=> __( 'Products', 'carton' ),
							'singular_name' 		=> __( 'Product', 'carton' ),
							'menu_name'				=> _x( 'Products', 'Admin menu name', 'carton' ),
							'add_new' 				=> __( 'Add Product', 'carton' ),
							'add_new_item' 			=> __( 'Add New Product', 'carton' ),
							'edit' 					=> __( 'Edit', 'carton' ),
							'edit_item' 			=> __( 'Edit Product', 'carton' ),
							'new_item' 				=> __( 'New Product', 'carton' ),
							'view' 					=> __( 'View Product', 'carton' ),
							'view_item' 			=> __( 'View Product', 'carton' ),
							'search_items' 			=> __( 'Search Products', 'carton' ),
							'not_found' 			=> __( 'No Products found', 'carton' ),
							'not_found_in_trash' 	=> __( 'No Products found in trash', 'carton' ),
							'parent' 				=> __( 'Parent Product', 'carton' )
						),
					'description' 			=> __( 'This is where you can add new products to your store.', 'carton' ),
					'public' 				=> true,
					'show_ui' 				=> true,
					'capability_type' 		=> 'product',
					'map_meta_cap'			=> true,
					'publicly_queryable' 	=> true,
					'exclude_from_search' 	=> false,
					'hierarchical' 			=> false, // Hierarchical causes memory issues - WP loads all records!
					'rewrite' 				=> $rewrite,
					'query_var' 			=> true,
					'supports' 				=> array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', 'page-attributes' ),
					'has_archive' 			=> $base_slug,
					'show_in_nav_menus' 	=> true
				)
			)
		);

		// Sort out attachment urls (removed, breaks pagination) no alternatives add_rewrite_rule( '^' . $attachment_base . '([^/]*)/([^/]*)/([^/]*)/?', 'index.php?attachment=$matches[3]', 'top' );

		register_post_type( "product_variation",
			apply_filters( 'carton_register_post_type_product_variation',
				array(
					'labels' => array(
							'name' 					=> __( 'Variations', 'carton' ),
							'singular_name' 		=> __( 'Variation', 'carton' ),
							'add_new' 				=> __( 'Add Variation', 'carton' ),
							'add_new_item' 			=> __( 'Add New Variation', 'carton' ),
							'edit' 					=> __( 'Edit', 'carton' ),
							'edit_item' 			=> __( 'Edit Variation', 'carton' ),
							'new_item' 				=> __( 'New Variation', 'carton' ),
							'view' 					=> __( 'View Variation', 'carton' ),
							'view_item' 			=> __( 'View Variation', 'carton' ),
							'search_items' 			=> __( 'Search Variations', 'carton' ),
							'not_found' 			=> __( 'No Variations found', 'carton' ),
							'not_found_in_trash' 	=> __( 'No Variations found in trash', 'carton' ),
							'parent' 				=> __( 'Parent Variation', 'carton' )
						),
					'public' 				=> true,
					'show_ui' 				=> false,
					'capability_type' 		=> 'product',
					'map_meta_cap'			=> true,
					'publicly_queryable' 	=> false,
					'exclude_from_search' 	=> true,
					'hierarchical' 			=> false,
					'rewrite' 				=> false,
					'query_var'				=> true,
					'supports' 				=> array( 'title', 'editor', 'custom-fields', 'page-attributes', 'thumbnail' ),
					'show_in_nav_menus' 	=> false
				)
			)
		);

		$menu_name = _x('Orders', 'Admin menu name', 'carton');
		if ( $order_count = carton_processing_order_count() ) {
			$menu_name .= " <span class='awaiting-mod update-plugins count-$order_count'><span class='processing-count'>" . number_format_i18n( $order_count ) . "</span></span>" ;
		}

	    register_post_type( "shop_order",
		    apply_filters( 'carton_register_post_type_shop_order',
				array(
					'labels' => array(
							'name' 					=> __( 'Orders', 'carton' ),
							'singular_name' 		=> __( 'Order', 'carton' ),
							'add_new' 				=> __( 'Add Order', 'carton' ),
							'add_new_item' 			=> __( 'Add New Order', 'carton' ),
							'edit' 					=> __( 'Edit', 'carton' ),
							'edit_item' 			=> __( 'Edit Order', 'carton' ),
							'new_item' 				=> __( 'New Order', 'carton' ),
							'view' 					=> __( 'View Order', 'carton' ),
							'view_item' 			=> __( 'View Order', 'carton' ),
							'search_items' 			=> __( 'Search Orders', 'carton' ),
							'not_found' 			=> __( 'No Orders found', 'carton' ),
							'not_found_in_trash' 	=> __( 'No Orders found in trash', 'carton' ),
							'parent' 				=> __( 'Parent Orders', 'carton' ),
							'menu_name'				=> $menu_name
						),
					'description' 			=> __( 'This is where store orders are stored.', 'carton' ),
					'public' 				=> true,
					'show_ui' 				=> true,
					'capability_type' 		=> 'shop_order',
					'map_meta_cap'			=> true,
					'publicly_queryable' 	=> false,
					'exclude_from_search' 	=> true,
					'show_in_menu' 			=> $show_in_menu,
					'hierarchical' 			=> false,
					'show_in_nav_menus' 	=> false,
					'rewrite' 				=> false,
					'query_var' 			=> true,
					'supports' 				=> array( 'title', 'comments', 'custom-fields' ),
					'has_archive' 			=> false,
				)
			)
		);

	    register_post_type( "shop_coupon",
		    apply_filters( 'carton_register_post_type_shop_coupon',
				array(
					'labels' => array(
							'name' 					=> __( 'Coupons', 'carton' ),
							'singular_name' 		=> __( 'Coupon', 'carton' ),
							'menu_name'				=> _x( 'Coupons', 'Admin menu name', 'carton' ),
							'add_new' 				=> __( 'Add Coupon', 'carton' ),
							'add_new_item' 			=> __( 'Add New Coupon', 'carton' ),
							'edit' 					=> __( 'Edit', 'carton' ),
							'edit_item' 			=> __( 'Edit Coupon', 'carton' ),
							'new_item' 				=> __( 'New Coupon', 'carton' ),
							'view' 					=> __( 'View Coupons', 'carton' ),
							'view_item' 			=> __( 'View Coupon', 'carton' ),
							'search_items' 			=> __( 'Search Coupons', 'carton' ),
							'not_found' 			=> __( 'No Coupons found', 'carton' ),
							'not_found_in_trash' 	=> __( 'No Coupons found in trash', 'carton' ),
							'parent' 				=> __( 'Parent Coupon', 'carton' )
						),
					'description' 			=> __( 'This is where you can add new coupons that customers can use in your store.', 'carton' ),
					'public' 				=> true,
					'show_ui' 				=> true,
					'capability_type' 		=> 'shop_coupon',
					'map_meta_cap'			=> true,
					'publicly_queryable' 	=> false,
					'exclude_from_search' 	=> true,
					'show_in_menu' 			=> $show_in_menu,
					'hierarchical' 			=> false,
					'rewrite' 				=> false,
					'query_var' 			=> false,
					'supports' 				=> array( 'title' ),
					'show_in_nav_menus'		=> false
				)
			)
		);
            
        register_post_type( "shop_discount",
		    apply_filters( 'carton_register_post_type_shop_discount',
				array(
					'labels' => array(
							'name' 					=> __( 'Discounts', 'carton' ),
							'singular_name' 		=> __( 'Discounts', 'carton' ),
							'menu_name'				=> _x( 'Discounts', 'Admin menu name', 'carton' ),
							'add_new' 				=> __( 'Add Discount', 'carton' ),
							'add_new_item' 			=> __( 'Add New Discount', 'carton' ),
							'edit' 					=> __( 'Edit', 'carton' ),
							'edit_item' 			=> __( 'Edit Discount', 'carton' ),
							'new_item' 				=> __( 'New Discount', 'carton' ),
							'view' 					=> __( 'View Discounts', 'carton' ),
							'view_item' 			=> __( 'View Disconts', 'carton' ),
							'search_items' 			=> __( 'Search Discounts', 'carton' ),
							'not_found' 			=> __( 'No Discounts found', 'carton' ),
							'not_found_in_trash' 	=> __( 'No Discounts found in trash', 'carton' ),
							'parent' 				=> __( 'Parent Discount', 'carton' )
						),
					'description' 			=> __( 'This is where you can add discounts that customers can use in your store.', 'carton' ),
					'public' 				=> true,
					'show_ui' 				=> true,
					'capability_type' 		=> 'shop_discount',
					'map_meta_cap'			=> true,
					'publicly_queryable' 	=> false,
					'exclude_from_search' 	=> true,
					'show_in_menu' 			=> $show_in_menu,
					'hierarchical' 			=> false,
					'rewrite' 				=> false,
					'query_var' 			=> false,
					'supports' 				=> array( 'title' ),
					'show_in_nav_menus'		=> false
				)
			)
		);
	}


	/**
	 * Init images.
	 *
	 * @access public
	 * @return void
	 */
	public function init_image_sizes() {
		$shop_thumbnail = $this->get_image_size( 'shop_thumbnail' );
		$shop_catalog	= $this->get_image_size( 'shop_catalog' );
		$shop_single	= $this->get_image_size( 'shop_single' );

		add_image_size( 'shop_thumbnail', $shop_thumbnail['width'], $shop_thumbnail['height'], $shop_thumbnail['crop'] );
		add_image_size( 'shop_catalog', $shop_catalog['width'], $shop_catalog['height'], $shop_catalog['crop'] );
		add_image_size( 'shop_single', $shop_single['width'], $shop_single['height'], $shop_single['crop'] );
	}


	/**
	 * Register/queue frontend scripts.
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_scripts() {
		global $post;

//		$suffix 				= defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$suffix 				= defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '';
		$lightbox_en 			= get_option( 'carton_enable_lightbox' ) == 'yes' ? true : false;
		$chosen_en 				= get_option( 'carton_enable_chosen' ) == 'yes' ? true : false;
		$ajax_cart_en			= get_option( 'carton_enable_ajax_add_to_cart' ) == 'yes' ? true : false;
		$frontend_script_path 	= $this->plugin_url() . '/assets/js/frontend/';

		// Register any scripts for later use, or used as dependencies
		wp_register_script( 'ajax-chosen', $this->plugin_url() . '/assets/js/chosen/ajax-chosen'.$suffix.'.js', array('jquery', 'chosen'), $this->version );
		wp_register_script( 'chosen', $this->plugin_url() . '/assets/js/chosen/chosen.jquery' . $suffix . '.js', array( 'jquery' ), $this->version, true );

		wp_register_script( 'jquery-blockui', $this->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'jquery-placeholder', $this->plugin_url() . '/assets/js/jquery-placeholder/jquery.placeholder' . $suffix . '.js', array( 'jquery' ), $this->version, true );

		wp_register_script( 'wc-add-to-cart-variation', $frontend_script_path . 'add-to-cart-variation' . $suffix . '.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'wc-single-product', $frontend_script_path . 'single-product' . $suffix . '.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'wc-single-product-shipping', $frontend_script_path . 'single-product-shipping' . $suffix . '.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'jquery-cookie', $this->plugin_url() . '/assets/js/jquery-cookie/jquery.cookie' . $suffix . '.js', array( 'jquery' ), '1.3.1', true );

		wp_enqueue_style( 'carton_custom_styles', $this->plugin_url() . '/assets/css/style.css' );

		// Queue frontend scripts conditionally
		if ( $ajax_cart_en )
			wp_enqueue_script( 'wc-add-to-cart', $frontend_script_path . 'add-to-cart' . $suffix . '.js', array( 'jquery' ), $this->version, true );

		if ( is_cart() ) {
			if ( $chosen_en ) {
                wp_enqueue_script( 'chosen' );
                wp_enqueue_script( 'ajax-chosen' );
				//wp_enqueue_script( 'wc-chosen', $frontend_script_path . 'chosen-frontend' . $suffix . '.js', array( 'chosen' ), $this->version, true );
				//wp_enqueue_script( 'wc-ajax-chosen', $frontend_script_path . 'ajax-chosen-frontend' . $suffix . '.js', array( 'ajax-chosen' ), $this->version, true );
				wp_enqueue_style( 'carton_chosen_styles', $this->plugin_url() . '/assets/css/chosen.css' );
			}

            wp_enqueue_script( 'wc-cart', $frontend_script_path . 'cart' . $suffix . '.js', array( 'jquery' ), $this->version, true );
        }

		if ( is_checkout() ) {
			if ( $chosen_en ) {
                wp_enqueue_script( 'chosen' );
                wp_enqueue_script( 'ajax-chosen' );
//				wp_enqueue_script( 'wc-chosen', $frontend_script_path . 'chosen-frontend' . $suffix . '.js', array( 'chosen' ), $this->version, true );
//				wp_enqueue_script( 'wc-ajax-chosen', $frontend_script_path . 'ajax-chosen-frontend' . $suffix . '.js', array( 'ajax-chosen' ), $this->version, true );
				wp_enqueue_style( 'carton_chosen_styles', $this->plugin_url() . '/assets/css/chosen.css' );
			}

			wp_enqueue_script( 'wc-checkout', $frontend_script_path . 'checkout' . $suffix . '.js', array( 'jquery', 'carton' ), $this->version, true );
		}

		if ( $lightbox_en && ( is_product() || ( ! empty( $post->post_content ) && strstr( $post->post_content, '[product_page' ) ) ) ) {
			wp_enqueue_script( 'prettyPhoto', $this->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( 'prettyPhoto-init', $this->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto.init' . $suffix . '.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_style( 'carton_prettyPhoto_css', $this->plugin_url() . '/assets/css/prettyPhoto.css' );
		}

		if ( is_product() ) {
			wp_enqueue_script( 'chosen' );
			wp_enqueue_script( 'ajax-chosen' );
			wp_enqueue_script( 'wc-single-product' );
			wp_enqueue_script( 'wc-single-product-shipping' );
			wp_enqueue_style( 'carton_chosen_styles', $this->plugin_url() . '/assets/css/chosen.css' );
		}

		// Global frontend scripts
		wp_enqueue_script( 'carton', $frontend_script_path . 'carton' . $suffix . '.js', array( 'jquery', 'jquery-blockui' ), $this->version, true );
		wp_enqueue_script( 'wc-cart-fragments', $frontend_script_path . 'cart-fragments' . $suffix . '.js', array( 'jquery', 'jquery-cookie' ), $this->version, true );
		wp_enqueue_script( 'jquery-placeholder' );

		// Variables for JS scripts
		$carton_params = array(
			'countries'                        => json_encode( $this->countries->get_allowed_country_states() ),
			'plugin_url'                       => $this->plugin_url(),
			'ajax_url'                         => $this->ajax_url(),
			'ajax_loader_url'                  => apply_filters( 'carton_ajax_loader_url', $this->plugin_url() . '/assets/images/ajax-loader@2x.gif' ),
			'i18n_select_state_text'           => esc_attr__( 'Select an option&hellip;', 'carton' ),
			'i18n_required_rating_text'        => esc_attr__( 'Please select a rating', 'carton' ),
			'i18n_no_matching_variations_text' => esc_attr__( 'Sorry, no products matched your selection. Please choose a different combination.', 'carton' ),
			'i18n_required_text'               => esc_attr__( 'required', 'carton' ),
			'i18n_view_cart'                   => esc_attr__( 'View Cart &rarr;', 'carton' ),
			'review_rating_required'           => get_option( 'carton_review_rating_required' ),
//			'update_order_review_nonce'        => wp_create_nonce( "update-order-review" ),
//			'apply_coupon_nonce'               => wp_create_nonce( "apply-coupon" ),
			'option_guest_checkout'            => get_option( 'carton_enable_guest_checkout' ),
			'checkout_url'                     => add_query_arg( 'action', 'carton-checkout', $this->ajax_url() ),
			'is_checkout'                      => is_page( carton_get_page_id( 'checkout' ) ) ? 1 : 0,
//			'update_shipping_method_nonce'     => wp_create_nonce( "update-shipping-method" ),
//			'add_to_cart_nonce'                => wp_create_nonce( "add-to-cart" ),
			'cart_url'                         => get_permalink( carton_get_page_id( 'cart' ) ),
			'cart_redirect_after_add'          => get_option( 'carton_cart_redirect_after_add' )
		);

		if ( is_checkout() || is_cart() )
			$carton_params['locale'] = json_encode( $this->countries->get_country_locale() );

		wp_localize_script( 'carton', 'carton_params', apply_filters( 'carton_params', $carton_params ) );

		// YES, it is dirty hack from KIDBERRIES.COM developers! =) It is for true chachin HTML pages.
		wp_localize_script( 'carton', "carton_params_ajax = jQuery(function(){jQuery.ajax({type:'POST',url:carton_params.ajax_url,data:{action:'get_carton_nonce_params'},async:false})}); carton_params_ajax", "undefined");

		// CSS Styles
		if ( ! defined( 'CARTON_USE_CSS' ) )
			define( 'CARTON_USE_CSS', get_option( 'carton_frontend_css' ) == 'yes' ? true : false );

		if ( CARTON_USE_CSS ) {
			$css = file_exists( get_stylesheet_directory() . '/carton/style.css' ) ? get_stylesheet_directory_uri() . '/carton/style.css' : $this->plugin_url() . '/assets/css/carton.css';
			wp_enqueue_style( 'carton_frontend_styles', $css );
		}
	}

	/**
	 * WC requires jQuery 1.7 since it uses functions like .on() for events.
	 * If, by the time wp_print_scrips is called, jQuery is outdated (i.e not
	 * using the version in core) we need to deregister it and register the
	 * core version of the file.
	 *
	 * @access public
	 * @return void
	 */
	public function check_jquery() {
		global $wp_scripts;

		// Enforce minimum version of jQuery
		if ( isset( $wp_scripts->registered['jquery']->ver ) && $wp_scripts->registered['jquery']->ver < '1.7' ) {
			wp_deregister_script( 'jquery' );
			wp_register_script( 'jquery', '/wp-includes/js/jquery/jquery.js', array(), '1.7' );
			wp_enqueue_script( 'jquery' );
		}
	}

	/** Load Instances on demand **********************************************/

	/**
	 * Get Checkout Class.
	 *
	 * @access public
	 * @return CTN_Checkout
	 */
	public function checkout() {
		if ( empty( $this->checkout ) )
			$this->checkout = new CTN_Checkout();

		return $this->checkout;
	}

	/**
	 * Get gateways class
	 *
	 * @access public
	 * @return CTN_Payment_Gateways
	 */
	public function payment_gateways() {
		if ( empty( $this->payment_gateways ) )
			$this->payment_gateways = new CTN_Payment_Gateways();

		return $this->payment_gateways;
	}

	/**
	 * Get shipping class
	 *
	 * @access public
	 * @return CTN_Shipping
	 */
	public function shipping() {
		if ( empty( $this->shipping ) )
			$this->shipping = new CTN_Shipping();

		return $this->shipping;
	}

	/**
	 * Get Logging Class.
	 *
	 * @access public
	 * @return CTN_Logger
	 */
	public function logger() {
		return new CTN_Logger();
	}

	/**
	 * Get Validation Class.
	 *
	 * @access public
	 * @return CTN_Validation
	 */
	public function validation() {
		return new CTN_Validation();
	}

	/**
	 * Init the mailer and call the notifications for the current filter.
	 *
	 * @access public
	 * @param array $args (default: array())
	 * @return void
	 */
	public function send_transactional_email( $args = array() ) {
		$this->mailer();
		do_action( current_filter() . '_notification', $args );
	}

	/**
	 * Email Class.
	 *
	 * @access public
	 * @return CTN_Email
	 */
	public function mailer() {
		if ( empty( $this->carton_email ) ) {
			$this->carton_email = new CTN_Emails();
		}
		return $this->carton_email;
	}

	/** Helper functions ******************************************************/

	/**
	 * Get the plugin url.
	 *
	 * @access public
	 * @return string
	 */
	public function plugin_url() {
		if ( $this->plugin_url ) return $this->plugin_url;
		return $this->plugin_url = plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) );
	}


	/**
	 * Get the plugin path.
	 *
	 * @access public
	 * @return string
	 */
	public function plugin_path() {
		if ( $this->plugin_path ) return $this->plugin_path;

		return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
	}


	/**
	 * Get Ajax URL.
	 *
	 * @access public
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}


	/**
	 * Return the WC API URL for a given request
	 *
	 * @access public
	 * @param mixed $request
	 * @param mixed $ssl (default: null)
	 * @return string
	 */
	public function api_request_url( $request, $ssl = null ) {
		if ( is_null( $ssl ) )
			$ssl = is_ssl();

		$url = trailingslashit( home_url( '/wc-api/' . $request ) );
		$url = $ssl ? str_replace( 'http:', 'https:', $url ) : str_replace( 'https:', 'http:', $url );

		return esc_url_raw( $url );
	}


	/**
	 * force_ssl function.
	 *
	 * @access public
	 * @param mixed $content
	 * @return void
	 */
	public function force_ssl( $content ) {
		if ( is_ssl() ) {
			if ( is_array($content) )
				$content = array_map( array( $this, 'force_ssl' ) , $content );
			else
				$content = str_replace( 'http:', 'https:', $content );
		}
		return $content;
	}


	/**
	 * Get an image size.
	 *
	 * Variable is filtered by carton_get_image_size_{image_size}
	 *
	 * @access public
	 * @param mixed $image_size
	 * @return string
	 */
	public function get_image_size( $image_size ) {

		// Only return sizes we define in settings
		if ( ! in_array( $image_size, array( 'shop_thumbnail', 'shop_catalog', 'shop_single' ) ) )
			return apply_filters( 'carton_get_image_size_' . $image_size, '' );

		$size = get_option( $image_size . '_image_size', array() );

		$size['width'] 	= isset( $size['width'] ) ? $size['width'] : '300';
		$size['height'] = isset( $size['height'] ) ? $size['height'] : '300';
		$size['crop'] 	= isset( $size['crop'] ) ? $size['crop'] : 1;

		return apply_filters( 'carton_get_image_size_' . $image_size, $size );
	}

	/** Messages ****************************************************************/

	/**
	 * Load Messages.
	 *
	 * @access public
	 * @return void
	 */
	public function load_messages() {
		$this->errors = $this->session->errors;
		$this->messages = $this->session->messages;
		unset( $this->session->errors, $this->session->messages );

		// Load errors from querystring
		if ( isset( $_GET['ctn_error'] ) )
			$this->add_error( esc_attr( $_GET['ctn_error'] ) );
	}


	/**
	 * Add an error.
	 *
	 * @access public
	 * @param string $error
	 * @return void
	 */
	public function add_error( $error ) {
		$this->errors[] = apply_filters( 'carton_add_error', $error );
	}


	/**
	 * Add a message.
	 *
	 * @access public
	 * @param string $message
	 * @return void
	 */
	public function add_message( $message ) {
		$this->messages[] = apply_filters( 'carton_add_message', $message );
	}


	/**
	 * Clear messages and errors from the session data.
	 *
	 * @access public
	 * @return void
	 */
	public function clear_messages() {
		$this->errors = $this->messages = array();
		unset( $this->session->errors, $this->session->messages );
	}


	/**
	 * error_count function.
	 *
	 * @access public
	 * @return int
	 */
	public function error_count() {
		return sizeof( $this->errors );
	}


	/**
	 * Get message count.
	 *
	 * @access public
	 * @return int
	 */
	public function message_count() {
		return sizeof( $this->messages );
	}


	/**
	 * Get errors.
	 *
	 * @access public
	 * @return array
	 */
	public function get_errors() {
		return (array) $this->errors;
	}


	/**
	 * Get messages.
	 *
	 * @access public
	 * @return array
	 */
	public function get_messages() {
		return (array) $this->messages;
	}


	/**
	 * Output the errors and messages.
	 *
	 * @access public
	 * @return void
	 */
	public function show_messages() {
		carton_show_messages();
	}


	/**
	 * Set session data for messages.
	 *
	 * @access public
	 * @return void
	 */
	public function set_messages() {
		$this->session->errors = $this->errors;
		$this->session->messages = $this->messages;
	}


	/**
	 * Redirection hook which stores messages into session data.
	 *
	 * @access public
	 * @param mixed $location
	 * @param mixed $status
	 * @return string
	 */
	public function redirect( $location, $status ) {
		$this->set_messages();

		return apply_filters( 'carton_redirect', $location );
	}

	/** Attribute Helpers ****************************************************************/

	/**
	 * Get attribute taxonomies.
	 *
	 * @access public
	 * @return object
	 */
	public function get_attribute_taxonomies() {

		$transient_name = 'ctn_attribute_taxonomies';

		if ( false === ( $attribute_taxonomies = get_transient( $transient_name ) ) ) {

			global $wpdb;

			$attribute_taxonomies = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "carton_attribute_taxonomies" );

			set_transient( $transient_name, $attribute_taxonomies );
		}

		return apply_filters( 'carton_attribute_taxonomies', $attribute_taxonomies );
	}


	/**
	 * Get a product attributes name.
	 *
	 * @access public
	 * @param mixed $name
	 * @return string
	 */
	public function attribute_taxonomy_name( $name ) {
		return 'pa_' . carton_sanitize_taxonomy_name( $name );
	}


	/**
	 * Get a product attributes label.
	 *
	 * @access public
	 * @param mixed $name
	 * @return string
	 */
	public function attribute_label( $name ) {
		global $wpdb;

		if ( strstr( $name, 'pa_' ) ) {
			$name = carton_sanitize_taxonomy_name( str_replace( 'pa_', '', $name ) );

			$label = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_label FROM " . $wpdb->prefix . "carton_attribute_taxonomies WHERE attribute_name = %s;", $name ) );

			if ( ! $label )
				$label = ucfirst( $name );
		} else {
			$label = $name;
		}

		return apply_filters( 'carton_attribute_label', $label, $name );
	}


	/**
	 * Get a product attributes orderby setting.
	 *
	 * @access public
	 * @param mixed $name
	 * @return string
	 */
	public function attribute_orderby( $name ) {
		global $wpdb;

		$name = str_replace( 'pa_', '', sanitize_title( $name ) );

		$orderby = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_orderby FROM " . $wpdb->prefix . "carton_attribute_taxonomies WHERE attribute_name = %s;", $name ) );

		return apply_filters( 'carton_attribute_orderby', $orderby, $name );
	}



	/**
	 * Get an array of product attribute taxonomies.
	 *
	 * @access public
	 * @return array
	 */
	public function get_attribute_taxonomy_names() {
		$taxonomy_names = array();
		$attribute_taxonomies = $this->get_attribute_taxonomies();
		if ( $attribute_taxonomies ) {
			foreach ( $attribute_taxonomies as $tax ) {
				$taxonomy_names[] = $this->attribute_taxonomy_name( $tax->attribute_name );
			}
		}
		return $taxonomy_names;
	}

	/** Coupon Helpers ********************************************************/

	/**
	 * Get coupon types.
	 *
	 * @access public
	 * @return array
	 */
	public function get_coupon_discount_types() {
		if ( ! isset( $this->coupon_discount_types ) ) {
			$this->coupon_discount_types = apply_filters( 'carton_coupon_discount_types', array(
    			'fixed_cart' 	=> __( 'Cart Discount', 'carton' ),
    			'percent' 		=> __( 'Cart % Discount', 'carton' ),
    			'fixed_product'	=> __( 'Product Discount', 'carton' ),
    			'percent_product'	=> __( 'Product % Discount', 'carton' )
    		) );
		}
		return $this->coupon_discount_types;
	}


	/**
	 * Get a coupon type's name.
	 *
	 * @access public
	 * @param string $type (default: '')
	 * @return string
	 */
	public function get_coupon_discount_type( $type = '' ) {
		$types = (array) $this->get_coupon_discount_types();
		if ( isset( $types[$type] ) ) return $types[$type];
	}

        /** Discount Helpers ********************************************************/

	/**
	 * Get discount types.
	 *
	 * @access public
	 * @return array
	 */
	public function get_discount_discount_types() {
		if ( ! isset( $this->discount_discount_types ) ) {
			$this->discount_discount_types = apply_filters(
				'carton_discount_discount_types',
				array(
					'percent_discout'	=> __( '% Discount', 'carton' ),
					'total_discout'		=> __( 'Total Discount', 'carton' ),
					'fixed_total'		=> __( 'Fixed Total', 'carton' ),
				)
			);
		}
		return $this->discount_discount_types;
	}


	/**
	 * Get a discount type's name.
	 *
	 * @access public
	 * @param string $type (default: '')
	 * @return string
	 */
	public function get_discount_discount_type( $type = '' ) {
		$types = (array) $this->get_discount_discount_types();
		if ( isset( $types[$type] ) ) return $types[$type];
	}


	/** Nonces ****************************************************************/

	/**
	 * Return a nonce field.
	 *
	 * @access public
	 * @param mixed $action
	 * @param bool $referer (default: true)
	 * @param bool $echo (default: true)
	 * @return void
	 */
	public function nonce_field( $action, $referer = true , $echo = true ) {
		return wp_nonce_field('carton-' . $action, '_n', $referer, $echo );
	}


	/**
	 * Return a url with a nonce appended.
	 *
	 * @access public
	 * @param mixed $action
	 * @param string $url (default: '')
	 * @return string
	 */
	public function nonce_url( $action, $url = '' ) {
		return add_query_arg( '_n', wp_create_nonce( 'carton-' . $action ), $url );
	}


	/**
	 * Check a nonce and sets carton error in case it is invalid.
	 *
	 * To fail silently, set the error_message to an empty string
	 *
	 * @access public
	 * @param string $name the nonce name
	 * @param string $action then nonce action
	 * @param string $method the http request method _POST, _GET or _REQUEST
	 * @param string $error_message custom error message, or false for default message, or an empty string to fail silently
	 * @return bool
	 */
	public function verify_nonce( $action, $method='_POST', $error_message = false ) {

		$name = '_n';
		$action = 'carton-' . $action;

		if ( $error_message === false ) $error_message = __( 'Action failed. Please refresh the page and retry.', 'carton' );

		if ( ! in_array( $method, array( '_GET', '_POST', '_REQUEST' ) ) ) $method = '_POST';

		if ( isset($_REQUEST[$name] ) && wp_verify_nonce( $_REQUEST[$name], $action ) ) return true;

		if ( $error_message ) $this->add_error( $error_message );

		return false;
	}

	/** Shortcode Helpers *********************************************************/

	/**
	 * Shortcode Wrapper
	 *
	 * @access public
	 * @param mixed $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public function shortcode_wrapper(
		$function,
		$atts = array(),
		$wrapper = array(
			'class' => 'carton',
			'before' => null,
			'after' => null
		)
	){
		ob_start();

		$before 	= empty( $wrapper['before'] ) ? '<div class="' . $wrapper['class'] . '">' : $wrapper['before'];
		$after 		= empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		echo $before;
		call_user_func( $function, $atts );
		echo $after;

		return ob_get_clean();
	}

	/** Cache Helpers *********************************************************/

	/**
	 * Sets a constant preventing some caching plugins from caching a page. Used on dynamic pages
	 *
	 * @access public
	 * @return void
	 */
	public function nocache() {
		if ( ! defined('DONOTCACHEPAGE') )
			define("DONOTCACHEPAGE", "true"); // WP Super Cache constant
	}


	/**
	 * Sets a cookie when the cart has something in it. Can be used by hosts to prevent caching if set.
	 *
	 * @access public
	 * @param mixed $set
	 * @return void
	 */
	public function cart_has_contents_cookie( $set ) {
		if ( ! headers_sent() ) {
			if ( $set ) {
				setcookie( "carton_items_in_cart", "1", 0, COOKIEPATH, COOKIE_DOMAIN, false );
				setcookie( "carton_cart_hash", md5( json_encode( $this->cart->get_cart() ) ), 0, COOKIEPATH, COOKIE_DOMAIN, false );
			} else {
				setcookie( "carton_items_in_cart", "0", time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false );
				setcookie( "carton_cart_hash", "0", time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false );
			}
		}
	}

	/**
	 * mfunc_wrapper function.
	 *
	 * Wraps a function in mfunc to keep it dynamic.
	 *
	 * If running WP Super Cache this checks for late_init (because functions calling this require WP to be loaded)
	 *
	 * @access public
	 * @param mixed $function
	 * @return void
	 */
	public function mfunc_wrapper( $mfunction, $function, $args ) {
		global $wp_super_cache_late_init;

		if ( is_null( $wp_super_cache_late_init ) || $wp_super_cache_late_init == 1 ) {
			echo '<!--mfunc ' . $mfunction . ' -->';
			$function( $args );
			echo '<!--/mfunc-->';
		} else {
			$function( $args );
		}
	}

	/** Transients ************************************************************/

	/**
	 * Clear all transients cache for product data.
	 *
	 * @access public
	 * @param int $post_id (default: 0)
	 * @return void
	 */
	public function clear_product_transients( $post_id = 0 ) {
		global $wpdb;

		$post_id = absint( $post_id );

		$wpdb->show_errors();

		// Clear core transients
		$transients_to_clear = array(
			'ctn_products_onsale',
			'ctn_hidden_product_ids',
			'ctn_hidden_product_ids_search',
			'ctn_attribute_taxonomies',
			'ctn_term_counts'
		);

		foreach( $transients_to_clear as $transient ) {
			delete_transient( 'ctn_products_onsale' );
			$wpdb->query( $wpdb->prepare( "DELETE FROM \"$wpdb->options\" WHERE \"option_name\" = %s OR \"option_name\" = %s", '_transient_' . $transient, '_transient_timeout_' . $transient ) );
		}

		// Clear transients for which we don't have the name
		$wpdb->query( "DELETE FROM \"$wpdb->options\" WHERE \"option_name\" LIKE ('_transient_ctn_uf_pid_%') OR \"option_name\" LIKE ('_transient_timeout_ctn_uf_pid_%')" );
		$wpdb->query( "DELETE FROM \"$wpdb->options\" WHERE \"option_name\" LIKE ('_transient_ctn_ln_count_%') OR \"option_name\" LIKE ('_transient_timeout_ctn_ln_count_%')" );
		$wpdb->query( "DELETE FROM \"$wpdb->options\" WHERE \"option_name\" LIKE ('_transient_ctn_ship_%') OR \"option_name\" LIKE ('_transient_timeout_ctn_ship_%')" );

		// Clear product specific transients
		$post_transients_to_clear = array(
			'ctn_product_children_ids_',
			'ctn_product_total_stock_',
			'ctn_average_rating_',
			'ctn_rating_count_',
			'carton_product_type_', // No longer used
			'ctn_product_type_', // No longer used
		);

		if ( $post_id > 0 ) {

			foreach( $post_transients_to_clear as $transient ) {
				delete_transient( $transient . $post_id );
				$wpdb->query( $wpdb->prepare( "DELETE FROM \"$wpdb->options\" WHERE \"option_name\" = %s OR \"option_name\" = %s", '_transient_' . $transient . $post_id, '_transient_timeout_' . $transient . $post_id ) );
			}

			clean_post_cache( $post_id );

		} else {

			foreach( $post_transients_to_clear as $transient ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM \"$wpdb->options\" WHERE \"option_name\" LIKE %s OR \"option_name\" LIKE %s", '_transient_' . $transient . '%', '_transient_timeout_' . $transient . '%' ) );
			}

		}
	}

	/** Body Classes **********************************************************/

	/**
	 * Add a class to the webpage body.
	 *
	 * @access public
	 * @param string $class
	 * @return void
	 */
	public function add_body_class( $class ) {
		$this->_body_classes[] = sanitize_html_class( strtolower($class) );
	}

	/**
	 * Output classes on the body tag.
	 *
	 * @access public
	 * @param mixed $classes
	 * @return array
	 */
	public function output_body_class( $classes ) {
		if ( sizeof( $this->_body_classes ) > 0 ) $classes = array_merge( $classes, $this->_body_classes );

		if ( is_singular('product') ) {
			$key = array_search( 'singular', $classes );
			if ( $key !== false ) unset( $classes[$key] );
		}

		return $classes;
	}

	/** Post Classes **********************************************************/

	/**
	 * Adds extra post classes for products
	 *
	 * @since 2.0
	 * @access public
	 * @param array $classes
	 * @param string|array $class
	 * @param int $post_id
	 * @return array
	 */
	public function post_class( $classes, $class, $post_id ) {
		$product = get_product( $post_id );

		if ( $product ) {
			if ( $product->is_on_sale() ) {
				$classes[] = 'sale';
			}
			if ( $product->is_featured() ) {
				$classes[] = 'featured';
			}
			$classes[] = $product->stock_status;
		}

		return $classes;
	}

	/** Inline JavaScript Helper **********************************************/

	/**
	 * Add some JavaScript inline to be output in the footer.
	 *
	 * @access public
	 * @param string $code
	 * @return void
	 */
	public function add_inline_js( $code ) {
		$this->_inline_js .= "\n" . $code . "\n";
	}

	/**
	 * Output any queued inline JS.
	 *
	 * @access public
	 * @return void
	 */
	public function output_inline_js() {
		if ( $this->_inline_js ) {

			echo "<!-- CartoN JavaScript-->\n<script type=\"text/javascript\">\njQuery(document).ready(function($) {";

			// Sanitize
			$this->_inline_js = wp_check_invalid_utf8( $this->_inline_js );
			$this->_inline_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $this->_inline_js );
			$this->_inline_js = str_replace( "\r", '', $this->_inline_js );

			// Output
			echo $this->_inline_js;

			echo "});\n";

			echo "</script>\n";

			$this->_inline_js = '';
		}
	}
}

/**
 * Init carton class
 */
$GLOBALS['carton'] = new Carton();

} // class_exists check
