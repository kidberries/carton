<?php
/**
 * CartoN countries
 *
 * The CartoN countries class stores country/state data.
 *
 * @class 		CTN_Countries
 * @version		1.6.4
 * @package		CartoN/Classes
 * @category	Class
 * @author 		CartonThemes
 */
class CTN_Countries {

	/** @var array Array of countries */
	public $countries;

	/** @var array Array of states */
	public $states;

	/** @var array Array of locales */
	public $locale;

	/** @var array Array of address formats for locales */
	public $address_formats;

	/**
	 * Constructor for the counties class - defines all countries and states.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		global $carton, $states;

		$this->countries = apply_filters('carton_countries', array(
			'AF' => __( 'Afghanistan', 'carton' ),
			'AX' => __( '&#197;land Islands', 'carton' ),
			'AL' => __( 'Albania', 'carton' ),
			'DZ' => __( 'Algeria', 'carton' ),
			'AD' => __( 'Andorra', 'carton' ),
			'AO' => __( 'Angola', 'carton' ),
			'AI' => __( 'Anguilla', 'carton' ),
			'AQ' => __( 'Antarctica', 'carton' ),
			'AG' => __( 'Antigua and Barbuda', 'carton' ),
			'AR' => __( 'Argentina', 'carton' ),
			'AM' => __( 'Armenia', 'carton' ),
			'AW' => __( 'Aruba', 'carton' ),
			'AU' => __( 'Australia', 'carton' ),
			'AT' => __( 'Austria', 'carton' ),
			'AZ' => __( 'Azerbaijan', 'carton' ),
			'BS' => __( 'Bahamas', 'carton' ),
			'BH' => __( 'Bahrain', 'carton' ),
			'BD' => __( 'Bangladesh', 'carton' ),
			'BB' => __( 'Barbados', 'carton' ),
			'BY' => __( 'Belarus', 'carton' ),
			'BE' => __( 'Belgium', 'carton' ),
			'PW' => __( 'Belau', 'carton' ),
			'BZ' => __( 'Belize', 'carton' ),
			'BJ' => __( 'Benin', 'carton' ),
			'BM' => __( 'Bermuda', 'carton' ),
			'BT' => __( 'Bhutan', 'carton' ),
			'BO' => __( 'Bolivia', 'carton' ),
			'BQ' => __( 'Bonaire, Saint Eustatius and Saba', 'carton' ),
			'BA' => __( 'Bosnia and Herzegovina', 'carton' ),
			'BW' => __( 'Botswana', 'carton' ),
			'BV' => __( 'Bouvet Island', 'carton' ),
			'BR' => __( 'Brazil', 'carton' ),
			'IO' => __( 'British Indian Ocean Territory', 'carton' ),
			'VG' => __( 'British Virgin Islands', 'carton' ),
			'BN' => __( 'Brunei', 'carton' ),
			'BG' => __( 'Bulgaria', 'carton' ),
			'BF' => __( 'Burkina Faso', 'carton' ),
			'BI' => __( 'Burundi', 'carton' ),
			'KH' => __( 'Cambodia', 'carton' ),
			'CM' => __( 'Cameroon', 'carton' ),
			'CA' => __( 'Canada', 'carton' ),
			'CV' => __( 'Cape Verde', 'carton' ),
			'KY' => __( 'Cayman Islands', 'carton' ),
			'CF' => __( 'Central African Republic', 'carton' ),
			'TD' => __( 'Chad', 'carton' ),
			'CL' => __( 'Chile', 'carton' ),
			'CN' => __( 'China', 'carton' ),
			'CX' => __( 'Christmas Island', 'carton' ),
			'CC' => __( 'Cocos (Keeling) Islands', 'carton' ),
			'CO' => __( 'Colombia', 'carton' ),
			'KM' => __( 'Comoros', 'carton' ),
			'CG' => __( 'Congo (Brazzaville)', 'carton' ),
			'CD' => __( 'Congo (Kinshasa)', 'carton' ),
			'CK' => __( 'Cook Islands', 'carton' ),
			'CR' => __( 'Costa Rica', 'carton' ),
			'HR' => __( 'Croatia', 'carton' ),
			'CU' => __( 'Cuba', 'carton' ),
			'CW' => __( 'Cura&Ccedil;ao', 'carton' ),
			'CY' => __( 'Cyprus', 'carton' ),
			'CZ' => __( 'Czech Republic', 'carton' ),
			'DK' => __( 'Denmark', 'carton' ),
			'DJ' => __( 'Djibouti', 'carton' ),
			'DM' => __( 'Dominica', 'carton' ),
			'DO' => __( 'Dominican Republic', 'carton' ),
			'EC' => __( 'Ecuador', 'carton' ),
			'EG' => __( 'Egypt', 'carton' ),
			'SV' => __( 'El Salvador', 'carton' ),
			'GQ' => __( 'Equatorial Guinea', 'carton' ),
			'ER' => __( 'Eritrea', 'carton' ),
			'EE' => __( 'Estonia', 'carton' ),
			'ET' => __( 'Ethiopia', 'carton' ),
			'FK' => __( 'Falkland Islands', 'carton' ),
			'FO' => __( 'Faroe Islands', 'carton' ),
			'FJ' => __( 'Fiji', 'carton' ),
			'FI' => __( 'Finland', 'carton' ),
			'FR' => __( 'France', 'carton' ),
			'GF' => __( 'French Guiana', 'carton' ),
			'PF' => __( 'French Polynesia', 'carton' ),
			'TF' => __( 'French Southern Territories', 'carton' ),
			'GA' => __( 'Gabon', 'carton' ),
			'GM' => __( 'Gambia', 'carton' ),
			'GE' => __( 'Georgia', 'carton' ),
			'DE' => __( 'Germany', 'carton' ),
			'GH' => __( 'Ghana', 'carton' ),
			'GI' => __( 'Gibraltar', 'carton' ),
			'GR' => __( 'Greece', 'carton' ),
			'GL' => __( 'Greenland', 'carton' ),
			'GD' => __( 'Grenada', 'carton' ),
			'GP' => __( 'Guadeloupe', 'carton' ),
			'GT' => __( 'Guatemala', 'carton' ),
			'GG' => __( 'Guernsey', 'carton' ),
			'GN' => __( 'Guinea', 'carton' ),
			'GW' => __( 'Guinea-Bissau', 'carton' ),
			'GY' => __( 'Guyana', 'carton' ),
			'HT' => __( 'Haiti', 'carton' ),
			'HM' => __( 'Heard Island and McDonald Islands', 'carton' ),
			'HN' => __( 'Honduras', 'carton' ),
			'HK' => __( 'Hong Kong', 'carton' ),
			'HU' => __( 'Hungary', 'carton' ),
			'IS' => __( 'Iceland', 'carton' ),
			'IN' => __( 'India', 'carton' ),
			'ID' => __( 'Indonesia', 'carton' ),
			'IR' => __( 'Iran', 'carton' ),
			'IQ' => __( 'Iraq', 'carton' ),
			'IE' => __( 'Republic of Ireland', 'carton' ),
			'IM' => __( 'Isle of Man', 'carton' ),
			'IL' => __( 'Israel', 'carton' ),
			'IT' => __( 'Italy', 'carton' ),
			'CI' => __( 'Ivory Coast', 'carton' ),
			'JM' => __( 'Jamaica', 'carton' ),
			'JP' => __( 'Japan', 'carton' ),
			'JE' => __( 'Jersey', 'carton' ),
			'JO' => __( 'Jordan', 'carton' ),
			'KZ' => __( 'Kazakhstan', 'carton' ),
			'KE' => __( 'Kenya', 'carton' ),
			'KI' => __( 'Kiribati', 'carton' ),
			'KW' => __( 'Kuwait', 'carton' ),
			'KG' => __( 'Kyrgyzstan', 'carton' ),
			'LA' => __( 'Laos', 'carton' ),
			'LV' => __( 'Latvia', 'carton' ),
			'LB' => __( 'Lebanon', 'carton' ),
			'LS' => __( 'Lesotho', 'carton' ),
			'LR' => __( 'Liberia', 'carton' ),
			'LY' => __( 'Libya', 'carton' ),
			'LI' => __( 'Liechtenstein', 'carton' ),
			'LT' => __( 'Lithuania', 'carton' ),
			'LU' => __( 'Luxembourg', 'carton' ),
			'MO' => __( 'Macao S.A.R., China', 'carton' ),
			'MK' => __( 'Macedonia', 'carton' ),
			'MG' => __( 'Madagascar', 'carton' ),
			'MW' => __( 'Malawi', 'carton' ),
			'MY' => __( 'Malaysia', 'carton' ),
			'MV' => __( 'Maldives', 'carton' ),
			'ML' => __( 'Mali', 'carton' ),
			'MT' => __( 'Malta', 'carton' ),
			'MH' => __( 'Marshall Islands', 'carton' ),
			'MQ' => __( 'Martinique', 'carton' ),
			'MR' => __( 'Mauritania', 'carton' ),
			'MU' => __( 'Mauritius', 'carton' ),
			'YT' => __( 'Mayotte', 'carton' ),
			'MX' => __( 'Mexico', 'carton' ),
			'FM' => __( 'Micronesia', 'carton' ),
			'MD' => __( 'Moldova', 'carton' ),
			'MC' => __( 'Monaco', 'carton' ),
			'MN' => __( 'Mongolia', 'carton' ),
			'ME' => __( 'Montenegro', 'carton' ),
			'MS' => __( 'Montserrat', 'carton' ),
			'MA' => __( 'Morocco', 'carton' ),
			'MZ' => __( 'Mozambique', 'carton' ),
			'MM' => __( 'Myanmar', 'carton' ),
			'NA' => __( 'Namibia', 'carton' ),
			'NR' => __( 'Nauru', 'carton' ),
			'NP' => __( 'Nepal', 'carton' ),
			'NL' => __( 'Netherlands', 'carton' ),
			'AN' => __( 'Netherlands Antilles', 'carton' ),
			'NC' => __( 'New Caledonia', 'carton' ),
			'NZ' => __( 'New Zealand', 'carton' ),
			'NI' => __( 'Nicaragua', 'carton' ),
			'NE' => __( 'Niger', 'carton' ),
			'NG' => __( 'Nigeria', 'carton' ),
			'NU' => __( 'Niue', 'carton' ),
			'NF' => __( 'Norfolk Island', 'carton' ),
			'KP' => __( 'North Korea', 'carton' ),
			'NO' => __( 'Norway', 'carton' ),
			'OM' => __( 'Oman', 'carton' ),
			'PK' => __( 'Pakistan', 'carton' ),
			'PS' => __( 'Palestinian Territory', 'carton' ),
			'PA' => __( 'Panama', 'carton' ),
			'PG' => __( 'Papua New Guinea', 'carton' ),
			'PY' => __( 'Paraguay', 'carton' ),
			'PE' => __( 'Peru', 'carton' ),
			'PH' => __( 'Philippines', 'carton' ),
			'PN' => __( 'Pitcairn', 'carton' ),
			'PL' => __( 'Poland', 'carton' ),
			'PT' => __( 'Portugal', 'carton' ),
			'QA' => __( 'Qatar', 'carton' ),
			'RE' => __( 'Reunion', 'carton' ),
			'RO' => __( 'Romania', 'carton' ),
			'RU' => __( 'Russia', 'carton' ),
			'RW' => __( 'Rwanda', 'carton' ),
			'BL' => __( 'Saint Barth&eacute;lemy', 'carton' ),
			'SH' => __( 'Saint Helena', 'carton' ),
			'KN' => __( 'Saint Kitts and Nevis', 'carton' ),
			'LC' => __( 'Saint Lucia', 'carton' ),
			'MF' => __( 'Saint Martin (French part)', 'carton' ),
			'SX' => __( 'Saint Martin (Dutch part)', 'carton' ),
			'PM' => __( 'Saint Pierre and Miquelon', 'carton' ),
			'VC' => __( 'Saint Vincent and the Grenadines', 'carton' ),
			'SM' => __( 'San Marino', 'carton' ),
			'ST' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'carton' ),
			'SA' => __( 'Saudi Arabia', 'carton' ),
			'SN' => __( 'Senegal', 'carton' ),
			'RS' => __( 'Serbia', 'carton' ),
			'SC' => __( 'Seychelles', 'carton' ),
			'SL' => __( 'Sierra Leone', 'carton' ),
			'SG' => __( 'Singapore', 'carton' ),
			'SK' => __( 'Slovakia', 'carton' ),
			'SI' => __( 'Slovenia', 'carton' ),
			'SB' => __( 'Solomon Islands', 'carton' ),
			'SO' => __( 'Somalia', 'carton' ),
			'ZA' => __( 'South Africa', 'carton' ),
			'GS' => __( 'South Georgia/Sandwich Islands', 'carton' ),
			'KR' => __( 'South Korea', 'carton' ),
			'SS' => __( 'South Sudan', 'carton' ),
			'ES' => __( 'Spain', 'carton' ),
			'LK' => __( 'Sri Lanka', 'carton' ),
			'SD' => __( 'Sudan', 'carton' ),
			'SR' => __( 'Suriname', 'carton' ),
			'SJ' => __( 'Svalbard and Jan Mayen', 'carton' ),
			'SZ' => __( 'Swaziland', 'carton' ),
			'SE' => __( 'Sweden', 'carton' ),
			'CH' => __( 'Switzerland', 'carton' ),
			'SY' => __( 'Syria', 'carton' ),
			'TW' => __( 'Taiwan', 'carton' ),
			'TJ' => __( 'Tajikistan', 'carton' ),
			'TZ' => __( 'Tanzania', 'carton' ),
			'TH' => __( 'Thailand', 'carton' ),
			'TL' => __( 'Timor-Leste', 'carton' ),
			'TG' => __( 'Togo', 'carton' ),
			'TK' => __( 'Tokelau', 'carton' ),
			'TO' => __( 'Tonga', 'carton' ),
			'TT' => __( 'Trinidad and Tobago', 'carton' ),
			'TN' => __( 'Tunisia', 'carton' ),
			'TR' => __( 'Turkey', 'carton' ),
			'TM' => __( 'Turkmenistan', 'carton' ),
			'TC' => __( 'Turks and Caicos Islands', 'carton' ),
			'TV' => __( 'Tuvalu', 'carton' ),
			'UG' => __( 'Uganda', 'carton' ),
			'UA' => __( 'Ukraine', 'carton' ),
			'AE' => __( 'United Arab Emirates', 'carton' ),
			'GB' => __( 'United Kingdom', 'carton' ),
			'US' => __( 'United States', 'carton' ),
			'UY' => __( 'Uruguay', 'carton' ),
			'UZ' => __( 'Uzbekistan', 'carton' ),
			'VU' => __( 'Vanuatu', 'carton' ),
			'VA' => __( 'Vatican', 'carton' ),
			'VE' => __( 'Venezuela', 'carton' ),
			'VN' => __( 'Vietnam', 'carton' ),
			'WF' => __( 'Wallis and Futuna', 'carton' ),
			'EH' => __( 'Western Sahara', 'carton' ),
			'WS' => __( 'Western Samoa', 'carton' ),
			'YE' => __( 'Yemen', 'carton' ),
			'ZM' => __( 'Zambia', 'carton' ),
			'ZW' => __( 'Zimbabwe', 'carton' )
		));

		// States set to array() are blank i.e. the country has no use for the state field.
		$states = array(
			'AF' => array(),
			'AT' => array(),
			'BE' => array(),
			'BI' => array(),
			'CZ' => array(),
			'DE' => array(),
			'DK' => array(),
			'FI' => array(),
			'FR' => array(),
			'HU' => array(),
			'IS' => array(),
			'IL' => array(),
			'KR' => array(),
			'NL' => array(),
			'NO' => array(),
			'PL' => array(),
			'PT' => array(),
			'SG' => array(),
			'SK' => array(),
			'SI' => array(),
			'LK' => array(),
			'SE' => array(),
			'VN' => array(),
		);

		// Load only the state files the shop owner wants/needs
		$allowed = $this->get_allowed_countries();

		if ( $allowed )
			foreach ( $allowed as $CC => $country )
				if ( ! isset( $states[ $CC ] ) && file_exists( $carton->plugin_path() . '/i18n/states/' . $CC . '.php' ) )
					include( $carton->plugin_path() . '/i18n/states/' . $CC . '.php' );

		$this->states = apply_filters('carton_states', $states );
	}


	/**
	 * Get the base country for the store.
	 *
	 * @access public
	 * @return string
	 */
	public function get_base_country() {
		$default = esc_attr( get_option('carton_default_country') );
		if ( ( $pos = strpos( $default, ':' ) ) === false )
			return $default;
		return substr( $default, 0, $pos );
	}


