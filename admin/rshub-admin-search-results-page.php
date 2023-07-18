<?php

global $wpdb;
$rshub_searches = $wpdb->prefix . 'rshub_searches';
$results = $wpdb->get_results("SELECT * FROM $rshub_searches");

// Imprimir el último error de la base de datos
if ($wpdb->last_error) {
    echo "Error de la base de datos: " . $wpdb->last_error;
}
?>

<h2> <?php esc_attr_e( 'Latest Searches', 'WpAdminStyle' ); ?></h2>
<div class="wrap">
    <h1>Resultados de la búsqueda</h1>
    <?php
    if (!$results): ?>
        <p>No hay resultados</p>
    <?php else: ?>
        <ul>
            <?php foreach ($results as $result) :
                $search_results = unserialize($result->search_results);
                foreach ($search_results['results'] as $search_result) : ?>
                    <li>
                        <p>id: <?php echo $result->id; ?> </p>
                        <p>Consulta de búsqueda: <?php echo $result->search_query; ?></p>
                        <p>Nombre del lugar: <?php echo $search_result; ?></p> <!-- Modificado aquí -->
                        <p>Geolocalización: <?php echo $result->search_geolocation; ?></p>
                        <p>Hora de la búsqueda: <?php echo $result->search_time; ?></p>
                    </li>
                <?php endforeach; endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

