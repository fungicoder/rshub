<?php
class RshubSettings
{
    private $pluginName;

    public function __construct($pluginName)
    {
        $this->pluginName = $pluginName;
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

    public function displayRshubSettingsPage()
    {
        include_once plugin_dir_path(__FILE__) . "../admin/rshub-admin-settings-page.php";
    }

    public function displayRshubSmsPage()
    {
        include_once plugin_dir_path(__FILE__) . "../admin/rshub-admin-sms-page.php";
    }

    public function displayRshubGoogleApiConfig()
    {
        include_once plugin_dir_path(__FILE__) . "../admin/rshub-admin-google-api-page.php";
    }

    public function displayRshubSearchResultsAdminPage()
    {
        include_once plugin_dir_path(__FILE__) . "../admin/rshub-admin-search-results-page.php";
    }


}