	/**
	 * Get the base state for the state.
	 *
	 * @access public
	 * @return string
	 */
	public function get_base_state() {
		$default = esc_attr( get_option( 'carton_default_country' ) );
		if ( ( $pos = strrpos( $default, ':' ) ) === false )
			return '';
		return substr( $default, $pos + 1 );
	}


	/**
	 * Get the allowed countries for the store.
	 *
	 * @access public
	 * @return array
	 */
	public function get_allowed_countries() {

		if ( apply_filters('carton_sort_countries', true ) )
			asort( $this->countries );

		if ( get_option('carton_allowed_countries') !== 'specific' )
			return $this->countries;

		$allowed_countries = array();

		$allowed_countries_raw = get_option( 'carton_specific_allowed_countries' );

		foreach ( $allowed_countries_raw as $country )
			$allowed_countries[ $country ] = $this->countries[ $country ];

		return $allowed_countries;
	}


	/**
	 * get_allowed_country_states function.
	 *
	 * @access public
	 * @return array
	 */
	public function get_allowed_country_states() {

		if ( get_option('carton_allowed_countries') !== 'specific' )
			return $this->states;

		$allowed_states = array();

		$allowed_countries_raw = get_option( 'carton_specific_allowed_countries' );

		foreach ( $allowed_countries_raw as $country )
			if ( ! empty( $this->states[ $country ] ) )
				$allowed_states[ $country ] = $this->states[ $country ];

		return $allowed_states;
	}


