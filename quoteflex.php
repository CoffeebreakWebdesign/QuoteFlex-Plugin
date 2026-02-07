<?php
/**
 * Plugin Name:       QuoteFlex
 * Plugin URI:        https://quoteflex.io
 * Description:       Flexible quote management for WordPress - search, import, organize and display inspiring quotes with quote sets.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            QuoteFlex Team
 * Author URI:        https://quoteflex.io
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       quoteflex
 * Domain Path:       /languages
 *
 * @package QuoteFlex
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'QUOTEFLEX_VERSION', '1.0.0' );

/**
 * Plugin directory path.
 */
define( 'QUOTEFLEX_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 */
define( 'QUOTEFLEX_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin basename.
 */
define( 'QUOTEFLEX_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Database version for schema updates.
 */
define( 'QUOTEFLEX_DB_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 */
function activate_quoteflex() {
	require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-activator.php';
	QuoteFlex_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_quoteflex() {
	require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-deactivator.php';
	QuoteFlex_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_quoteflex' );
register_deactivation_hook( __FILE__, 'deactivate_quoteflex' );

/**
 * The core plugin class.
 */
require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-quoteflex.php';

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 */
function run_quoteflex() {
	$plugin = new QuoteFlex();
	$plugin->run();
}

run_quoteflex();
