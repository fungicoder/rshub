<?php
// solo los usuarios con los permisos adecuados puedan acceder a esta página
if (!current_user_can('manage_options')) {
    wp_die('No tienes suficientes permisos para acceder a esta página.');
}

// Obtener los resultados de búsqueda de donde están almacenandos.
global $wpdb;
$table_name = $wpdb->prefix . 'rshub_searches';
$results = $wpdb->get_results("SELECT * FROM $table_name");

// Muestra los resultados
foreach ($results as $result) {
    $data = json_decode($result->search_results, true);
}
?>
<div class="wrap">
    <h1>Resultados de la búsqueda</h1>

    <?php
    // Obtén todas las opciones que comienzan con 'rshub_search_'
    global $wpdb;
    $results = $wpdb->get_results("SELECT table_name FROM $wpdb->options WHERE option_name LIKE 'rshub_search_%'", 'ARRAY_A');

    // Itera sobre cada resultado de búsqueda y muéstralos
    foreach ($results as $result_option_name) {
        $result = get_option($result_option_name['option_name']);
        echo $this->search->display_search_result($result);
    }
    ?>

</div>