	/**
	 * Gets an array of countries in the EU.
	 *
	 * @access public
	 * @return array
	 */
	public function get_european_union_countries() {
		return array( 'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK' );
	}


	/**
	 * Gets the correct string for shipping - ether 'to the' or 'to'
	 *
	 * @access public
	 * @return string
	 */
	public function shipping_to_prefix() {
		global $carton;
		$return = '';
		if (in_array($carton->customer->get_shipping_country(), array( 'GB', 'US', 'AE', 'CZ', 'DO', 'NL', 'PH', 'USAF' ))) $return = __( 'to the', 'carton' );
		else $return = __( 'to', 'carton' );
		return apply_filters('carton_countries_shipping_to_prefix', $return, $carton->customer->get_shipping_country());
	}


	/**
	 * Prefix certain countries with 'the'
	 *
	 * @access public
	 * @return string
	 */
	public function estimated_for_prefix() {
		$return = '';
		if (in_array($this->get_base_country(), array( 'GB', 'US', 'AE', 'CZ', 'DO', 'NL', 'PH', 'USAF' ))) $return = __( 'the', 'carton' ) . ' ';
		return apply_filters('carton_countries_estimated_for_prefix', $return, $this->get_base_country());
	}


	/**
	 * Correctly name tax in some countries VAT on the frontend
	 *
	 * @access public
	 * @return string
	 */
	public function tax_or_vat() {
		$return = ( in_array($this->get_base_country(), $this->get_european_union_countries()) ) ? __( 'VAT', 'carton' ) : __( 'Tax', 'carton' );

		return apply_filters( 'carton_countries_tax_or_vat', $return );
	}


