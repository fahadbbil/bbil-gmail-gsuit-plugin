<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://blubirdinteractive.com/
 * @since             1.0.0
 * @package           Bbil_Gmail_Gsuit
 *
 * @wordpress-plugin
 * Plugin Name:       BBIL-Gmail-Gsuit
 * Plugin URI:        https://blubirdinteractive.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            BBIL
 * Author URI:        https://blubirdinteractive.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bbil-gmail-gsuit
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BBIL_GMAIL_GSUIT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bbil-gmail-gsuit-activator.php
 */
function activate_bbil_gmail_gsuit() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bbil-gmail-gsuit-activator.php';
	Bbil_Gmail_Gsuit_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bbil-gmail-gsuit-deactivator.php
 */
function deactivate_bbil_gmail_gsuit() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bbil-gmail-gsuit-deactivator.php';
	Bbil_Gmail_Gsuit_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bbil_gmail_gsuit' );
register_deactivation_hook( __FILE__, 'deactivate_bbil_gmail_gsuit' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bbil-gmail-gsuit.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bbil_gmail_gsuit() {

	$plugin = new Bbil_Gmail_Gsuit();
	$plugin->run();

}
run_bbil_gmail_gsuit();

// Class files load
$classes = glob(plugin_dir_path( __FILE__ ).'class/autoload/*.php');
if ($classes) {
    foreach ($classes as $class) {
        require_once $class;
    }
}

