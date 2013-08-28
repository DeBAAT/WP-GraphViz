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

/**
 * WP_GraphViz Shortcodes class.
 *
 * @package   WP_GraphViz_Plugin
 * @author    Jan de Baat <jan.de.baat@alten.nl>
 */

class WP_GraphViz_Shortcodes {

	var $option_page, $page_title, $menu_title, $capability, $menu_slug, $version;

	function __construct() {
		if (!defined('WP_GRAPHVIZ_SHORTCODES_VERSION')) {
			define('WP_GRAPHVIZ_SHORTCODES_VERSION', '0.1.0');
		}

		// Set some variables
		$this->page_title = 'WP GraphViz';
		$this->menu_title = 'WP GraphViz Shortcodes';
		$this->capability = 'edit_theme_options';
		$this->menu_slug = 'wp-graphviz-shortcodes';
		$this->version = WP_GRAPHVIZ_SHORTCODES_VERSION;

		add_action('admin_init', array(&$this, 'admin_init'));

		//add_shortcode('wp-graphviz-categories', array(&$this, 'list_categories'));
		//add_shortcode('wp-graphviz-the-post', array(&$this, 'the_post'));
		$wp_graphviz_shortcodes = $this->get_wp_graphviz_shortcodes();
		foreach ($wp_graphviz_shortcodes as $shortcode) {
			$shortcode_lc = strtolower($shortcode['label']);
			$shortcode_uc = strtoupper($shortcode['label']);
			add_shortcode($shortcode['label'], array($this, $shortcode['function']));
			add_shortcode($shortcode_lc, array($this, $shortcode['function']));
			add_shortcode($shortcode_uc, array($this, $shortcode['function']));
		}

		global $wp_graphviz_shortcode_options;
		$wp_graphviz_shortcode_options = get_option('wp_graphviz_shortcode_options');
		if (!isset($wp_graphviz_shortcode_options) || !is_array($wp_graphviz_shortcode_options)) {
			$wp_graphviz_shortcode_options = array();
		}
		$wp_graphviz_shortcode_options = array_merge(
			array(
				'adhoc_wareas' => 5,
				'adhoc_column_counts' => array(
					1 => 1,
					2 => 1,
					3 => 1,
					4 => 1,
					5 => 1,
				)
			),
			$wp_graphviz_shortcode_options
		);

		// Add a menu entry to the WP_GraphViz plugin menu
        add_filter('add_wp_graphviz_menu_items',array($this,'add_menu_items'),90);

	}

