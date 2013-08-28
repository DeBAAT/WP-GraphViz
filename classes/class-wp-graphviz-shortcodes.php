<?php
/**
 * WP_GraphViz Shortcodes class.
 *
 * @package   WP_GraphViz
 * @author    Jan de Baat <WP_GraphViz@de-baat.nl>
 * @license   GPL-2.0+
 * @link      http://www.de-baat.nl/WP_Graphviz
 * @copyright 2013 De B.A.A.T.
 */

class WP_GraphViz_Shortcodes {

	var $option_page, $page_title, $menu_title, $capability, $menu_slug, $version, $count;

	function __construct() {
		if (!defined('WP_GRAPHVIZ_SHORTCODES_VERSION')) {
			define('WP_GRAPHVIZ_SHORTCODES_VERSION', '0.1.0');
		}

		// Set some variables
		$this->page_title = 'WP GraphViz';
		$this->menu_title = __('WP GraphViz Shortcodes', WPG_PLUGIN);
		$this->capability = 'edit_theme_options';
		$this->menu_slug = 'wp-graphviz-shortcodes';
		$this->version = WP_GRAPHVIZ_SHORTCODES_VERSION;
		$this->count = 1;

		add_action('admin_init', array(&$this, 'admin_init'));

		// Define the wp_graphviz_shortcodes, including version independent of upper or lower case
		$wp_graphviz_shortcodes = $this->get_wp_graphviz_shortcodes();
		foreach ($wp_graphviz_shortcodes as $shortcode) {
			$shortcode_lc = strtolower($shortcode['label']);
			$shortcode_uc = strtoupper($shortcode['label']);
			add_shortcode($shortcode['label'], array($this, $shortcode['function']));
			add_shortcode($shortcode_lc, array($this, $shortcode['function']));
			add_shortcode($shortcode_uc, array($this, $shortcode['function']));
		}

		// Add a menu entry to the WP_GraphViz plugin menu
        add_filter('add_wp_graphviz_menu_items',array($this,'add_menu_items'),90);

	}

    /**
     * Add the shortcode menu for this page
     *
     * @param mixed[] $menuItems
     * @return mixed[]
     */
    function add_menu_items($menuItems) {
        return array_merge(
                    $menuItems,
                    array(
                        array(
                            'label'     => $this->menu_title,
                            'slug'      => $this->menu_slug,
                            'class'     => $this,
                            'function'  => 'render_options'
                        ),
                    )
                );
    }

    /**
     * Get all shortcodes defined for WP GraphViz
     *
     * @return $shortcodes[]
     */
    function get_wp_graphviz_shortcodes() {
        return array (
					array(
						'label'       => 'WP_GraphViz',
						'description' => __('The basic shortcode to render a graph specified in the DOT language.', WPG_PLUGIN),
						'class'       => $this,
						'function'    => 'wp_graphviz_shortcode'
					)
				);
    }

	function add_admin_scripts($hook) {
		if ($hook == $this->option_page) {
			if (is_admin()) {
				wp_enqueue_style('wp-graphviz-shortcodes-admin', plugins_url('css/admin.css', dirname(__FILE__)), array(), $this->version);
				wp_enqueue_style('wp-graphviz-shortcodes-admin-dosis', 'http://fonts.googleapis.com/css?family=Dosis', array(), $this->version);
			}
		}
	}

	function render_options() {
		
		$render_options_output = '';
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php echo __('Welcome to WP GraphViz Shortcodes', WPG_PLUGIN); ?></h2>
			<?php
			if (isset($_REQUEST['settings-updated'])) {
				?>
				<div id="sip-return-message" class="updated"><?php echo __('Your Settings have been saved.', WPG_PLUGIN); ?></div>
				<?php
			}
			?>
			<p>
				<?php echo __('This page shows the shortcodes provided by the WP GraphViz plugin:', WPG_PLUGIN); ?>
			</p>
			<div id='wp_graphviz_table_wrapper'>
			<table id='wp_graphviz_shortcodes_table' class='wp-graphviz wp-list-table widefat fixed posts' cellspacing="0">

			<thead>
				<tr>
					<th class="manage-column sortable"><br/><code>[SHORTCODE]</code><br/>&nbsp;</th>
					<th class="manage-column sortable"><br/><?php echo __('Description', WPG_PLUGIN); ?><br/>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$row_style = 'even';
				$wp_graphviz_shortcodes = $this->get_wp_graphviz_shortcodes();
				foreach ($wp_graphviz_shortcodes as $shortcode) {
					$row_style = ($row_style == 'odd') ? 'even' : 'odd';
					$render_options_output = '';
					$render_options_output .= '<tr class="wp_graphviz_shortcodes_row ' . $row_style . '">';
					$render_options_output .= '<td class="wp_graphviz_shortcodes_cell"><code>[' . $shortcode['label'] . ']</code></td>';
					$render_options_output .= '<td class="wp_graphviz_shortcodes_cell">' . $shortcode['description'] . '</td>';
					$render_options_output .= '</tr>';
					echo $render_options_output;
				}
				?>
			</tbody>
			</table>
		</div>
		<p>
			<?php echo sprintf (__('For usage information see <a href="%s">here</a>.', WPG_PLUGIN), WP_GRAPHVIZ_LINK); ?>
		</p>

	</div>
	<?php
	}

