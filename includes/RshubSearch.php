<?php
class RshubSearch
{
    private $pluginName;

    public function __construct(string $pluginName)
    {
        $this->pluginName = $pluginName;
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

    function displayRshubSearchResultsAdminPage()
    {
        include_once plugin_dir_path(__FILE__) . "../admin/rshub-admin-search-results-page.php";
    }

    // Otros métodos relacionados con la búsqueda...
}
