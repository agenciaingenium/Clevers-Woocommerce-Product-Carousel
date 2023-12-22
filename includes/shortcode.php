<?php
// Enqueue Bootstrap CSS y JS
function enqueue_slick_scripts()
{
    // Agrega Slick CSS
    wp_enqueue_style('slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
    wp_enqueue_style('slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css');

    wp_enqueue_style('custom-style', plugin_dir_url(__FILE__) . '../css/style.css', array('slick-css', 'slick-theme-css'));


    // Agrega jQuery y Slick JS
    wp_enqueue_script('jquery');
    wp_enqueue_script('slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), null, true);
}

function enqueue_woocommerce_script()
{
    if (class_exists('WooCommerce')) {
        wp_enqueue_script('wc-add-to-cart', WC()->plugin_url() . '/assets/js/frontend/add-to-cart.min.js', array('jquery'), WC()->version, true);
    }
}

add_action('wp_enqueue_scripts', 'enqueue_woocommerce_script');

add_action('wp_enqueue_scripts', 'enqueue_slick_scripts');


// Shortcode para el carousel
function slick_woocommerce_carousel_shortcode($atts)
{
    // Definir los atributos y sus valores predeterminados
    $atts = shortcode_atts(
        array(
            'num_per_slide' => 3,  // Número de productos por slide
            'limit' => 8,          // Límite total de productos a mostrar
            'orderBy' => 'price',
            'direction' => 'desc', //
            'on_sale' => false,
            'on_featured' => false,
            'filter_by' => null,
            'category' => null
        ),
        $atts,
        'slick_woocommerce_carousel'
    );

    ob_start();
    ?>
    <div class="slick-carousel custom-carousel">

        <?php
print_r($atts);
        $query = new WC_Product_Query();
        if ($atts['on_sale']) {
            $ids_ofertas = wc_get_product_ids_on_sale();
            $args['include'] = array_values($ids_ofertas);
        } elseif ($atts['on_featured']) {
            $ids = wc_get_featured_product_ids();
            $args['include'] = array_values($ids);
        } elseif ($atts['category'] != NULL) {
            $args = array(
                'limit' => $atts['limit'],
                'category' => array($atts['category']),
            );
        } else {

            // Obtener productos de WooCommerce
            $args = array(
                'limit' => $atts['limit'],
                'include' => $ids
            );
        }

        $query = new WC_Product_Query($args);
        $products = $query->get_products();

        $ordered = wc_products_array_orderby($products, $atts['orderBy'], $atts['direction']);

        // Comprobar si hay productos
        if (!empty($products)) {
            // Hay productos, puedes trabajar con ellos
            foreach ($ordered as $product) {
                //print_r($product->get_name());
                ?>
                <div class="slick-slide ingenium-product">
                    <?php
                    // Mostrar la imagen del producto
                    echo $product->get_image();
                    ?>
                    <h3><?php echo $product->get_name(); ?></h3>
                    <p class="ingenium-price"><span><?php echo $product->get_price_html(); ?></span><span><?php $product->get_sale_price(); ?></span></p>
                    <a href="<?php echo $product->add_to_cart_url() ?>"
                       value="<?php echo esc_attr($product->get_id()); ?>"
                       class="ingenium-button ajax_add_to_cart add_to_cart_button"
                       data-product_id="<?php echo $product->get_id(); ?>"
                       data-product_sku="<?php echo esc_attr($sku) ?>"
                       aria-label="Add “<?php the_title_attribute() ?>” to your cart">
                        Añadir al carro
                    </a>
                </div>
                <?php
            }

        } else {
            echo 'No hay productos para mostrar.';
        }
        ?>
    </div>

    <script>
        jQuery(document).ready(function ($) {
            // Inicializar el carrusel Slick
            $('.slick-carousel').slick({
                slidesToShow: <?php echo $atts['num_per_slide']; ?>,
                slidesToScroll: 1,
                prevArrow: '<button type="button" class="slick-prev">Previous</button>',
                nextArrow: '<button type="button" class="slick-next">Next</button>',
            });

            // Manejar el clic del botón "Agregar al carrito" mediante Ajax
            $('.add_to_cart').on('click', function (e) {
                e.preventDefault();

                var productId = $(this).data('product-id');

                console.log(wc_add_to_cart_params.ajax_url);

                // Realizar la solicitud Ajax para agregar al carrito
                $.ajax({
                    type: 'POST',
                    url: wc_add_to_cart_params.ajax_url,
                    data: {
                        action: 'woocommerce_ajax_add_to_cart',
                        product_id: productId,
                    },
                    success: function (response) {
                        // Manejar la respuesta después de agregar al carrito
                        alert('Producto agregado al carrito');
                    },
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}

add_shortcode('slick_woocommerce_carousel', 'slick_woocommerce_carousel_shortcode');