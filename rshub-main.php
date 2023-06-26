<?php

/**
 * @link              https://roofingsidinghub.com
 * @since             0.0.1
 * @package           rshub
 *
 *Plugin Name: Rshub
 *Plugin URI: https://www.roofingsidinghub.com/
 *Description: The best wordpress plugin for sending bulk SMS to high rated contractors near you. 
 *Version:  0.0.1
 * Author: fungicoder | roofingsidinghub.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 **/



// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 0.0.1
 */
define( 'Rshub', '0.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_rshub() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/rshub-activator.php';
	Rshub_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_rshub() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/rshub-deactivator.php';
	Rshub_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_rshub' );
register_deactivation_hook( __FILE__, 'deactivate_rshub' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/rshub.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_rshub() {

	$plugin = new Rshub();
	$plugin->run();

}
run_rshub();