    /**
     * Add the Pro Pack menu
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
                        )
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
						'label'       => 'GraphViz',
						'description' => __('The basic shortcode to render a graph specified in the DOT language.', 'wp-graphviz'),
						'class'       => $this,
						'function'    => 'wpg_shortcode_graphviz'
					),
					array(
						'label'       => 'GraphViz_File',
						'description' => __('The shortcode to render a file containing the graph specified in the DOT language.', 'wp-graphviz'),
						'class'       => $this,
						'function'    => 'wpg_shortcode_graphviz_file'
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
		global $wp_graphviz_shortcode_options;
		?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2>Welcome to WP GraphViz Shortcodes</h2>
		<?php
		if (isset($_REQUEST['settings-updated'])) {
			?>
			<div id="sip-return-message" class="updated">Your Settings have been saved.</div>
			<?php
		}
		?>
		<p>
			This plugin makes available all the shortcodes from the WP GraphViz plugin. If you decide to stop using WP GraphViz in the future, this plugin will ensure that your content is not broken. The following are the shortcodes provided:
		</p>
		<div id='wp_graphviz_table_wrapper'>
		<table id='wp_graphviz_shortcodes_table' class='wp-graphviz wp-list-table widefat fixed posts' cellspacing="0">
		<?php
			echo '<thead>';
			echo '<tr>';
			echo '<th class="manage-column sortable"><br/><code>[SHORTCODE]</code><br/>&nbsp;</th>';
			echo '<th class="manage-column sortable"><br/>Description<br/>&nbsp;</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			$row_style = 'even';
			$wp_graphviz_shortcodes = $this->get_wp_graphviz_shortcodes();
			foreach ($wp_graphviz_shortcodes as $shortcode) {
				$row_style = ($row_style == 'odd') ? 'even' : 'odd';
				echo '<tr class="wp_graphviz_shortcodes_row ' . $row_style . '">';
				echo '<td class="wp_graphviz_shortcodes_cell"><code>[' . $shortcode['label'] . ']</code></td>';
				echo '<td class="wp_graphviz_shortcodes_cell">' . $shortcode['description'] . '</td>';
				echo '</tr>';
			}
			echo '</tbody>';
		?>
		</table>
		</div>
		<p>
			For usage information see <a href="http://aquoid.com/news/themes/wp-graphviz/shortcodes-to-enhance-functionality/">here</a>.
		</p>

		<fieldset>
			<legend>Shortcode Settings</legend>
			<form method="post" name="shortcode_settings_form" id="shortcode_settings_form" action="options.php">
				<h3>Ad Hoc Widgets</h3>
				<p>
					WP GraphViz lets you insert ad hoc widgets into your content using the <code>[wp-graphviz-widgets]</code> shortcode.
					By default this comes with 5 ad hoc widget areas.
				</p>
				<?php
				$adhoc_count = apply_filters('wp_graphviz_adhoc_count', 5);
				if ($this->check_integer($wp_graphviz_shortcode_options['adhoc_wareas'])) {
					$adhoc_count = (int)$wp_graphviz_shortcode_options['adhoc_wareas'];
				}
				?>
				<p>
					<label>
						Number of ad hoc widget areas
						<input type="text" name="wp_graphviz_shortcode_options[adhoc_wareas]" value="<?php echo $adhoc_count; ?>" />
					</label>
				</p>
				<?php
				for ($i = 1; $i <= $adhoc_count; $i++) {
					$columns = 1;
					if (is_array($wp_graphviz_shortcode_options['adhoc_column_counts']) && isset($wp_graphviz_shortcode_options['adhoc_column_counts'][$i]) &&
						$this->check_integer($wp_graphviz_shortcode_options['adhoc_column_counts'][$i])) {
						$columns = $wp_graphviz_shortcode_options['adhoc_column_counts'][$i];
					}
					if ($columns < 1) {
						$columns = 1;
					}
					else if ($columns > 5) {
						$columns = 5;
					}
					?>
				<p>
					<label>
						Number of columns in ad hoc widget area <?php echo $i; ?>
						<input type="text" name="wp_graphviz_shortcode_options[adhoc_column_counts][<?php echo $i; ?>]" value="<?php echo $columns; ?>" />
					</label>
				</p>
					<?php
				}

				settings_fields('wp_graphviz_shortcode_options');
				?>
				<input type="submit" name="Submit" class="button" value="Save" />
			</form>
		</fieldset>
	</div>
	<?php
	}

	function add_scripts() {
		if (!is_admin()) {
			global $wp_graphviz_shortcode_options;
			if (!defined('WP_GRAPHVIZ_THEME_VERSION')) {
				wp_enqueue_style('wp-graphviz-shortcodes', plugins_url('include/css/wp-graphviz-shortcodes.css', __FILE__), array(), WP_GRAPHVIZ_SHORTCODES_VERSION);
			}
		}
	}

	function admin_init() {
		register_setting('wp_graphviz_shortcode_options', 'wp_graphviz_shortcode_options', array(&$this, 'validate_options'));
	}

	function check_integer($val) {
		if (substr($val, 0, 1) == '-') {
			$val = substr($val, 1);
		}
		return (preg_match('/^\d*$/', $val) == 1);
	}

	/**
	 * Validation function for the Settings API.
	 *
	 * @param $options
	 * @return array
	 */
	function validate_options($options) {
		foreach ($options as $option => $option_value) {
			if (!is_array($option_value)) {
				$options[$option] = esc_attr($option_value);
			}
			else {
				foreach ($option_value as $inner_option => $inner_option_value) {
					$options[$option][$inner_option] = esc_attr($inner_option_value);
				}
			}
		}
		return $options;
	}

	function list_categories($attr) {
		if (isset($attr['title_li'])) {
			$attr['title_li'] = $this->shortcode_string_to_bool($attr['title_li']);
		}
		if (isset($attr['hierarchical'])) {
			$attr['hierarchical'] = $this->shortcode_string_to_bool($attr['hierarchical']);
		}
		if (isset($attr['use_desc_for_title'])) {
			$attr['use_desc_for_title'] = $this->shortcode_string_to_bool($attr['use_desc_for_title']);
		}
		if (isset($attr['hide_empty'])) {
			$attr['hide_empty'] = $this->shortcode_string_to_bool($attr['hide_empty']);
		}
		if (isset($attr['show_count'])) {
			$attr['show_count'] = $this->shortcode_string_to_bool($attr['show_count']);
		}
		if (isset($attr['show_last_update'])) {
			$attr['show_last_update'] = $this->shortcode_string_to_bool($attr['show_last_update']);
		}
		if (isset($attr['child_of'])) {
			$attr['child_of'] = (int)$attr['child_of'];
		}
		if (isset($attr['depth'])) {
			$attr['depth'] = (int)$attr['depth'];
		}
		$attr['echo'] = false;

		$output = wp_list_categories($attr);

		return $output;
	}

