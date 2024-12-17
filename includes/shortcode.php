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

    $args = array(
        'limit' => $atts['limit'],
        'stock_status' => 'instock'
    );
    if ($atts['on_sale']) {
        $ids_ofertas = wc_get_product_ids_on_sale();
        $args['include'] = array_values($ids_ofertas);
    } elseif ($atts['on_featured']) {
        $ids = wc_get_featured_product_ids();
        $args['include'] = array_values($ids);
    } elseif ($atts['category'] != NULL) {
        $args['category'] = array($atts['category']);
    }


    $query = new WC_Product_Query($args);
    $products = $query->get_products();

    $ordered = wc_products_array_orderby($products, $atts['orderBy'], $atts['direction']);
    ?>
    <div class="slick-carousel ingenium-carousel">
        <?php
        // Comprobar si hay productos
        if (!empty($products)) {
            // Hay productos, puedes trabajar con ellos
            foreach ($ordered

                     as $product) {

                $price_tiers = get_post_meta($product->get_id(), 'b2bking_product_pricetiers_group_b2c', true);
                $pricesWholesale = explode(":", $price_tiers);
                if (count($pricesWholesale) > 1) {
                    $priceWholesale = intval(str_replace(';', '', $pricesWholesale[1]));

                    $priceWholesaleFormat = number_format($priceWholesale, 0, ',', '.');
                }
                $discount = get_product_discount_percentage($product->id)


                ?>
                <div class="slick-slide ingenium-product">
                    <a href="<?php echo $product->get_permalink() ?>">
                        <?php if (!is_null($discount)) { ?>

                            <div style="text-align:left;height: 0">
                            <span class="ing-on-card-button ing-onsale-card circle" data-sale="[]"
                                  data-notification="sale-percentage"
                                  data-sale-per-text="-[value]%"><?php echo "-" . $discount . "%" ?></span>
                            </div>
                            <?php
                        }
                        // Mostrar la imagen del producto
                        echo $product->get_image();
                        ?>
                    </a>
                    <a href="<?php echo $product->get_permalink() ?>">
                        <h3 class="product-title"><?php echo $product->get_name(); ?></h3>
                    </a>

                    <?php
                    if (count($pricesWholesale) > 1 && $priceWholesale > 0) {
                        if ($product->is_type('simple')) {
                            ?>
                            <div class="wholesaler-prices">
                                <p class='wholesaler'> Mayorista: </p>
                                <p class='wholesalers-price'>$ <?php echo $priceWholesaleFormat ?></p>
                            </div>
                        <?php } elseif
                        ($product->is_type('variable')) { ?>
                            <div class="wholesaler-prices">
                                <p class='wholesaler'>
                                    Mayorista: </p>
                                <p class='wholesalers-price'>Ver precios según variación</p>
                            </div>

                            <div class="detail-prices">
                                <p class="detail">Detalle</p>
                                <p class="details-price">

                                    <?php if ($product->is_type('simple')) {
                                        echo $product->get_price_html();
                                    } else {
                                        echo "Desde: $" . number_format($product->get_variation_price(), 0, ',', '.');
                                    } ?>
                                </p>
                            </div>
                        <?php }
                    } ?>
                    <?php if ($product->is_type('simple')) { ?>
                        <?php echo $product->get_price_html(); ?>
                        <a href="<?php echo $product->add_to_cart_url() ?>"
                           value="<?php echo esc_attr($product->get_id()); ?>"
                           class="ingenium-button ajax_add_to_cart add_to_cart_button"
                           data-product_id="<?php echo $product->get_id(); ?>"
                           data-product_sku="<?php echo $product->get_sku() ?>"
                           aria-label="Add “<?php the_title_attribute() ?>” to your cart">
                            Añadir al carro
                        </a>
                    <?php } else {
                        echo "Desde: $" . number_format($product->get_variation_price(), 0, ',', '.');
                        echo '<a class="ingenium-button" href="' . $product->get_permalink() . '">Seleccionar Opciones</a>';
                    }
                    ?>
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
                autoplay: true,
                autoplaySpeed: 2000,
                prevArrow: '<button type="button" class="slick-prev">Previous</button>',
                nextArrow: '<button type="button" class="slick-next">Next</button>',
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3,
                            infinite: true,
                            dots: true
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1
                        }
                    }
                    // You can unslick at a given breakpoint now by adding:
                    // settings: "unslick"
                    // instead of a settings object
                ],

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

function get_product_discount_percentage($product_id)
{
    // Obtener el producto
    $product = wc_get_product($product_id);

    // Asegurarse de que es un producto válido
    if (!$product) {
        return null;
    }

    // Obtener el precio regular y el precio de oferta
    $regular_price = $product->get_regular_price();
    $sale_price = $product->get_sale_price();

    // Si no hay precio de oferta, no hay descuento
    if (empty($sale_price) || empty($regular_price)) {
        return null;
    }

    // Calcular el porcentaje de descuento
    $discount_percentage = (($regular_price - $sale_price) / $regular_price) * 100;

    // Redondear a 2 decimales
    return round($discount_percentage);
}