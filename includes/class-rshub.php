<?php


require_once(plugin_dir_path(__FILE__) . 'RshubSettings.php');
require_once(plugin_dir_path(__FILE__) . 'RshubSearch.php');
require_once(plugin_dir_path(__FILE__) . 'RshubSMS.php');



class Rshub
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     * @var      Rshub $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    public $pluginName;

    private string $RSHUB_VERSION;

    public $settings;
    public $sms;
    public $search;

    public function __construct()
    {
        if (defined('RSHUB_VERSION')) {
            $this->RSHUB_VERSION = RSHUB_VERSION;
        } else {
            $this->RSHUB_VERSION = '0.0.1';
        }

        $this->pluginName = "rshub";


        // Create a new Classes instances

        $this->settings = new RshubSettings($this->pluginName);
        $this->sms = new RshubSMS($this->pluginName);
        $this->search = new RshubSearch($this->pluginName);

        //$this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    //private function load_dependencies(){

     //   require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-plugin-name-loader.php';
    //}

    private function define_admin_hooks(){

    }

    private function define_public_hooks(){

    }

    public function get_version()
    {
        return $this->RSHUB_VERSION;
    }


    // Run acciones y filtros
    public function run()
    {
        // Saves and update settings
        add_action("admin_init", [$this->settings, 'rshubSettingsSave']);

        // calls sending function whenever we try sending messages.
        add_action('admin_init', [$this->sms, "send_message"]);


        // Add shortcodes
        add_action('init', [$this, 'register_shortcodes']);


        add_action('admin_post_rshub_search', [$this->search, 'rshub_handle_search']);

        add_action('admin_post_nopriv_rshub_search', [$this->search, 'rshub_handle_search']);

        // Add settings fields to main menu page
        add_action('admin_menu', [$this->settings, 'rshub_settings_page']);

        add_action('admin_init', [$this->search, 'rshub_search_results_admin_page']);


    }


    /**
     *
     * This section refers to the search form and search results
     * Register the shortcode for the search form
     */
    public function register_shortcodes()
    {
        add_shortcode('rshub_search_form', [$this->search, 'rshub_search_form']);
    }
}





