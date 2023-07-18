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
 * Currently plugin version.Start at version 0.0.1
 */
define( 'RSHUB_VERSION', '0.0.1' );




function activate_rshub() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-rshub-activator.php';
	Rshub_Activator::activate();

    add_role(
        'contractor',
        'Contractor',
        array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'publish_posts' => false,
            'upload_files' => true,
        )
    );

    add_role(
        'homeowner',
        'Homeowner',
        array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'publish_posts' => false,
            'upload_files' => true,
        )
    );

}

function deactivate_rshub() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-rshub-deactivator.php';
	Rshub_Deactivator::deactivate();
}

function rshub_install() {
    global $wpdb;
    $rshub_searches = $wpdb->prefix . 'rshub_searches';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $rshub_searches (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        search_query longtext NOT NULL,
        search_results longtext NOT NULL,
        search_geolocation longtext NOT NULL,
        search_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    $rshub_leads = $wpdb->prefix . 'rshub_leads';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $rshub_leads (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        lead_name tinytext NOT NULL,
        lead_email longtext NOT NULL,
        lead_phone longtext NOT NULL,
        lead_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


register_activation_hook( __FILE__, 'rshub_install' );
register_activation_hook( __FILE__, 'activate_rshub' );
register_deactivation_hook( __FILE__, 'deactivate_rshub' );

require plugin_dir_path(__FILE__) . 'includes/class-rshub.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.0.1
 */
function run_rshub() {

	$plugin = new Rshub();
	$plugin->run();

}

run_rshub();