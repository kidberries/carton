<?php
/**
 * Welcome Page Class
 *
 * Shows a feature overview for the new version (major) and credits.
 *
 * Adapted from code in EDD (Copyright (c) 2012, Pippin Williamson) and WP.
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin
 * @version     2.0.0
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * CTN_Welcome_Page class.
 *
 * @since 2.0
 */
class CTN_Welcome_Page {

	private $plugin;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->plugin             = 'carton/carton.php';

		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'welcome'    ) );
	}

	/**
	 * Add admin menus/screens
	 *
	 * @access public
	 * @return void
	 */
	public function admin_menus() {

		$welcome_page_title = __( 'Welcome to CartoN', 'carton' );

		// About
		$about = add_dashboard_page( $welcome_page_title, $welcome_page_title, 'manage_options', 'wc-about', array( $this, 'about_screen' ) );

		// Credits
		$credits = add_dashboard_page( $welcome_page_title, $welcome_page_title, 'manage_options', 'wc-credits', array( $this, 'credits_screen' ) );

		add_action( 'admin_print_styles-'. $about, array( $this, 'admin_css' ) );
		add_action( 'admin_print_styles-'. $credits, array( $this, 'admin_css' ) );
	}

	/**
	 * admin_css function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_css() {
		wp_enqueue_style( 'carton-activation', plugins_url(  '/assets/css/activation.css', dirname( dirname( __FILE__ ) ) ) );
	}

	/**
	 * Add styles just for this page, and remove dashboard page links.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_head() {
		global $carton;

		remove_submenu_page( 'index.php', 'wc-about' );
		remove_submenu_page( 'index.php', 'wc-credits' );

		// Badge for welcome page
		$badge_url = $carton->plugin_url() . '/assets/images/welcome/wc-badge.png';
		?>
		<style type="text/css">
			/*<![CDATA[*/
			.wc-badge {
				padding-top: 150px;
				height: 52px;
				width: 185px;
				color: #9c5d90;
				font-weight: bold;
				font-size: 14px;
				text-align: center;
				text-shadow: 0 1px 0 rgba(255, 255, 255, 0.6);
				margin: 0 -5px;
				background: url('<?php echo $carton->plugin_url() . '/assets/images/welcome/wc-welcome.png'; ?>') no-repeat center center;
			}

			@media
			(-webkit-min-device-pixel-ratio: 2),
			(min-resolution: 192dpi) {
				.wc-badge {
					background-image:url('<?php echo $carton->plugin_url() . '/assets/images/welcome/wc-welcome@2x.png'; ?>');
					background-size: 173px 194px;
				}
			}

			.about-wrap .wc-badge {
				position: absolute;
				top: 0;
				right: 0;
			}
			/*]]>*/
		</style>
		<?php
	}

	/**
	 * Into text/links shown on all about pages.
	 *
	 * @access private
	 * @return void
	 */
	private function intro() {
		global $carton;

		// Flush after upgrades
		if ( ! empty( $_GET['wc-updated'] ) || ! empty( $_GET['wc-installed'] ) )
			flush_rewrite_rules();

		// Drop minor version if 0
		$major_version = substr( $carton->version, 0, 3 );
		?>
		<h1><?php printf( __( 'Welcome to CartoN %s', 'carton' ), $major_version ); ?></h1>

		<div class="about-text carton-about-text">
			<?php
				if ( ! empty( $_GET['wc-installed'] ) )
					$message = __( 'Thanks, all done!', 'carton' );
				elseif ( ! empty( $_GET['wc-updated'] ) )
					$message = __( 'Thank you for updating to the latest version!', 'carton' );
				else
					$message = __( 'Thanks for installing!', 'carton' );

				printf( __( '%s CartoN %s is more powerful, stable, and secure than ever before. We hope you enjoy it.', 'carton' ), $message, $major_version );
			?>
		</div>

		<div class="wc-badge"><?php printf( __( 'Version %s' ), $carton->version ); ?></div>

		<p class="carton-actions">
			<a href="<?php echo admin_url('admin.php?page=carton_settings'); ?>" class="button button-primary"><?php _e( 'Settings', 'carton' ); ?></a>
			<a class="docs button button-primary" href="http://docs.carton-ecommerce.com/"><?php _e( 'Docs', 'carton' ); ?></a>
			<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.carton-ecommerce.com/carton/" data-text="A open-source (free) #ecommerce plugin for #WordPress that helps you sell anything. Beautifully." data-via="CartonThemes" data-size="large" data-hashtags="CartoN">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</p>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( $_GET['page'] == 'wc-about' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wc-about' ), 'index.php' ) ) ); ?>">
				<?php _e( "What's New", 'carton' ); ?>
			</a><a class="nav-tab <?php if ( $_GET['page'] == 'wc-credits' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wc-credits' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Credits', 'carton' ); ?>
			</a>
		</h2>
		<?php
	}

	/**
	 * Output the about screen.
	 *
	 * @access public
	 * @return void
	 */
	public function about_screen() {
		global $carton;
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<!--<div class="changelog point-releases"></div>-->

			<div class="changelog">

				<h3><?php _e( 'Security in mind', 'carton' ); ?></h3>

				<div class="feature-section images-stagger-right">
					<img src="<?php echo $carton->plugin_url() . '/assets/images/welcome/badge-sucuri.png'; ?>" alt="Sucuri Safe Plugin" style="padding: 1em" />
					<h4><?php _e( 'Sucuri Safe Plugin', 'carton' ); ?></h4>
					<p><?php _e( 'You will be happy to learn that CartoN has been audited and certified by the Sucuri Security team. Whilst there is not much to be seen visually to understand the amount of work that went into this audit, rest assured that your website is powered by one of the most powerful and stable eCommerce plugins available.', 'carton' ); ?></p>
				</div>

				<h3><?php _e( 'A Smoother Admin Experience', 'carton' ); ?></h3>

				<div class="feature-section col three-col">

					<div>
						<img src="<?php echo $carton->plugin_url() . '/assets/images/welcome/product.png'; ?>" alt="Product panel screenshot" style="width: 99%; margin: 0 0 1em;" />
						<h4><?php _e( 'New Product Panel', 'carton' ); ?></h4>
						<p><?php _e( 'We have revised the product data panel making it cleaner, more streamlined, and more logical. Adding products is a breeze!', 'carton' ); ?></p>
					</div>

					<div>
						<img src="<?php echo $carton->plugin_url() . '/assets/images/welcome/orders.png'; ?>" alt="Order panel screenshot" style="width: 99%; margin: 0 0 1em;" />
						<h4><?php _e( 'Nicer Order Screens', 'carton' ); ?></h4>
						<p><?php _e( 'Order pages have had a cleanup, with a more easily scannable interface. We particularly like the new status icons!', 'carton' ); ?></p>
					</div>

					<div class="last-feature">
						<img src="<?php echo $carton->plugin_url() . '/assets/images/welcome/downloads.png'; ?>" alt="Download panel screenshot" style="width: 99%; margin: 0 0 1em;" />
						<h4><?php _e( 'Multi-Download Support', 'carton' ); ?></h4>
						<p><?php _e( 'Products can have multiple downloadable files - purchasers will get access to all the files added.', 'carton' ); ?></p>
					</div>

				</div>

				<h3><?php _e( 'Less Taxing Taxes', 'carton' ); ?></h3>

				<div class="feature-section col two-col">

					<img src="<?php echo $carton->plugin_url() . '/assets/images/welcome/taxes.png'; ?>" alt="Tax Options" style="width:99%; margin: 0 0 1em 0;" />
					<div>
						<h4><?php _e( 'New Tax Input Panel', 'carton' ); ?></h4>
						<p><?php _e( 'The tax input pages have been streamlined to make inputting taxes simpler - adding multiple taxes for a single jurisdiction is now much easier using the priority system. There is also CSV import/export support.', 'carton' ); ?></p>
					</div>

					<div class="last-feature">
						<h4><?php _e( 'Improved Tax Options', 'carton' ); ?></h4>
						<p><?php _e( 'As requested by some users, we now support taxing the billing address instead of shipping (optional), and we allow you to choose which tax class applies to shipping.', 'carton' ); ?></p>
					</div>

				</div>

				<h3><?php _e( 'Product Listing Improvements Customers Will Love', 'carton' ); ?></h3>

				<div class="feature-section col three-col">

					<div>
						<img src="<?php echo $carton->plugin_url() . '/assets/images/welcome/sorting.png'; ?>" alt="Sorting" style="width: 99%; margin: 0 0 1em;" />
						<h4><?php _e( 'New Sorting Options', 'carton' ); ?></h4>
						<p><?php _e( 'Customers can now sort products by popularity and ratings.', 'carton' ); ?></p>
					</div>

					<div>
						<img src="<?php echo $carton->plugin_url() . '/assets/images/welcome/pagination.png'; ?>" alt="Pagination" style="width: 99%; margin: 0 0 1em;" />
						<h4><?php _e( 'Better Pagination and Result Counts', 'carton' ); ?></h4>
						<p><?php _e( 'Numbered pagination has been added to core, and we show the number of results found above the listings.', 'carton' ); ?></p>
					</div>

					<div class="last-feature">
						<img src="<?php echo $carton->plugin_url() . '/assets/images/welcome/rating.png'; ?>" alt="Ratings" style="width: 99%; margin: 0 0 1em;" />
						<h4><?php _e( 'Inline Star Rating Display', 'carton' ); ?></h4>
						<p><?php _e( 'We have added star ratings to the catalog which are pulled from reviews.', 'carton' ); ?></p>
					</div>

				</div>

			</div>

			<div class="changelog">
				<h3><?php _e( 'Under the Hood', 'carton' ); ?></h3>

				<div class="feature-section col three-col">
					<div>
						<h4><?php _e( 'New product classes', 'carton' ); ?></h4>
						<p><?php _e( 'The product classes have been rewritten and are now factory based. Much more extendable, and easier to query products using the new <code>get_product()</code> function.', 'carton' ); ?></p>
					</div>

					<div>
						<h4><?php _e( 'Capability overhaul', 'carton' ); ?></h4>
						<p><?php _e( 'More granular capabilities for admin/shop manager roles covering products, orders and coupons.', 'carton' ); ?></p>
					</div>

					<div class="last-feature">
						<h4><?php _e( 'API Improvements', 'carton' ); ?></h4>
						<p><?php _e( '<code>WC-API</code> now has real endpoints, and we\'ve optimised the gateways API significantly by only loading gateways when needed.', 'carton' ); ?></p>
					</div>
				</div>
				<div class="feature-section col three-col">

					<div>
						<h4><?php _e( 'Cache-friendly cart widgets', 'carton' ); ?></h4>
						<p><?php _e( 'Cart widgets and other "fragments" are now pulled in via AJAX - this works wonders with static page caching.', 'carton' ); ?></p>
					</div>

					<div>
						<h4><?php _e( 'Session handling', 'carton' ); ?></h4>
						<p><?php _e( 'PHP SESSIONS have been a problem for many users in the past, so we\'ve developed our own handler using cookies and options to make these more reliable.', 'carton' ); ?></p>
					</div>

					<div class="last-feature">
						<h4><?php _e( 'Retina Ready', 'carton' ); ?></h4>
						<p><?php _e( 'All graphics within WC have been optimised for HiDPI displays.', 'carton' ); ?></p>
					</div>

				</div>
				<div class="feature-section col three-col">

					<div>
						<h4><?php _e( 'Better stock handling', 'carton' ); ?></h4>
						<p><?php _e( 'We have added an option to hold stock for unpaid orders (defaults to 60mins). When this time limit is reached, and the order is not paid for, stock is released and the order is cancelled.', 'carton' ); ?></p>
					</div>

					<div>
						<h4><?php _e( 'Improved Line-item storage', 'carton' ); ?></h4>
						<p><?php _e( 'We have changed how order items get stored making them easier (and faster) to access for reporting. Order items are no longer serialised within an order - they are stored within their own table.', 'carton' ); ?></p>
					</div>

					<div class="last-feature">
						<h4><?php _e( 'Autoload', 'carton' ); ?></h4>
						<p><?php _e( 'We have setup autoloading for classes - this has dramatically reduced memory usage in 2.0.', 'carton' ); ?></p>
					</div>

				</div>
			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'carton_settings' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to CartoN Settings', 'carton' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Output the credits.
	 *
	 * @access public
	 * @return void
	 */
	public function credits_screen() {
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<p class="about-description"><?php _e( 'CartoN is developed and maintained by a worldwide team of passionate individuals and backed by an awesome developer community. Want to see your name? <a href="https://github.com/carton-ecommerce/carton/blob/master/CONTRIBUTING.md">Contribute to CartoN</a>.', 'carton' ); ?></p>

			<?php echo $this->contributors(); ?>

		</div>
		<?php
	}

	/**
	 * Render Contributors List
	 *
	 * @access public
	 * @return string $contributor_list HTML formatted list of contributors.
	 */
	public function contributors() {
		$contributors = $this->get_contributors();

		if ( empty( $contributors ) )
			return '';

		$contributor_list = '<ul class="wp-people-group">';

		foreach ( $contributors as $contributor ) {
			$contributor_list .= '<li class="wp-person">';
			$contributor_list .= sprintf( '<a href="%s" title="%s">',
				esc_url( 'https://github.com/' . $contributor->login ),
				esc_html( sprintf( __( 'View %s', 'carton' ), $contributor->login ) )
			);
			$contributor_list .= sprintf( '<img src="%s" width="64" height="64" class="gravatar" alt="%s" />', esc_url( $contributor->avatar_url ), esc_html( $contributor->login ) );
			$contributor_list .= '</a>';
			$contributor_list .= sprintf( '<a class="web" href="%s">%s</a>', esc_url( 'https://github.com/' . $contributor->login ), esc_html( $contributor->login ) );
			$contributor_list .= '</a>';
			$contributor_list .= '</li>';
		}

		$contributor_list .= '</ul>';

		return $contributor_list;
	}

	/**
	 * Retreive list of contributors from GitHub.
	 *
	 * @access public
	 * @return void
	 */
	public function get_contributors() {
		$contributors = get_transient( 'carton_contributors' );

		if ( false !== $contributors )
			return $contributors;

		$response = wp_remote_get( 'https://api.github.com/repos/carton-ecommerce/carton/contributors', array( 'sslverify' => false ) );

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) )
			return array();

		$contributors = json_decode( wp_remote_retrieve_body( $response ) );

		if ( ! is_array( $contributors ) )
			return array();

		set_transient( 'carton_contributors', $contributors, 3600 );

		return $contributors;
	}

	/**
	 * Sends user to the welcome page on first activation
	 */
	public function welcome() {

		// Bail if no activation redirect transient is set
	    if ( ! get_transient( '_ctn_activation_redirect' ) )
			return;

		// Delete the redirect transient
		delete_transient( '_ctn_activation_redirect' );

		// Bail if we are waiting to install or update via the interface update/install links
		if ( get_option( '_ctn_needs_update' ) == 1 || get_option( '_ctn_needs_pages' ) == 1 )
			return;

		// Bail if activating from network, or bulk, or within an iFrame
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) || defined( 'IFRAME_REQUEST' ) )
			return;

		if ( ( isset( $_GET['action'] ) && 'upgrade-plugin' == $_GET['action'] ) && ( isset( $_GET['plugin'] ) && strstr( $_GET['plugin'], 'carton.php' ) ) )
			return;

		wp_safe_redirect( admin_url( 'index.php?page=wc-about' ) );
		exit;
	}
}

new CTN_Welcome_Page();