	function add_scripts() {
		// No scripts needed
	}

	function admin_init() {
		// No admin_init needed
	}


	/**
	 * Generates an image specified in the DOT language. The short code takes the following arguments:
	 *  - image_caption: the caption of the image generated
	 *
	 * @param  $attr
	 * @return string
	 */
	function wp_graphviz_shortcode($attr, $content) {

		$wpg_shortcode_output = '';
		$wpg_graph_doc = '';
		$wpg_graph_spec = '';
		global $WP_GraphViz_Object;

		// Get the shortcode_attributes
		$wpg_atts = shortcode_atts(array(
			'id' => 'wp_graphviz_'.($this->count++),
			'type' => false,
			'graph' => '',
			'lang' => 'dot',
			'simple' => false,
			'output' => 'svg',
			'imap' => false,
			'href' => false,
			'title' => '',
			'showdot' => false,
		), $attr);


		$WP_GraphViz_Object->debugMP('pr','WP GraphViz wp_graphviz_dot_shortcode attributes',$attr,__FILE__,__LINE__);
		$WP_GraphViz_Object->debugMP('pr','WP GraphViz wp_graphviz_dot_shortcode attributes',$wpg_atts,__FILE__,__LINE__);
		$WP_GraphViz_Object->debugMP('msg','WP GraphViz wp_graphviz_dot_shortcode dot parameter',esc_html($content),__FILE__,__LINE__);

		// Set some variables
		$wpg_div_id = 'wpg_div_' . $wpg_atts['id'];
		$wpg_id = 'wpg_' . $wpg_atts['id'];

		// Get the dot specification of the graph
		$wpg_graph_dot = preg_replace(array('#<br\s*/?>#i', '#</?p>#i'), ' ', $content);

		$wpg_graph_dot = str_replace(
			array('&lt;', '&gt;', '&quot;', '&#8220;', '&#8221;', '&#039;', '&#8125;', '&#8127;', '&#8217;', '&#038;', '&amp;', "\n", "\r", "\xa0", '&#8211;'),
			array('<',    '>',    '"',      '"',       '"',       "'",      "'",       "'",       "'",       '&',      '&',     '',   '',   '-',    ''),
			$wpg_graph_dot
		);

		// Build the dot specification of the graph when not complete
		if($wpg_atts['simple']) { # emulate eht-graphviz
			$wpg_atts['type'] = 'digraph';
		} 
		if ($wpg_atts['type']) {
			$wpg_graph_dot = $wpg_atts['type'] . " " . $wpg_id . " {" . $wpg_graph_dot . "}";
		}

		// Build the script to generate the graph
		$wpg_graph_spec .= '<script type="text/vnd.graphviz" id="' . $wpg_id . '">';
			$wpg_graph_spec .= $wpg_graph_dot;
		$wpg_graph_spec .= '</script>';

		// Build the script to generate the graph and replace the placeholder div with the graph itself
		$wpg_graph_doc .= '<script>';
			$wpg_graph_doc .= $wpg_div_id . '.innerHTML = createViz("' . $wpg_id . '", "svg");';

			// Check value to showdot
			if (wpg_string_to_bool($wpg_atts['showdot'])) {
				$wpg_graph_doc .= $wpg_div_id . '.innerHTML += "' . __('<h4>DOT specification for graph id = ', WPG_PLUGIN) . $wpg_id . '.</h4>";';
				$wpg_graph_doc .= $wpg_div_id . '.innerHTML += "<blockquote>' . esc_html($wpg_graph_spec) . '</blockquote>";';
			}
		$wpg_graph_doc .= '</script>';

		// Build the placeholder and scripts to display the graph
		if ($wpg_atts['title']) {
			$wpg_shortcode_output .= '<h2>' . $wpg_atts['title'] . '</h2>';
		}
		$wpg_shortcode_output .= '<div id=' . $wpg_div_id . '>' . $wpg_div_id . '</div>';
		$wpg_shortcode_output .= $wpg_graph_spec;
		$wpg_shortcode_output .= $wpg_graph_doc;

		//$wpg_shortcode_output = 'TESTING wp_graphviz_dot_shortcode<br/>';

		$WP_GraphViz_Object->debugMP('msg','WP GraphViz wp_graphviz_shortcode shortcode_output',esc_html($wpg_shortcode_output),__FILE__,__LINE__);

		return $wpg_shortcode_output;
	}

}

add_action('init', 'init_wp_graphviz_shortcodes');
function init_wp_graphviz_shortcodes() {
	global $WP_GraphViz_Shortcodes;
	$WP_GraphViz_Shortcodes = new WP_GraphViz_Shortcodes();
}
