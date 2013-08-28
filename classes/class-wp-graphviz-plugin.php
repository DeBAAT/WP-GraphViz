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
 * WP_GraphViz Plugin class.
 *
 * TODO: Rename this class to a proper name for your plugin.
 *
 * @package   WP_GraphViz_Plugin
 * @author    Jan de Baat <jan.de.baat@alten.nl>
 */
class WP_GraphViz_Plugin {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   0.1.0
	 *
	 * @var     string
	 */
	protected $version = '0.1.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    0.1.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'wp-graphviz';
	protected $plugin_icon = '';

	/**
	 * Instance of this class.
	 *
	 * @since    0.1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    0.1.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	static $options = null;

	// Some local variables
	var $option_page, $page_title, $menu_title, $capability, $menu_slug;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     0.1.0
	 */
	private function __construct() {

		// Set some variables
		$this->page_title = 'WP GraphViz';
		$this->menu_title = 'WP GraphViz';
		$this->capability = 'edit_theme_options';
		$this->menu_slug = 'wp-graphviz';
		$this->plugin_icon = WP_GRAPHVIZ_URL . '/assets/icon-wp-graphviz-18.png';

		// Load plugin text domain
		add_action( 'init', array( $this, 'wpg_init' ) );
		add_action( 'dmp_addpanel', array($this,'create_DMPPanels') );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'plugin_page_init' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.1.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    0.1.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		// No activation functionality needed
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    0.1.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		// No deactivation functionality needed
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.1.0
	 */
	public function wpg_init() {
		load_plugin_textdomain( WPG_PLUGIN, FALSE, WP_GRAPHVIZ_BASENAME . '/lang/' );

		$this->debugMP('msg','WP GraphViz Admin page wpg_init',WP_GRAPHVIZ_BASENAME . '/lang/',__FILE__,__LINE__);
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     0.1.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		wp_enqueue_style( $this->plugin_slug .'-admin-styles', WP_GRAPHVIZ_URL . '/css/admin.css', array(), $this->version );

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     0.1.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {
		// No admin scripts needed
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {
		// No public css needed
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-viz-public-script', WP_GRAPHVIZ_URL . '/js/viz-public.js', false, $this->version );
		wp_enqueue_script( $this->plugin_slug . '-viz-script', WP_GRAPHVIZ_URL . '/js/viz.js', false, $this->version );
	}

	public function print_general_section_info(){
		print __('Enter your general settings below:', WPG_PLUGIN);
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.1.0
	 */
	public function add_plugin_admin_menu() {
		$this->debugMP('msg','WP GraphViz Admin page add_plugin_admin_menu','Count(menuItems)=' . '...',__FILE__,__LINE__);

        if (current_user_can($this->capability)) {
			do_action('wpg_admin_menu_starting');

			// Get all menu items
            $menuItems = array();
            $menuItems = apply_filters('add_wp_graphviz_menu_items', $menuItems);

			// Check the number of submenu_pages to add
			$this->debugMP('msg','WP GraphViz Admin page add_plugin_admin_menu', 'Count(menuItems)=' . count($menuItems) . '...',__FILE__,__LINE__);
			if (count($menuItems) == 1) {
				$this->debugMP('pr','WP GraphViz Admin page add_plugin_admin_menu menuItems',$menuItems,__FILE__,__LINE__);
				// The main hook for the menu
				//
				//$menuitem = $menuItems[0];
				global $WP_GraphViz_Shortcodes;
				add_menu_page(
					$this->page_title,
					$this->menu_title,									//$menuItem['label'],
					$this->capability,
					$this->plugin_slug,									//$menuItem['slug'],
					array($WP_GraphViz_Shortcodes,'render_options'),	//array($menuItem['class'],$menuItem['function']),
					$this->plugin_icon
				);
				
			} else {
				
				// The main hook for the menu
				//
				add_menu_page(
					$this->page_title,
					$this->menu_title,
					$this->capability,
					$this->plugin_slug,
					array($this,'display_plugin_admin_page'),
					$this->plugin_icon
				);

				// Attach Menu Items To Sidebar and Top Nav
				//
				foreach ($menuItems as $menuItem) {

					// Using class names (or objects)
					//
					if (isset($menuItem['class'])) {
						add_submenu_page(
							$this->plugin_slug,
							$menuItem['label'],
							$menuItem['label'],
							$this->capability,
							$menuItem['slug'],
							array($menuItem['class'],$menuItem['function'])
							);

					// Full URL or plain function name
					//
					} else {
						add_submenu_page(
							$this->plugin_slug,
							$menuItem['label'],
							$menuItem['label'],
							$this->capability,
							$menuItem['url']
							);
					}
				}
			}

        }
    }

	/**
	 * Init the settings page for this plugin.
	 *
	 * @since    0.1.0
	 */
	public function plugin_page_init() {
		$this->debugMP('msg','WP GraphViz Admin page plugin_page_init',WP_GRAPHVIZ_BASENAME . '/lang/',__FILE__,__LINE__);
    }

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    0.1.0
	 */
	public function display_plugin_admin_page() {
		include_once( WP_GRAPHVIZ_DIR . '/views/admin.php' );
	}

    /**
     * Create a Map Settings Debug My Plugin panel.
     *
     * @return null
     */
    function create_DMPPanels() {
        if (!isset($GLOBALS['DebugMyPlugin'])) { return; }
        if (class_exists('DMPPanelWPGraphVizMain') == false) {
			require_once(dirname( __FILE__ ) . '/class.dmppanels.php');
        }
        $GLOBALS['DebugMyPlugin']->panels['wp-graphviz'] = new DMPPanelWPGraphVizMain();
    }

    /**
     * Add DebugMyPlugin messages.
     *
     * @param string $panel - panel name
     * @param string $type - what type of debugging (msg = simple string, pr = print_r of variable)
     * @param string $header - the header
     * @param string $message - what you want to say
     * @param string $file - file of the call (__FILE__)
     * @param int $line - line number of the call (__LINE__)
     * @param boolean $notime - show time? default true = yes.
     * @return null
     */
    function debugMP($type='msg', $header='Debug WP GraphViz',$message='',$file=null,$line=null,$notime=false) {

		$panel='wp-graphviz';

        // Panel not setup yet?  Return and do nothing.
        //
        if (
            !isset($GLOBALS['DebugMyPlugin']) ||
            !isset($GLOBALS['DebugMyPlugin']->panels[$panel])
           ) {
            return;
        }

        // Do normal real-time message output.
        //
        switch (strtolower($type)):
            case 'pr':
                $GLOBALS['DebugMyPlugin']->panels[$panel]->addPR($header,$message,$file,$line,$notime);
                break;
            default:
                $GLOBALS['DebugMyPlugin']->panels[$panel]->addMessage($header,$message,$file,$line,$notime);
        endswitch;
    }

}