	/**
	 * Include the Inc Tax label.
	 *
	 * @access public
	 * @return string
	 */
	public function inc_tax_or_vat() {
		$return = ( in_array($this->get_base_country(), $this->get_european_union_countries()) ) ? __( '(incl. VAT)', 'carton' ) : __( '(incl. tax)', 'carton' );

		return apply_filters( 'carton_countries_inc_tax_or_vat', $return );
	}


	/**
	 * Include the Ex Tax label.
	 *
	 * @access public
	 * @return string
	 */
	public function ex_tax_or_vat() {
		$return = ( in_array($this->get_base_country(), $this->get_european_union_countries()) ) ? __( '(ex. VAT)', 'carton' ) : __( '(ex. tax)', 'carton' );

		return apply_filters( 'carton_countries_ex_tax_or_vat', $return );
	}


	/**
	 * Get the states for a country.
	 *
	 * @access public
	 * @param mixed $cc country code
	 * @return array of states
	 */
	public function get_states( $cc ) {
		if (isset( $this->states[$cc] )) return $this->states[$cc];
	}


	/**
	 * Outputs the list of countries and states for use in dropdown boxes.
	 *
	 * @access public
	 * @param string $selected_country (default: '')
	 * @param string $selected_state (default: '')
	 * @param bool $escape (default: false)
	 * @return void
	 */
	public function country_dropdown_options( $selected_country = '', $selected_state = '', $escape = false ) {

		if ( apply_filters('carton_sort_countries', true ) )
			asort( $this->countries );

		if ( $this->countries ) foreach ( $this->countries as $key=>$value) :
			if ( $states =  $this->get_states($key) ) :
				echo '<optgroup label="'.$value.'">';
    				foreach ($states as $state_key=>$state_value) :
    					echo '<option value="'.$key.':'.$state_key.'"';

    					if ($selected_country==$key && $selected_state==$state_key) echo ' selected="selected"';

    					echo '>'.$value.' &mdash; '. ($escape ? esc_js($state_value) : $state_value) .'</option>';
    				endforeach;
    			echo '</optgroup>';
			else :
    			echo '<option';
    			if ($selected_country==$key && $selected_state=='*') echo ' selected="selected"';
    			echo ' value="'.$key.'">'. ($escape ? esc_js( $value ) : $value) .'</option>';
			endif;
		endforeach;
	}


