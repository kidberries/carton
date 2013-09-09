<?php
/**
 * CartoN Integrations class
 *
 * Loads Integrations into CartoN.
 *
 * @class 		CTN_Integrations
 * @version		2.0.0
 * @package		CartoN/Classes/Integrations
 * @category	Class
 * @author 		CartonThemes
 */
class CTN_Integrations {

	/** @var array Array of integration classes */
	var $integrations = array();

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct() {

		do_action( 'carton_integrations_init' );

		$load_integrations = apply_filters( 'carton_integrations', array() );

		// Load integration classes
		foreach ( $load_integrations as $integration ) {

			$load_integration = new $integration();

			$this->integrations[$load_integration->id] = $load_integration;

		}

	}

	/**
	 * Return loaded integrations.
	 *
	 * @access public
	 * @return array
	 */
	public function get_integrations() {
		return $this->integrations;
	}
}