	function the_post($attr) {
		global $post;
		$id = $post->ID;
		if (isset($attr['display'])) {
			$display = $attr['display'];
			if ($id) {
				switch ($display) {
					case 'id':
						return $id;
					case 'title':
						return get_the_title($id);
					case 'permalink':
						return get_permalink($id);
					default:
						return get_the_title($id);
				}
			}
		}
		else {
			return get_the_title($id);
		}
		return "";
	}

	/**
	 * Creates an ad hoc widget area based on parameters passed to it. To use this feature you have to add widgets to the corresponding
	 * Ad Hoc widget areas in your administration panel. The syntax for this short code is [wp-graphviz-widgets id='2' container='false' class='some-class'].
	 * The 'id' refers to the index of the ad hoc widget area and can be anything from 1 to 5.
	 * The 'container' parameter, if set to false, will not put the widgets in a container. Otherwise the container will have the id "ad-hoc-[id]", where [id] is the id that you passed.
	 * The 'container-class' parameter assigns the passed class to the container. If the 'container' parameter is false then this is ignored.
	 *
	 * @param  $attr
	 * @return string
	 */
	function widget_area($attr) {
		$id = 1;
		if (isset($attr['id'])) {
			$id = (int)$attr['id'];
		}
		$container = isset($attr['container']) ? (bool)$attr['container'] : true;
		$sidebar_class = isset($attr['container_class']) ? $attr['container_class'] : "";
		ob_start(); // Output buffering is needed here otherwise there is no way to get the dynamic_sidebar output added to existing text
		if ($container) echo "<div id='ad-hoc-$id' class='$sidebar_class warea'>\n";
		dynamic_sidebar("Ad Hoc Widgets $id");
		if ($container) echo "</div>\n";
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	/**
	 * Prints a Flickr stream. The short code takes the following arguments:
	 *  - id: Mandatory, can be obtained from http://idgettr.com using your photo stream's URL
	 *  - type: Mandatory. Legitimate values: user, group.
	 *  - size: Optional. Values: s (square), t (thumbnail), m (mid-size). Default: s
	 *  - number: Optional. Default: 10
	 *  - order: Optional. Values: latest, random. Default: latest
	 *  - layout: Optional. Values: h (horizontal), v (vertical), x (no layout - user-styled). Default: x
	 *
	 * @param  $attr
	 * @return string
	 */
	function flickr($attr) {
		if (!isset($attr['id']) || !isset($attr['type'])) {
			return "";
		}
		$id = $attr['id'];
		$type = $attr['type'];
		$size = isset($attr['size']) ? $attr['size'] : 's';
		$number = isset($attr['number']) ? $attr['number'] : 10;
		$order = isset($attr['order']) ? $attr['order'] : 'latest';
		$layout = isset($attr['layout']) ? $attr['layout'] : 'x';

		return "<div class='suf-flickr-stream'><script type=\"text/javascript\" src=\"http://www.flickr.com/badge_code_v2.gne?count=$number&amp;display=$order&amp;size=$size&amp;layout=$layout&amp;source=$type&amp;$type=$id\"></script></div>";
	}

	function shortcode_string_to_bool($value) {
		if ($value == true || $value == 'true' || $value == 'TRUE' || $value == '1') {
			return true;
		}
		else if ($value == false || $value == 'false' || $value == 'FALSE' || $value == '0') {
			return false;
		}
		else {
			return $value;
		}
	}


	/**
	 * Generates an image specified in the DOT language. The short code takes the following arguments:
	 *  - image_caption: the caption of the image generated
	 *
	 * @param  $attr
	 * @return string
	 */
	function wpg_shortcode_graphviz($attr) {
		if (!isset($attr['image_caption'])) {
			return "";
		}
		$image_caption = isset($attr['image_caption']) ? $attr['image_caption'] : '';

		return "<div class='wp-graphviz-image'><img /></div>";
	}

	/**
	 * Generates an image specified in the DOT language. The short code takes the following arguments:
	 *  - image_caption: the caption of the image generated
	 *  - filename: the name of the file containing the graph specification
	 *
	 * @param  $attr
	 * @return string
	 */
	function wpg_shortcode_graphviz_file($attr) {
		if (!isset($attr['image_caption']) || !isset($attr['filename'])) {
			return "";
		}
		$image_caption = isset($attr['image_caption']) ? $attr['image_caption'] : '';
		$filename = $attr['filename'];

		return "<div class='wp-graphviz-image'><img /></div>";
	}

}

add_action('init', 'init_wp_graphviz_shortcodes');
function init_wp_graphviz_shortcodes() {
	global $WP_GraphViz_Shortcodes;
	$WP_GraphViz_Shortcodes = new WP_GraphViz_Shortcodes();
}
