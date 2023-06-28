<?php

// solo los usuarios con los permisos adecuados puedan acceder a esta página
if (!current_user_can('manage_options')) {
    wp_die('No tienes suficientes permisos para acceder a esta página.');
}

// Obtener los resultados de búsqueda de donde están almacenandos.
$results = get_option('rshub_search_results', array());

settings_fields($this->pluginName);
do_settings_sections('rshub-serach-results-page');

?>

<div class="wrap">
    <h1>Search Results</h1>

    <?php if (empty($results)): ?>
        <p>No se encontraron resultados.</p>
    <?php else: ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
            <tr>
                <th scope="col">Nombre del contratista</th>
                <th scope="col">Número de teléfono</th>
                <th scope="col">Dirección</th>
                <!-- Agregar aquí más columnas si es necesario -->
            </tr>
            </thead>

            <tbody>
            <?php foreach ($results as $result): ?>
                <tr>
                    <td><?php echo esc_html($result['name']); ?></td>
                    <td><?php echo esc_html($result['phone']); ?></td>
                    <td><?php echo esc_html($result['address']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