	/**
	 * Outputs the list of countries and states for use in multiselect boxes.
	 *
	 * @access public
	 * @param string $selected_countries (default: '')
	 * @param bool $escape (default: false)
	 * @return void
	 */
	public function country_multiselect_options( $selected_countries = '', $escape = false ) {

		$countries = $this->get_allowed_countries();

		foreach ( $countries as $key => $val ) {

			echo '<option value="' . $key . '" ' . selected( isset( $selected_countries[ $key ] ) && in_array( '*', $selected_countries[ $key ] ), true, false ) . '>' . ( $escape ? esc_js( $val ) : $val ) . '</option>';

			if ( $states = $this->get_states( $key ) ) {
				foreach ($states as $state_key => $state_value ) {

	    			echo '<option value="' . $key . ':' . $state_key . '" ' . selected(  isset( $selected_countries[ $key ] ) && in_array( $state_key, $selected_countries[ $key ] ), true, false ) . '>' . ( $escape ? esc_js( $val . ' &gt; ' . $state_value ) : $val . ' &gt; ' . $state_value ) . '</option>';

	    		}
			}

		}
	}


	/**
	 * Get country address formats
	 *
	 * @access public
	 * @return array
	 */
	public function get_address_formats() {

		if (!$this->address_formats) :

			// Common formats
			$postcode_before_city = "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}";

			// Define address formats
			$this->address_formats = apply_filters('carton_localisation_address_formats', array(
				'default' => "{name}\n{company}\n{address_1}\n{address_2}\n{city}\n{state}\n{postcode}\n{country}",
				'AU' => "{name}\n{company}\n{address_1}\n{address_2}\n{city} {state} {postcode}\n{country}",
				'AT' => $postcode_before_city,
				'BE' => $postcode_before_city,
				'CH' => $postcode_before_city,
				'CN' => "{country} {postcode}\n{state}, {city}, {address_2}, {address_1}\n{company}\n{name}",
				'CZ' => $postcode_before_city,
				'DE' => $postcode_before_city,
				'FI' => $postcode_before_city,
				'DK' => $postcode_before_city,
				'FR' => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city_upper}\n{country}",
				'HK' => "{company}\n{first_name} {last_name_upper}\n{address_1}\n{address_2}\n{city_upper}\n{state_upper}\n{country}",
				'HU' => "{name}\n{company}\n{city}\n{address_1}\n{address_2}\n{postcode}\n{country}",
				'IS' => $postcode_before_city,
				'IS' => $postcode_before_city,
				'LI' => $postcode_before_city,
				'NL' => $postcode_before_city,
				'NZ' => "{name}\n{company}\n{address_1}\n{address_2}\n{city} {postcode}\n{country}",
				'NO' => $postcode_before_city,
				'PL' => $postcode_before_city,
				'SK' => $postcode_before_city,
				'SI' => $postcode_before_city,
				'ES' => "{name}\n{company}\n{address_1}\n{address_2}\n{postcode} {city}\n{state}\n{country}",
				'SE' => $postcode_before_city,
				'TR' => "{name}\n{company}\n{address_1}\n{address_2}\n{postcode} {city} {state}\n{country}",
				'US' => "{name}\n{company}\n{address_1}\n{address_2}\n{city}, {state} {postcode}\n{country}",
				'VN' => "{name}\n{company}\n{address_1}\n{city}\n{country}",
				'RU' => "{first_name} {last_name}\n{address_1_upper}, {city_upper}, {postcode}",
			));
		endif;

