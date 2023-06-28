<?php

require_once(plugin_dir_path(__FILE__) . 'twilio-lib/twilio-php-main/src/Twilio/autoload.php');

use Twilio\Rest\Client;

class Rshub
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     * @var      Plugin_Name_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    public $pluginName;

    private string $RSHUB_VERSION;

    public function __construct()
    {
        if (defined('RSHUB_VERSION')) {
            $this->RSHUB_VERSION = RSHUB_VERSION;
        } else {
            $this->RSHUB_VERSION = '0.0.1';
        }

        $this->pluginName = "rshub";
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies(){

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-plugin-name-loader.php';



    }

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

        // Create a new rshub instance
        $rshubInstance = new Rshub();

        // Saves and update settings
        add_action("admin_init", [$rshubInstance, 'rshubTwilioSettingsSave']);

        add_action("admin_init", [$rshubInstance, 'results_settings_df']);

        // calls sending function whenever we try sending messages.
        add_action('admin_init', [$rshubInstance, "send_message"]);


        $this->register_shortcodes();

        add_action('admin_post_rshub_search', [$this, 'rshub_handle_search']);

        add_action('admin_post_nopriv_rshub_search', [$this, 'rshub_handle_search']);

        // Add setting menu item
        add_action('admin_menu', [$rshubInstance, 'rshub_settings_page']);
    }


    function rshub_settings_page()
    {
        add_menu_page(
            'RoofingSidingHub.com',
            'RsHub',
            'manage_options',
            'rshub-main',
            [$this, 'displayRshubSettingsPage'],
            'dashicons-wordpress-alt',
            100
        );

        // Create our settings page as a submenu page.
        add_submenu_page(
            "rshub-main",
            // parent slug
            __("RooingSidingHub SMS PAGE", $this->pluginName . "-sms"),
            // page title
            __("RooingSidingHub SMS", $this->pluginName . "-sms"),
            // menu title
            "manage_options", // capability
            $this->pluginName . "-sms",
            // menu_slug
            [$this, "displayRshubSmsPage"] // callable function
        );

        add_submenu_page(
            "rshub-main",
            // parent slug
            __("RooingSidingHub Google Api", $this->pluginName . "-google"),
            // page title
            __("RooingSidingHub Google Api Config", $this->pluginName . "-google"),
            // menu title
            "manage_options", // capability
            $this->pluginName . "-google",
            // menu_slug
            [$this, "displayRshubGoogleApiConfig"] // callable function
        );

        add_submenu_page(
            "rshub-main",
            // parent slug
            __("RooingSidingHub Search Results", $this->pluginName . "-search"),
            // page title
            __("RooingSidingHub Search Results", $this->pluginName . "-search"),
            // menu title
            "manage_options", // capability
            $this->pluginName . "-search",
            // menu_slug
            [$this, "displayRshubSearchResultsAdminPage"] // callable function
        );

    }

    /**
     * Display the settings for this plugin.
     */
    public function displayRshubSmsPage()
    {
        include_once plugin_dir_path(__FILE__) . "../admin/rshub-admin-sms-page.php";
    }

    function displayRshubSearchResultsAdminPage()
    {
        include_once plugin_dir_path(__FILE__) . "../admin/rshub-admin-search-results-page.php";
    }

    function displayRshubGoogleApiConfig()
    {
        include_once plugin_dir_path(__FILE__) . "../admin/rshub-admin-google-api-page.php";
    }


    public function displayRshubSettingsPage()
    {
        include_once plugin_dir_path(__FILE__) . "../admin/rshub-admin-settings-page.php";
    }

    /**
     * This secction refers to the sms page
     * Registers and Defines the necessary fields we need.
     */
    public function rshubTwilioSettingsSave()
    {

        register_setting(
            $this->pluginName,
            $this->pluginName,
            [$this, "rshubTwilioOptionsValidate"]
        );
        add_settings_section(
            "rshub_main",
            "Main Settings",
            [$this, "rshubTwilioConfigSectionText"],
            "rshub-settings-page"
        );
        add_settings_field(
            "api_sid",
            "API SID",
            [$this, "rshubSettingTwilioSid"],
            "rshub-settings-page",
            "rshub_main"
        );
        add_settings_field(
            "api_auth_token",
            "API AUTH TOKEN",
            [$this, "rshubSettingTwilioToken"],
            "rshub-settings-page",
            "rshub_main"
        );
    }

    /**
     * Sanitises all input fields.
     *
     */
    public function rshubTwilioOptionsValidate($input)
    {
        $newinput["api_sid"] = trim($input["api_sid"]);
        $newinput["api_auth_token"] = trim($input["api_auth_token"]);
        return $newinput;
    }

    /**
     * Displays the settings sub title header
     */
    public function rshubTwilioConfigSectionText()
    {
        echo '<h4 style="text-decoration: underline;">Edit Twilio Api details</h4>';
    }

    /**
     * Renders the sid input field
     */
    public function rshubSettingTwilioSid()
    {
        $options = get_option($this->pluginName);
        if ($options === false || !isset($options['api_sid'])) {
            $api_sid = '';
        } else {
            $api_sid = $options['api_sid'];
        }
        echo "
        <input
            id='{$this->pluginName}[api_sid]'
            name='{$this->pluginName}[api_sid]'
            size='40'
            type='text'
            value='{$api_sid}'
            placeholder='Enter your API SID here'
        />
            ";
    }

    /**
     * Renders the auth_token input field
     *
     */
    public function rshubSettingTwilioToken()
    {
        $options = get_option($this->pluginName);
        if ($options === false || !isset($options['api_auth_token'])) {
            $api_auth_token = '';
        } else {
            $api_auth_token = $options['api_auth_token'];
        }
        echo "
        <input
            id='{$this->pluginName}[api_auth_token]'
            name='{$this->pluginName}[api_auth_token]'
            size='40'
            type='text'
            value='{$api_auth_token}'
            placeholder='Enter your API AUTH TOKEN here'
        />
    ";
    }

    public function send_message()
    {
        if (!isset($_POST["send_sms_message"])) {
            return;
        }

        $to = (isset($_POST["numbers"])) ? $_POST["numbers"] : "";
        $sender_id = (isset($_POST["sender"])) ? $_POST["sender"] : "";
        $message = (isset($_POST["message"])) ? $_POST["message"] : "";

        //gets our api details from the database.
        $api_details = get_option($this->pluginName);
        if (is_array($api_details) and count($api_details) != 0) {
            $TWILIO_SID = $api_details["api_sid"];
            $TWILIO_TOKEN = $api_details["api_auth_token"];
        }

        try {
            $client = new Client($TWILIO_SID, $TWILIO_TOKEN);
            $response = $client->messages->create(
                $to,
                array(
                    "from" => $sender_id,
                    "body" => $message
                )
            );

            self::DisplaySuccess();
        } catch (Exception $e) {

            self::DisplayError($e->getMessage());
        }
    }

    /**
     * Designs for displaying Notices
     *
     * @var $message - String - The message we are displaying
     * @var $status - Boolean - its either true or false
     */
    public static function adminNotice($message, $status = true)
    {
        $class = ($status) ? "notice notice-success" : "notice notice-error";
        $message = __($message, "sample-text-domain");

        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }

    /**
     * Displays Error Notices
     */
    public static function DisplayError($message = "Aww!, there was an error.")
    {
        add_action('admin_notices', function () use ($message) {
            self::adminNotice($message, false);
        });
    }

    /**
     * Displays Success Notices
     */
    public static function DisplaySuccess($message = "Successful!")
    {
        add_action('admin_notices', function () use ($message) {
            self::adminNotice($message, true);
        });
    }

    /**
     *
     * This section refers to the search form and search results
     *
     * Register the shortcode for the search form
     */
    public function register_shortcodes()
    {
        add_shortcode('rshub_search_form', [$this, 'rshub_search_form']);
        add_shortcode('rshub_public_search_results', [$this, 'rshub_search_results_admin_page']);
    }

    /**
     * Renders the search form
     */
    public function rshub_search_form()
    {
        ob_start();
        ?>
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <input type="hidden" name="action" value="rshub-search">.
            <input type="search" id="rshub-search-id" name="rshub-search" placeholder="Contractors...">
            <input type="submit" value="Search">
        </form>
        <?php
        return ob_get_clean();
    }

    /**
     * Handles the search form submission
     */
    function rshub_handle_search()
    {
        // Captura la consulta de búsqueda
        $search_query = sanitize_text_field($_POST['rshub-search-id']);

        // Realiza la solicitud a la API de Google Places y decodifica la respuesta
        $api_url = "https://maps.googleapis.com/maps/api/place/textsearch/json?query={$search_query}&key=AIzaSyAJMb51OqkPRd8WHDv7y4m5cN8c99cCItI";
        $response = wp_remote_get($api_url);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Guarda los datos de la API en la base de datos de WordPress
        update_option('rshub_last_search', $data);

        // Redirige al usuario a una página de resultados de búsqueda
        wp_redirect(home_url("/public-search-results"));
        exit;
    }

    /**
     * Muestra los resultados de la búsqueda
     */
    function rshub_search_results_admin_page()
    {
        // Obtiene los datos de la última búsqueda
        $data = get_option('rshub_last_search');

        // Si no hay datos, muestra un mensaje de error
        if (!$data) {
            return "No hay resultados";
        }

        // Si hay datos, los muestra
        ob_start();
        ?>
        <h1>Resultados de la búsqueda</h1>
        <ul>
            <?php foreach ($data['results'] as $result) : ?>
                <li>
                    <h2><?php echo $result['name']; ?></h2>
                    <p><?php echo $result['formatted_address']; ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
        return ob_get_clean();

    }


    public function rshubSearchResultsAdminSectionText()
    {
        echo '<p>Latest searches</p>';
    }

    public function results_settings_df()
    {
        register_setting(
            $this->pluginName,
            $this->pluginName
        );
        add_settings_section(
            "rshub_search_results_section",
            "Searches Admin View",
            [$this, "rshubSearchResultsAdminSectionText"],
            "rshub-serach-results-page"
        );
        add_settings_field(
            "latest_searches",
            "Latest Searches",
            [$this, ""],
            "rshub-serach-results-page"
        );
    }


}





