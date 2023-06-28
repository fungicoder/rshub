<?php

require_once(plugin_dir_path(__FILE__) . 'twilio-lib/twilio-php-main/src/Twilio/autoload.php');

use Twilio\Rest\Client;

class Rshub
{

    public $pluginName = "rshub";

    private string $RSHUB_VERSION;

    public function __construct()
    {
        $this->RSHUB_VERSION = '0.0.1';
    }

    public function run()
    {
        // acciones y filtros
        // Create a new rshub instance
        $rshubInstance = new Rshub();

        // Add setting menu item
        add_action("admin_menu", [$rshubInstance, "addRshubAdminOption"]);

        // Saves and update settings
        add_action("admin_init", [$rshubInstance, 'rshubAdminSettingsSave']);

        // Hook our sms page
        add_action("admin_menu", [$rshubInstance, "registerRshubSmsPage"]);

        // calls sending function whenever we try sending messages.
        add_action('admin_init', [$rshubInstance, "send_message"]);

        $this->register_shortcodes();

        add_action('admin_post_rshub_search', 'rshub_handle_search');

        add_action('admin_post_nopriv_rshub_search', 'rshub_handle_search');

        add_action('admin_menu', [$rshubInstance, "rshub_search_results"]);
    }

    public function get_version()
    {
        return $this->RSHUB_VERSION;
    }

    public function displayRshubSettingsPage()
    {
        include_once plugin_dir_path(__FILE__) . "../admin/rshub-admin-settings-page.php";
    }

    public function addRshubAdminOption()
    {
        add_options_page(
            "RoofingSidingHub SMS PAGE",
            "RoofingSidingHub",
            "manage_options",
            $this->pluginName,
            [$this, "displayRshubSettingsPage"]
        );
    }

    /**
     * Sanitises all input fields.
     *
     */
    public function pluginOptionsValidate($input)
    {
        $newinput["api_sid"] = trim($input["api_sid"]);
        $newinput["api_auth_token"] = trim($input["api_auth_token"]);
        return $newinput;
    }

    /**
     * Registers and Defines the necessary fields we need.
     */
    public function rshubAdminSettingsSave()
    {
        register_setting(
            $this->pluginName,
            $this->pluginName,
            [$this, "pluginOptionsValidate"]
        );
        add_settings_section(
            "rshub_main",
            "Main Settings",
            [$this, "rshubSectionText"],
            "rshub-settings-page"
        );
        add_settings_field(
            "api_sid",
            "API SID",
            [$this, "rshubSettingSid"],
            "rshub-settings-page",
            "rshub_main"
        );
        add_settings_field(
            "api_auth_token",
            "API AUTH TOKEN",
            [$this, "rshubSettingToken"],
            "rshub-settings-page",
            "rshub_main"
        );
    }

    /**
     * Displays the settings sub header
     */
    public function rshubSectionText()
    {
        echo '<h3 style="text-decoration: underline;">Edit api details</h3>';
    }

    /**
     * Renders the sid input field
     */
    public function rshubSettingSid()
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
    public function rshubSettingToken()
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

    /**
     * Register the sms page for the admin area.
     */
    public function registerRshubSmsPage()
    {
        // Create our settings page as a submenu page.
        add_submenu_page(
            "tools.php",
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


    }

    /**
     * Display the sms page - The page we are going to be sending message from.
     * @since    1.0.0
     */
    public function displayRshubSmsPage()
    {
        include_once plugin_dir_path(__FILE__) . "../admin/rshub-admin-sms-page.php";
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
     * @since    0.0.1
     * @access   private
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
     *
     * @since    0.0.1
     * @access   private
     */
    public static function DisplayError($message = "Aww!, there was an error.")
    {
        add_action('admin_notices', function () use ($message) {
            self::adminNotice($message, false);
        });
    }

    /**
     * Displays Success Notices
     *
     * @since    0.0.1
     * @access   private
     */
    public static function DisplaySuccess($message = "Successful!")
    {
        add_action('admin_notices', function () use ($message) {
            self::adminNotice($message, true);
        });
    }

    /**
     * Register the shortcode for the search form
     */
    public function register_shortcodes()
    {
        add_shortcode('rshub_search_form', [$this, 'rshub_search_form']);
        add_shortcode('rshub_search_results', 'rshub_search_results');
    }

    /**
     * Renders the search form
     */
    public function rshub_search_form()
    {
        ob_start();
        ?>
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <input type="hidden" name="action" value="rshub_search">.
            <input type="search" id="rshub-search" name="rshub-search" placeholder="Contractors...">
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
        $search_query = sanitize_text_field($_POST['rshub-search']);

        // Realiza la solicitud a la API de Google Places y decodifica la respuesta
        $api_url = "https://maps.googleapis.com/maps/api/place/textsearch/json?query={$search_query}&key=YOUR_API_KEY";
        $response = wp_remote_get($api_url);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Guarda los datos de la API en la base de datos de WordPress
        update_option('rshub_last_search', $data);

        // Redirige al usuario a una página de resultados de búsqueda
        wp_redirect(home_url("/search-results"));
        exit;
    }

    /**
     * Muestra los resultados de la búsqueda
     */
    function rshub_search_results()
    {
        // Recupera los datos de la última búsqueda
        $data = get_option('rshub_last_search');

        // Asegurarse de que los resultados de la búsqueda son un array
        if (!is_array($data)) {
            echo 'No search results found.';
            return;
        }

        // Mostrar los resultados de la búsqueda
        echo '<table>';
        echo '<tr><th>Name</th><th>Address</th><th>Phone Number</th></tr>';
        foreach ($data as $data_item) {
            echo '<tr>';
            echo '<td>' . esc_html($data_item['name']) . '</td>';
            echo '<td>' . esc_html($data_item['address']) . '</td>';
            echo '<td>' . esc_html($data_item['phone_number']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }




}





