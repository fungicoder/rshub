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
            <input type="hidden" id="rshub_user_geolocation" name="rshub_user_geolocation">
            <input type="search" id="rshub_search_user_query" name="rshub_search_user_query" placeholder="Contractors...">
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
        $search_query = sanitize_text_field($_POST['rshub_search_user_query']);
        $geolocation = sanitize_text_field($_POST['rshub_user_geolocation']);

        // Realiza la solicitud a la API de Google Places y decodifica la respuesta
        $api_url = "https://maps.googleapis.com/maps/api/place/textsearch/json?query={$search_query}&key=AIzaSyAJMb51OqkPRd8WHDv7y4m5cN8c99cCItI";
        // Realiza la solicitud a la API de Google Places
        $response = wp_remote_get($api_url);
        if (is_wp_error($response)) {
            // Maneja el error
            $error_message = $response->get_error_message();
            echo "Algo salió mal: $error_message";
        } else {
            // Decodifica la respuesta
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            // Procesa los resultados
            $processed_results = [];
            foreach ($data['results'] as $result) {
                $processed_results[] = [
                    'name' => $result['name'],
                    'formatted_address' => $result['formatted_address'],
                    // Añade aquí cualquier otro campo que necesites
                ];
            }

            // Guarda los datos de la API en la base de datos de WordPress
            global $wpdb;
            $table_name = $wpdb->prefix . 'rshub_searches';
            $result = $wpdb->insert(
                $table_name,
                array(
                    'id' => null,
                    'search_query' => $search_query,
                    'search_results' => serialize($processed_results),
                    'search_geolocation' => $geolocation,
                    'search_time' => current_time('mysql'),
                )
            );
            if ($result === false) {
                // Maneja el error
                echo "No se pudo insertar los datos en la base de datos";
                echo "Error de la base de datos: " . $wpdb->last_error;
            }
        }

        // Redirige al usuario a una página de resultados de búsqueda
        wp_redirect(home_url("/public-search-results/"));
        exit;
    }

    // Otros métodos relacionados con la búsqueda...
}
