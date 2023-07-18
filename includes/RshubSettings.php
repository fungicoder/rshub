<?php

require_once(plugin_dir_path(__FILE__) . 'RshubSearch.php');


class RshubSettings
{
    private $pluginName;
    public $search;

    public function __construct($pluginName)
    {
        $this->pluginName = $pluginName;
        $this->search = new RshubSearch($this->pluginName);
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
            __("RoofingSidingHub SMS PAGE", $this->pluginName . "-sms"),
            // page title
            __("RoofingSidingHub SMS", $this->pluginName . "-sms"),
            // menu title
            "manage_options", // capability
            $this->pluginName . "-sms",
            // menu_slug
            [$this, "displayRshubSmsPage"] // callable function
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

        add_submenu_page(
            "rshub-main",
            // parent slug
            __("RooingSidingHub leads", $this->pluginName . "-leads"),
            // page title
            __("RooingSidingHub leads", $this->pluginName . "-search"),
            // menu title
            "manage_options", // capability
            $this->pluginName . "-leads",
            // menu_slug
            [$this, "displayRshubLeadsAdminPage"] // callable function
        );

    }

    /**
     * This secction refers to the sms page
     * Registers and Defines the necessary fields we need.
     */
    public function rshubSettingsSave()
    {

        register_setting(
            $this->pluginName,
            $this->pluginName,
            [$this, "rshubSettingsOptionsValidate"]
        );
        add_settings_section(
            "rshub_main_twilio_section",
            "Main Settings",
            [$this, "rshubTwilioConfigSectionText"],
            "rshub-settings-page"
        );
        add_settings_field(
            "api_sid",
            "API SID",
            [$this, "rshubSettingTwilioSid"],
            "rshub-settings-page",
            "rshub_main_twilio_section"
        );
        add_settings_field(
            "api_auth_token",
            "API AUTH TOKEN",
            [$this, "rshubSettingTwilioToken"],
            "rshub-settings-page",
            "rshub_main_twilio_section"
        );

        add_settings_section(
            "rshub_main_google_section",
            "Google Api Settings",
            [$this, "rshubGoogleConfigSectionText"],
            "rshub-settings-page"
        );

        add_settings_field(
            "google_places_api_key",
            "Google Places Api Key",
            [$this, "rshubSettingsGoogleApi"],
            "rshub-settings-page",
            "rshub_main_google_section"
        );
    }

    /**
     * Sanitises all input fields.
     *
     */
    public function rshubSettingsOptionsValidate($input)
    {
        $newinput["twilio_api_sid"] = trim($input["twilio_api_sid"]);
        $newinput["twilio_api_auth_token"] = trim($input["twilio_api_auth_token"]);
        $newinput["google_places_api_key"] = trim($input["google_places_api_key"]);
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
        if ($options === false || !isset($options['twilio_api_sid'])) {
            $twilio_api_sid = '';
        } else {
            $twilio_api_sid = $options['twilio_api_sid'];
        }
        echo "
        <input
            id='{$this->pluginName}[twilio_api_sid]'
            name='{$this->pluginName}[twilio_api_sid]'
            size='40'
            type='text'
            value='{$twilio_api_sid}'
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
        if ($options === false || !isset($options['twilio_api_auth_token'])) {
            $twilio_api_auth_token = '';
        } else {
            $twilio_api_auth_token = $options['twilio_api_auth_token'];
        }
        echo "
        <input
            id='{$this->pluginName}[twilio_api_auth_token]'
            name='{$this->pluginName}[twilio_api_auth_token]'
            size='40'
            type='text'
            value='{$twilio_api_auth_token}'
            placeholder='Enter your API AUTH TOKEN here'
        />
    ";
    }

    /**
     * Displays the settings sub title header
     */
    public function rshubGoogleConfigSectionText()
    {
        echo '<h4 style="text-decoration: underline;">Edit Google Places Api details</h4>';
    }

    /**
     * Registers and Defines the necessary fields we need.
     */
    public function rshubSettingsGoogleApi(){
        $options = get_option($this->pluginName);
        if ($options === false || !isset($options['google_places_api_key'])) {
            $google_places_api_key = '';
        } else {
            $google_places_api_key = $options['google_places_api_key'];
        }
        echo "
        <input
            id='{$this->pluginName}[google_places_api_key]'
            name='{$this->pluginName}[google_places_api_key]'
            size='40'
            type='text'
            value='{$google_places_api_key}'
            placeholder='Enter your Google Places API Key here'
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

    public function displayRshubSearchResultsAdminPage()
    {
        include_once plugin_dir_path(__FILE__) . "../admin/rshub-admin-search-results-page.php";
    }

    public function displayRshubLeadsAdminPage()
    {
        include_once plugin_dir_path(__FILE__) . "../admin/rshub-admin-leads-page.php";
    }
}