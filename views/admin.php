<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * WP-GraphViz Plugin.
 *
 * @package   WP_GraphViz
 * @author    Jan de Baat <WP_GraphViz@de-baat.nl>
 * @license   GPL-2.0+
 * @link      http://www.de-baat.nl/WP_Graphviz
 * @copyright 2013 De B.A.A.T.
 */
?>
<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form method="post" action="options.php">
	
	<?php
		settings_fields( 'wp_graphviz_option_group' );
		do_settings_sections( 'wp-graphviz-setting-admin' );
	?>
	
		<?php submit_button(); ?>
	</form>

	<!-- TODO: Provide markup for your options page here. -->
	<h2>Test output of the WP GraphViz admin page!</h2>
	<p>Value of option wp_graphviz_id = <?php echo wpg_get_option( 'wp_graphviz_id' ); ?></p>
	<p>Value of option wp_graphviz_title = <?php echo wpg_get_option( 'wp_graphviz_title' ); ?></p>
	<p>Value of option wp_graphviz_debug_out = <?php 
		echo wpg_get_option( 'wp_graphviz_debug_out' );
		echo '<br/>DEBUG: wp_graphviz_title wp_settings_fields:<br/><br/>';

		global $wp_settings_fields;
		global $WP_GraphViz_Object;
		$wp_graphviz_options = get_option('wp_graphviz_options');

		$WP_GraphViz_Object->debugMP('pr','WP GraphViz Admin page wp_graphviz_options',$wp_graphviz_options,__FILE__,__LINE__);
		$WP_GraphViz_Object->debugMP('pr','WP GraphViz Admin page wp_settings_fields',$wp_settings_fields['wp-graphviz-setting-admin'],__FILE__,__LINE__);

	?></p>
	
</div>
