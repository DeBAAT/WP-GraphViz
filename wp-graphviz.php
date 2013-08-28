<?php
/**
 * The WordPress GraphViz Plugin.
 *
 * A plugin to provide GraphViz functionality for WordPress sites
 *
 * @package   WP_GraphViz
 * @author    Jan de Baat <WP_GraphViz@de-baat.nl>
 * @license   GPL-2.0+
 * @link      http://www.de-baat.nl/WP_Graphviz
 * @copyright 2013 De B.A.A.T.
 *
 * @wordpress-plugin
 * Plugin Name: WP-GraphViz
 * Plugin URI:  TODO
 * Description: A plugin to provide GraphViz functionality for WordPress sites
 * Version:     0.1.0
 * Author:      Jan de Baat
 * Author URI:  http://www.de-baat.nl/WP_Graphviz
 * Text Domain: wp-graphviz-locale
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WP_GRAPHVIZ_LINK',					'http://www.de-baat.nl/WP_Graphviz' );
define( 'WP_GRAPHVIZ_VERSION', 				'0.1.0' );
define( 'WP_GRAPHVIZ_OPTIONS_NAME', 		'wp-graphviz-options' ); // Option name for save settings

define( 'WP_GRAPHVIZ_URL', plugins_url('', __FILE__) );
define( 'WP_GRAPHVIZ_DIR', rtrim(plugin_dir_path(__FILE__), '/') );
define( 'WP_GRAPHVIZ_BASENAME', dirname(plugin_basename(__FILE__)) );

define( 'WPG_PLUGIN', 'wp-graphviz' );

require_once( WP_GRAPHVIZ_DIR . '/wp-graphviz-functions.php' );
require_once( WP_GRAPHVIZ_DIR . '/classes/class-wp-graphviz-plugin.php' );
require_once( WP_GRAPHVIZ_DIR . '/classes/class-wp-graphviz-shortcodes.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'WP_GraphViz_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WP_GraphViz_Plugin', 'deactivate' ) );

// Create the plugin object
global $WP_GraphViz_Object;
$WP_GraphViz_Object = WP_GraphViz_Plugin::get_instance();

