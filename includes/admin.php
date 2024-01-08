<?php

function imprimir_estilos_personalizados()
{
    $colorPrimary = get_option('color-primary', '#3498db');
    $colorSecondary = get_option('color-secondary', '#3498db');
    $colorBackground = get_option('color-background', '#3498db');
    ?>
    <style>
        :root {
            --ingenium-primary: <?php echo esc_attr($colorPrimary); ?>;
            --ingenium-seconday: <?php echo esc_attr($colorSecondary); ?>; /* Verde */
            --ingenium-background: <?php echo esc_attr($colorBackground); ?>; /* Gris claro */
        }
    </style>
    <?php
}

// Registra la acción para imprimir los estilos
add_action('wp_head', 'imprimir_estilos_personalizados');


