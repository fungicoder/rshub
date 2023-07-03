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
            <input type="hidden" name="action" value="rshub_search">
            <input type="hidden" id="rshub_geolocation" name="rshub_geolocation">
            <input type="search" id="rshub_search_id" name="rshub_search" placeholder="Contractors...">
            <input type="submit" value="Search">
        </form>
        <script>
            // la geolocalización debe estar habilitada y luego establece el valor del campo oculto
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    document.getElementById('rshub_geolocation').value = `${position.coords.latitude},${position.coords.longitude}`;
                });
            }
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Handles the search form submission
     */
    function rshub_handle_search()
    {

        // Captura la consulta de búsqueda
        $search_query = sanitize_text_field($_POST['rshub_search_id']);
        $geolocation = sanitize_text_field($_POST['rshub_geolocation']);

        // Realiza la solicitud a la API de Google Places y decodifica la respuesta
        $api_url = "https://maps.googleapis.com/maps/api/place/textsearch/json?query={$search_query}&key=AIzaSyAJMb51OqkPRd8WHDv7y4m5cN8c99cCItI";
        $response = wp_remote_get($api_url);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Guarda los datos de la API en la base de datos de WordPress
        global $wpdb;
        $table_name = $wpdb->prefix . 'rshub_searches';
        $wpdb->insert(
            $table_name,
            array(
                'search_term' => $search_query,
                'results' => serialize($data),
                'time' => current_time( 'mysql' ),
            )
        );

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
        $data = get_option('rshub_searches');

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



    // Otros métodos relacionados con la búsqueda...
}
