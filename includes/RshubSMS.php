<?php
class RshubSMS
{
    private $pluginName;

    public function __construct(string $pluginName)
    {
        $this->pluginName = $pluginName;
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
     * Display the settings for this plugin.
     */
    public function displayRshubSmsPage()
    {
        include_once plugin_dir_path(__FILE__) . "../admin/rshub-admin-sms-page.php";
    }

    // Otros métodos relacionados con SMS...
}
