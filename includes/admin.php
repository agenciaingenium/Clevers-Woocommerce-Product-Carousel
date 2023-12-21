<?php
// Función para agregar una página de opciones en el menú de administración
function agregar_pagina_opciones() {
    global $submenu;
    add_menu_page(
        'Opciones de Color',  // Título de la página
        'Opciones de Color',  // Título en el menú
        'manage_options',     // Capacidad requerida
        'opciones-de-color',  // Slug de la página (URL)
        'mostrar_pagina_opciones'  // Función que mostrará la página
    );
}
add_action('admin_menu', 'agregar_pagina_opciones');

// Función para mostrar el contenido de la página de opciones
function mostrar_pagina_opciones() {
    ?>
    <div class="wrap">
        <h1>Opciones de Color</h1>

        <form method="post" action="options.php">
            <?php
            // Output campos de configuración para las variables de color
            settings_fields('grupo-opciones-color');
            do_settings_sections('opciones-de-color');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Función para inicializar y registrar las opciones de color
function inicializar_opciones_color() {
    add_settings_section(
        'seccion-opciones-color',
        'Configuración de Colores',
        'mostrar_seccion_opciones',
        'opciones-de-color'
    );

    add_settings_field(
        'color-primario',
        'Color Primario',
        'mostrar_campo_color_primario',
        'opciones-de-color',
        'seccion-opciones-color'
    );

    add_settings_field(
        'categoria',
        'Categoría',
        'mostrar_campo_categoria',
        'opciones-de-color',
        'seccion-opciones-color'
    );

    add_settings_field(
        'orden',
        'Ordenar por',
        'mostrar_campo_orden',
        'opciones-de-color',
        'seccion-opciones-color'
    );

    add_settings_field(
        'ofertas',
        'Mostrar solo ofertas',
        'mostrar_campo_ofertas',
        'opciones-de-color',
        'seccion-opciones-color'
    );

    register_setting(
        'grupo-opciones-color',
        'color-primario',
        array(
            'sanitize_callback' => 'sanitize_hex_color',
            'default'           => '#3498db', // Valor predeterminado
        )
    );

    register_setting(
        'grupo-opciones-color',
        'categoria'
    );

    register_setting(
        'grupo-opciones-color',
        'orden',
        array(
            'default' => 'date',  // Valor predeterminado
        )
    );

    register_setting(
        'grupo-opciones-color',
        'ofertas',
        array(
            'default' => false,  // Valor predeterminado
        )
    );
}
add_action('admin_init', 'inicializar_opciones_color');

// Función para mostrar la sección de opciones
function mostrar_seccion_opciones() {
    echo 'Personaliza los colores para tu tema o plugin.';
}

// Función para mostrar el campo de color primario
function mostrar_campo_color_primario() {
    $color = get_option('color-primario', '#3498db');
    echo "<input type='text' name='color-primario' value='$color' class='color-field' />";
}

// Función para mostrar el campo de categoría
function mostrar_campo_categoria() {
    $categorias = get_terms('category', array('hide_empty' => false));
    $categoria_actual = get_option('categoria');
    ?>
    <select name="categoria">
        <option value="">Todas las categorías</option>
        <?php foreach ($categorias as $categoria) : ?>
            <option value="<?php echo esc_attr($categoria->term_id); ?>" <?php selected($categoria->term_id, $categoria_actual); ?>><?php echo esc_html($categoria->name); ?></option>
        <?php endforeach; ?>
    </select>
    <?php
}

// Función para mostrar el campo de orden
function mostrar_campo_orden() {
    $orden_actual = get_option('orden', 'date');
    ?>
    <select name="orden">
        <option value="date" <?php selected('date', $orden_actual); ?>>Fecha</option>
        <option value="price" <?php selected('price', $orden_actual); ?>>Precio</option>
        <!-- Agrega más opciones según sea necesario -->
    </select>
    <?php
}

// Función para mostrar el campo de ofertas
function mostrar_campo_ofertas() {
    $ofertas = get_option('ofertas', false);
    ?>
    <label>
        <input type="checkbox" name="ofertas" value="1" <?php checked($ofertas, true); ?> />
        Mostrar solo ofertas
    </label>
    <?php
}
