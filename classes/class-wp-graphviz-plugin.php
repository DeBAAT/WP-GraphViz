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
		$this->plugin_icon = WP_GRAPHVIZ_URL . '/assets/lightbulb.png';

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

		// Define custom functionality. Read more about actions and filters: http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		add_action( 'TODO', array( $this, 'action_method_name' ) );
		add_filter( 'TODO', array( $this, 'filter_method_name' ) );

		
		// Admin and XML-RPC
		//if ( is_admin() ) {
		//	require( WP_GRAPHVIZ_DIR . '/classes/class.admin.php' );
		//	new WP_GraphViz_Admin();
		//}
		
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
		// TODO: Define activation functionality here
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    0.1.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		// TODO: Define deactivation functionality here
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

		wp_enqueue_script( $this->plugin_slug . '-admin-script', WP_GRAPHVIZ_URL . '/js/admin.js', array( 'jquery' ), $this->version );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', WP_GRAPHVIZ_URL . '/css/public.css', array(), $this->version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {
		//wp_enqueue_script( $this->plugin_slug . '-plugin-script', WP_GRAPHVIZ_URL . '/js/public.js', array( 'jquery' ), $this->version );
		wp_enqueue_script( $this->plugin_slug . '-viz-public-script', WP_GRAPHVIZ_URL . '/js/viz-public.js', false, $this->version );
		wp_enqueue_script( $this->plugin_slug . '-viz-script', WP_GRAPHVIZ_URL . '/js/viz.js', false, $this->version );
	}

	public function print_general_section_info(){
		print 'Enter your general setting below:';
	}

	public function create_wp_graphviz_id_field(){
		$wp_graphviz_id = wpg_get_option('wp_graphviz_id');
		?><input type="text" id="input_wp_graphviz_id" name="wp_graphviz_options[wp_graphviz_id]" value="<?php echo $wp_graphviz_id; ?>" /><?php
	}

	public function create_wp_graphviz_title_field(){
		$wp_graphviz_options = get_option('wp_graphviz_options');
		$wp_graphviz_title = $wp_graphviz_options['wp_graphviz_title'];
		?><input type="text" id="input_wp_graphviz_title" name="wp_graphviz_options[wp_graphviz_title]" value="<?php echo $wp_graphviz_title; ?>" /><?php
	}

	function check_wp_graphviz_option($input) {

		$newinput = array();
	
		// Check value of wp_graphviz_id
		$newinput['wp_graphviz_id'] = trim($input['wp_graphviz_id']);
		if ( !is_numeric( $newinput['wp_graphviz_id'] ) ) {
			$newinput['wp_graphviz_id'] = '';
		}
	
		// Check value of wp_graphviz_title
		$newinput['wp_graphviz_title'] = trim($input['wp_graphviz_title']);

		return $newinput;
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.1.0
	 */
	public function add_plugin_admin_menu() {

        if (current_user_can($this->capability)) {
			do_action('wpg_admin_menu_starting');

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

            // Default menu items
            //
            $menuItems = array(
                array(
                    'label'             => __('General Settings', WPG_PLUGIN),
                    'slug'              => $this->plugin_slug,
                    'class'             => $this,
                    'function'          => 'display_plugin_admin_page'
                )
            );

            // Third class plugin add-ons
            //
            $menuItems = apply_filters('add_wp_graphviz_menu_items', $menuItems);

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

            // Remove the duplicate menu entry
            //
            //remove_submenu_page($this->plugin->prefix, $this->plugin->prefix);
        }
    }

	/**
	 * Init the settings page for this plugin.
	 *
	 * @since    0.1.0
	 */
	public function plugin_page_init() {		
        register_setting( 'wp_graphviz_option_group', 'wp_graphviz_options', array( $this, 'check_wp_graphviz_option' ) );
            
		add_settings_section(
			'wp_graphviz_section_id',
			__('General Settings', WPG_PLUGIN),
			array( $this, 'print_general_section_info' ),
			'wp-graphviz-setting-admin'
		);

		add_settings_field(
			'wp_graphviz_id', 
			__('WP GraphViz ID', WPG_PLUGIN),
			array( $this, 'create_wp_graphviz_id_field' ), 
			'wp-graphviz-setting-admin',
			'wp_graphviz_section_id',
			array( 'label_for' => 'wp_graphviz_id', 'field' => 'wp_graphviz_id' )
		);

		add_settings_field(
			'wp_graphviz_title',
			__('WP GraphViz Title', WPG_PLUGIN),
			array( $this, 'create_wp_graphviz_title_field' ),
			'wp-graphviz-setting-admin',
			'wp_graphviz_section_id',
			array( 'label_for' => 'wp_graphviz_title', 'field' => 'wp_graphviz_title' )
		);

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
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        WordPress Actions: http://codex.wordpress.org/Plugin_API#Actions
	 *        Action Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    0.1.0
	 */
	public function action_method_name() {
		// TODO: Define your action hook callback here
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        WordPress Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Filter Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    0.1.0
	 */
	public function filter_method_name() {
		// TODO: Define your filter hook callback here
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