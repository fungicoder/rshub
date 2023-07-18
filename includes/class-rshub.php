<?php


require_once(plugin_dir_path(__FILE__) . 'RshubSettings.php');
require_once(plugin_dir_path(__FILE__) . 'RshubSearch.php');
require_once(plugin_dir_path(__FILE__) . 'RshubSMS.php');
require_once(plugin_dir_path(__FILE__) . 'RshubLeadForm.php');
require_once(plugin_dir_path(__FILE__) . 'RshubRegistration.php');


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
    public $leadform;
    public $registration;

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
        $this->leadform = new RshubLeadForm($this->pluginName);
        $this->registration = new RshubRegistration($this->pluginName);

        //$this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    //private function load_dependencies(){

    //   require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-plugin-name-loader.php';
    //}

    private function define_admin_hooks()
    {
        // Add settings fields to main menu page
        add_action('admin_menu', [$this->settings, 'rshub_settings_page']);
    }

    private function define_public_hooks()
    {
        // Add shortcodes
        add_action('init', [$this, 'register_shortcodes']);
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

        add_action('admin_post_rshub_search', [$this->search, 'rshub_handle_search']);
        add_action('admin_post_nopriv_rshub_search', [$this->search, 'rshub_handle_search']);

        add_action('admin_post_rshub_leads', [$this->leadform, 'rshub_handle_lead']);
        add_action('admin_post_nopriv_rshub_leads', [$this->leadform, 'rshub_handle_lead']);

        add_action('admin_post_rshub_contractor_register', [$this->registration, 'rshub_handle_contractor_registration']);
        add_action('admin_post_nopriv_rshub_contractor_register', [$this->registration, 'rshub_handle_contractor_registration']);

        add_action('admin_post_rshub_homeowner_register', [$this->registration, 'rshub_handle_homeowner_registration']);
        add_action('admin_post_nopriv_rshub_homeowner_register', [$this->registration, 'rshub_handle_homeowner_registration']);

        add_action('init',[$this->leadform, 'create_post_from_lead']);


        add_filter('theme_page_templates', [$this, 'rshub_add_template']);
        add_filter('template_include', [$this, 'rshub_load_template']);
    }

    /**
     *
     * This section refers to the search form and search results
     * Register the shortcode for the search form
     */
    public function register_shortcodes()
    {
        add_shortcode('rshub_search_form', [$this->search, 'rshub_search_form']);

        add_shortcode('rshub_lead_form', [$this->leadform, 'rshub_lead_form']);

        add_shortcode('rshub_contractor_registration_form', [$this->registration, 'rshub_contractor_registration_form']);

        add_shortcode('rshub_homeowner_registration_form', [$this->registration, 'rshub_homeowner_registration_form']);
    }

    public function rshub_add_template($templates)
    {
        $templates['page-projects.php'] = 'Projects Page';
        return $templates;
    }


    function rshub_load_template($template)
    {
        global $post;
        if ($post->post_type == 'page' && $post->page_template == 'page-projects.php') {
            $template = plugin_dir_path(__FILE__) . '../public/page-projects.php';
        }
        return $template;
    }

}