		return $this->address_formats;
	}


	/**
	 * Get country address format
	 *
	 * @access public
	 * @param array $args (default: array())
	 * @return string address
	 */
	public function get_formatted_address( $args = array() ) {

		$args = array_map( 'trim', $args );

		extract( $args );

		// Get all formats
		$formats 		= $this->get_address_formats();

		// Get format for the address' country
		$format			= ( $country && isset( $formats[ $country ] ) ) ? $formats[ $country ] : $formats['default'];

		// Handle full country name
		$full_country 	= ( isset( $this->countries[ $country ] ) ) ? $this->countries[ $country ] : $country;

		// Country is not needed if the same as base
		if ( $country == $this->get_base_country() )
			$format = str_replace( '{country}', '', $format );

		// Handle full state name
		$full_state		= ( $country && $state && isset( $this->states[ $country ][ $state ] ) ) ? $this->states[ $country ][ $state ] : $state;

		// Substitute address parts into the string
		$replace = apply_filters( 'carton_formatted_address_replacements', array(
			'{first_name}'       => $first_name,
			'{last_name}'        => $last_name,
			'{name}'             => $first_name . ' ' . $last_name,
			'{company}'          => $company,
			'{address_1}'        => $address_1,
			'{address_2}'        => $address_2,
			'{city}'             => $city,
			'{state}'            => $full_state,
			'{postcode}'         => $postcode,
			'{country}'          => $full_country,
			'{first_name_upper}' => strtoupper( $first_name ),
			'{last_name_upper}'  => strtoupper( $last_name ),
			'{name_upper}'       => strtoupper( $first_name . ' ' . $last_name ),
			'{company_upper}'    => strtoupper( $company ),
			'{address_1_upper}'  => strtoupper( $address_1 ),
			'{address_2_upper}'  => strtoupper( $address_2 ),
			'{city_upper}'       => strtoupper( $city ),
			'{state_upper}'      => strtoupper( $full_state ),
			'{postcode_upper}'   => strtoupper( $postcode ),
			'{country_upper}'    => strtoupper( $full_country ),
		) ) ;

		$replace = array_map( 'esc_html', $replace );

		$formatted_address = str_replace( array_keys( $replace ), $replace, $format );

		// Clean up white space
		$formatted_address = preg_replace( '/  +/', ' ', trim( $formatted_address ) );
		$formatted_address = preg_replace( '/\n\n+/', "\n", $formatted_address );

		// Add html breaks
		$formatted_address = nl2br( $formatted_address );

		// We're done!
		return $formatted_address;
	}


	/**
	 * Returns the fields we show by default. This can be filtered later on.
	 *
	 * @access public
	 * @return void
	 */
	public function get_default_address_fields() {
		$fields = array(
			'address_1' => array(
				'label' 		=> __( 'Address', 'carton' ),
				'placeholder'		=> __( 'Street address', 'carton' ),
				'required' 		=> true,
				'class' 		=> array( 'form-row-wide', 'address-field' ),
				),
			'address_2' => array(
				'placeholder' 	=> _x( 'Apartment, suite, unit etc. (optional)', 'placeholder', 'carton' ),
				'class' 		=> array( 'form-row-wide', 'address-field' ),
				'required' 	    => false
				),
			'city' => array(
				'label' 		=> __( 'Town / City', 'carton' ),
				'placeholder'	=> __( 'Town / City', 'carton' ),
				'required' 		=> true,
				'class' 		=> array( 'form-row-wide', 'address-field' ),
				),
			'state' => array(
				'type'			=> 'state',
				'label' 		=> __( 'State / County', 'carton' ),
				'placeholder' 	=> __( 'State / County', 'carton' ),
				'required' 		=> true,
				'class' 		=> array( 'form-row-wide', 'address-field' )
				),
			'postcode' => array(
				'label' 		=> __( 'Postcode / Zip', 'carton' ),
				'placeholder' 	=> __( 'Postcode / Zip', 'carton' ),
				'required' 		=> true,
				'class'			=> array( 'form-row-wide', 'address-field' ),
				'clear'			=> true
				),
			'country' => array(
				'type'			=> 'country',
				'label' 		=> __( 'Country', 'carton' ),
				'required' 		=> true,
				'class' 		=> array( 'form-row-wide', 'address-field', 'update_totals_on_change' ),
				),
			'company' => array(
				'label' 		=> __( 'Company Name', 'carton' ),
				'class' 		=> array( 'form-row-wide' ),
				),
			'first_name' => array(
				'label' 		=> __( 'First Name', 'carton' ),
				'required' 		=> true,
				'class'			=> array( 'form-row-wide' ),
				),
			'last_name' => array(
				'label' 		=> __( 'Last Name', 'carton' ),
				'required' 		=> true,
				'class' 		=> array( 'form-row-wide' ),
				'clear'			=> true
				),
			'phone' => array(
				'label' 		=> __( 'Phone', 'carton' ),
				'required' 		=> true,
				'class' 		=> array( 'form-row-wide' ),
				'clear'			=> true
			),
			'email' => array(
				'label' 		=> __( 'Email Address', 'carton' ),
				'required' 		=> true,
				'class' 		=> array( 'form-row-wide' ),
				'validate'		=> array( 'email' ),
			),
		);

		return apply_filters( 'carton_default_address_fields', $fields );
	}

	/**
	 * Get country locale settings
	 *
	 * @access public
	 * @return array
	 */
	public function get_country_locale() {
		if ( ! $this->locale ) {

			// Locale information used by the checkout
			$this->locale = apply_filters('carton_get_country_locale', array(
				'RU' => array(
					'postcode_after_city' => true,
					'state' => array( 'required' => false, 'label' => __( 'Region', 'carton' ) ),
				),
				'AF' => array(
					'state' => array(
					'required' => false,
					),
				),
				'AT' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'BE' => array(
					'postcode_before_city' => true,
					'state' => array(
						'required' => false,
						'label'    => __( 'Province', 'carton' ),
					),
				),
				'BI' => array(
					'state' => array(
						'required' => false,
					),
				),
				'CA' => array(
					'state'	=> array(
					'label'	=> __( 'Province', 'carton' ),
					)
				),
				'CH' => array(
					'postcode_before_city' => true,
					'state' => array(
						'label'         => __( 'Canton', 'carton' ),
						'required'      => false
					)
				),
				'CL' => array(
					'city'		=> array(
						'required' 	=> false,
					),
					'state'		=> array(
						'label'		=> __( 'Municipality', 'carton' ),
					)
				),
				'CN' => array(
					'state'	=> array(
					'label'			=> __( 'Province', 'carton' ),
					)
				),
				'CO' => array(
					'postcode' => array(
					'required' 	=> false
					)
				),
				'CZ' => array(
					'state'		=> array(
					'required' => false
					)
				),
				'DE' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'DK' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'FI' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'FR' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'HK' => array(
					'postcode'	=> array(
						'required' => false
					),
					'city'	=> array(
						'label'				=> __( 'Town / District', 'carton' ),
					),
					'state'		=> array(
						'label' 		=> __( 'Region', 'carton' ),
					)
				),
				'HU' => array(
					'state'		=> array(
						'required' => false
					)
				),
				'ID' => array(
	                'state' => array(
	                    'label'         => __( 'Province', 'carton' ),
	                )
            	),
				'IS' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'IL' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'KR' => array(
					'state'		=> array(
						'required' => false
					)
				),
				'NL' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false,
						'label'    => __( 'Province', 'carton' ),
					)
				),
				'NZ' => array(
					'state'		=> array(
						'required' => false
					)
				),
				'NO' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'PL' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'PT' => array(
					'state'		=> array(
						'required' => false
					)
				),
				'RO' => array(
					'state'		=> array(
						'required' => false
					)
				),
				'SG' => array(
					'state'		=> array(
						'required' => false
					)
				),
				'SK' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'SI' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'ES' => array(
					'postcode_before_city' => true,
					'state'	=> array(
						'label'			=> __( 'Province', 'carton' ),
					)
				),
				'LI' => array(
                    'postcode_before_city' => true,
                    'state' => array(
                        'label'         => __( 'Municipality', 'carton' ),
                        'required'      => false
                    )
                ),
				'LK' => array(
					'state'	=> array(
						'required' => false
					)
				),
				'SE' => array(
					'postcode_before_city' => true,
					'state'	=> array(
						'required' => false
					)
				),
				'TR' => array(
					'postcode_before_city' => true,
					'state'	=> array(
						'label'			=> __( 'Province', 'carton' ),
					)
				),
				'US' => array(
					'postcode'	=> array(
						'label' 		=> __( 'Zip', 'carton' ),
					),
					'state'		=> array(
						'label' 		=> __( 'State', 'carton' ),
					)
				),
				'GB' => array(
					'postcode'	=> array(
						'label' 		=> __( 'Postcode', 'carton' ),
					),
					'state'		=> array(
						'label' 		=> __( 'County', 'carton' ),
						'required' 		=> false
					)
				),
				'VN' => array(
					'state'		=> array(
						'required' => false
					),
					'postcode' => array(
						'required' 	=> false,
						'hidden'	=> true
					),
					'address_2' => array(
						'required' 	=> false,
						'hidden'	=> true
					)
				),
				'WS' => array(
					'postcode' => array(
						'required' 	=> false,
						'hidden'	=> true
					),
				),
				'ZA' => array(
					'state'	=> array(
						'label'			=> __( 'Province', 'carton' ),
					)
				),
				'ZW' => array(
					'postcode' => array(
						'required' 	=> false,
						'hidden'	=> true
					),
				),
			));

			$this->locale = array_intersect_key( $this->locale, $this->get_allowed_countries() );

			// Default Locale Can be filters to override fields in get_address_fields().
			// Countries with no specific locale will use default.
			$this->locale['default'] = apply_filters('carton_get_country_locale_default', $this->get_default_address_fields() );

			// Filter default AND shop base locales to allow overides via a single function. These will be used when changing countries on the checkout
			if ( ! isset( $this->locale[ $this->get_base_country() ] ) )
				$this->locale[ $this->get_base_country() ] = $this->locale['default'];

			$this->locale['default'] = apply_filters( 'carton_get_country_locale_base', $this->locale['default'] );
			$this->locale[ $this->get_base_country() ] = apply_filters( 'carton_get_country_locale_base', $this->locale[ $this->get_base_country() ] );
		}

		return $this->locale;
	}

	/**
	 * Apply locale and get address fields
	 *
	 * @access public
	 * @param mixed $country
	 * @param string $type (default: 'billing_')
	 * @return void
	 */
	public function get_address_fields( $country, $type = 'billing_' ) {
		$fields = $this->get_default_address_fields();
		$locale = $this->get_country_locale();

		if ( isset( $locale[ $country ] ) ) {

			$fields = carton_array_overlay( $fields, $locale[ $country ] );

			// If default country has postcode_before_city switch the fields round.
			// This is only done at this point, not if country changes on checkout.
			if ( isset( $locale[ $country ]['postcode_before_city'] ) ) {
				if ( isset( $fields['postcode'] ) ) {
					$fields['postcode']['class'] = array( 'form-row-wide', 'address-field' );

					$switch_fields = array();

					foreach ( $fields as $key => $value ) {
						if ( $key == 'city' ) {
							// Place postcode before city
							$switch_fields['postcode'] = '';
						}
						$switch_fields[$key] = $value;
					}

					$fields = $switch_fields;
				}
			}
		}

		// Prepend field keys
		$address_fields = array();

		foreach ( $fields as $key => $value ) {
			$address_fields[$type . $key] = $value;
		}

		$address_fields = apply_filters( 'carton_billing_fields', $address_fields, $country );
		$address_fields = apply_filters( 'carton_shipping_fields', $address_fields, $country );

/*
		// Billing/Shipping Specific
		if ( $type == 'billing_' ) {

			$address_fields['billing_email'] = array(
				'label' 		=> __( 'Email Address', 'carton' ),
				'required' 		=> true,
				'class' 		=> array( 'form-row-first' ),
				'validate'		=> array( 'email' ),
			);
			$address_fields['billing_phone'] = array(
				'label' 		=> __( 'Phone', 'carton' ),
				'required' 		=> true,
				'class' 		=> array( 'form-row-last' ),
				'clear'			=> true
			);

			$address_fields = apply_filters( 'carton_billing_fields', $address_fields, $country );

		} elseif( $type == 'shipping_' ) {

			$address_fields['shipping_email'] = array(
				'label' 		=> __( 'Email Address', 'carton' ),
				'required' 		=> true,
				'class' 		=> array( 'form-row-first' ),
				'validate'		=> array( 'email' ),
			);
			$address_fields['shipping_phone'] = array(
				'label' 		=> __( 'Phone', 'carton' ),
				'required' 		=> true,
				'class' 		=> array( 'form-row-last' ),
				'clear'			=> true
			);

			$address_fields = apply_filters( 'carton_shipping_fields', $address_fields, $country );
		}
*/
		// Return
		return $address_fields;
	}
}