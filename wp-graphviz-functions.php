<?php
/**
 * WP-GraphViz Plugin.
 *
 * @package   WP_GraphViz
 * @author    Jan de Baat <WP_GraphViz@de-baat.nl>
 * @license   GPL-2.0+
 * @link      http://www.de-baat.nl/WP_Graphviz
 * @copyright 2013 De B.A.A.T.
 */

 
function wpg_get_option($option_key = '') {
	$wp_graphviz_options = get_option('wp_graphviz_options');
	return isset( $wp_graphviz_options[$option_key] ) ? $wp_graphviz_options[$option_key] : false;
}

function wpg_update_option($option_key = '', $option_value = '') {
	$wp_graphviz_options = get_option('wp_graphviz_options');
	if ( isset( $wp_graphviz_options[$option_key] ) ) {
		$wp_graphviz_options[$option_key] = $option_value;
	}
	return update_option('wp_graphviz_options', $wp_graphviz_options);
}
