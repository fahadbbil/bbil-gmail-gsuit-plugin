<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://blubirdinteractive.com/
 * @since      1.0.0
 *
 * @package    Bbil_Gmail_Gsuit
 * @subpackage Bbil_Gmail_Gsuit/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Bbil_Gmail_Gsuit
 * @subpackage Bbil_Gmail_Gsuit/includes
 * @author     BBIL <info@blubirdinteractive.com>
 */
class Bbil_Gmail_Gsuit_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'bbil-gmail-gsuit',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
