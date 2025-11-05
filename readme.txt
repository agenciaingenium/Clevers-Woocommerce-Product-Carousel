=== Clevers Product Slider ===
Contributors: cleversdevs
Donate link: https://clevers.dev
Tags: woocommerce, carousel, slider, products, ecommerce, slick, responsive
Requires at least: 5.8
Tested up to: 6.7
Stable tag: 0.2.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Crea sliders profesionales de productos WooCommerce con presets personalizables, colores por carrusel y diseño responsive basado en Slick.js.

== Description ==

**Clevers Product Slider** te permite crear y mostrar carruseles de productos de WooCommerce completamente personalizables desde el panel de administración.

- Diseños prediseñados ("presets") listos para usar.
- Personalización de colores, botones, fondo y texto por carrusel.
- Filtros de productos: destacados, en oferta, en stock, categorías específicas.
- Configuración de slides, autoplay, dots y flechas.
- Cache inteligente y variables CSS dinámicas para mejor rendimiento.
- Soporte para plantillas sobreescribibles en el tema (como WooCommerce).

### Características principales

✅ **Presets personalizables:** 4 diseños base, fácilmente extendibles.
🎨 **Colores dinámicos:** define colores por slider (primary, secondary, botones, burbujas, etc).
🛒 **Compatibilidad completa con WooCommerce.**
⚙️ **Opciones visuales:** autoplay, velocidad, número de slides visibles, flechas y dots.
🚀 **Plantillas sobreescribibles:** copia `templates/sliders/slider-1.php` o `templates/cards/card-1.php` en tu tema bajo `/clevers-carousel/` para personalizar el HTML.
🧠 **Sistema de caché avanzado:** evita consultas repetitivas y mejora la velocidad.

== Installation ==

1. Sube la carpeta del plugin al directorio `/wp-content/plugins/`, o instálalo directamente desde el repositorio de WordPress.
2. Activa el plugin desde el menú "Plugins" en WordPress.
3. Crea un nuevo **Product Slider** desde el menú “Product Sliders” en el panel de administración.
4. Configura su diseño, colores y filtros.
5. Inserta el slider en cualquier página o plantilla usando el shortcode:

```
[clevers_slider id="123"]
```

*(Reemplaza `123` por el ID de tu slider.)*

== Frequently Asked Questions ==

= ¿Puedo usarlo sin WooCommerce? =
No. El plugin requiere WooCommerce activo para poder obtener los productos.

= ¿Cómo cambio el diseño de las tarjetas? =
Copia el archivo desde:
```
wp-content/plugins/clevers-product-slider/templates/cards/card-1.php
```
a:
```
wp-content/themes/tu-tema/clevers-carousel/cards/card-1.php
```
y edítalo allí.

= ¿Cómo personalizo los colores? =
Cada slider tiene sus propios campos de color en el editor. También puedes usar CSS variables:

```css
#clevers-slider-123 {
  --clevers-primary: #e63946;
  --clevers-secondary: #1d3557;
}
```

== Screenshots ==
1. Panel de administración con los campos de configuración del slider.
2. Ejemplo de slider de productos en el frontend.
3. Edición de colores por carrusel.
4. Diferentes presets de tarjetas.

== Changelog ==

= 0.2.0 =
* Añadido sistema de colores por slider (CSS variables dinámicas).
* Mejorado el render con caché inteligente.
* Añadido soporte para sobrescribir plantillas en el tema.
* Refactor general del código.

= 0.1.0 =
* Versión inicial: creación de sliders de productos básicos.

== Upgrade Notice ==

= 0.2.0 =
Esta actualización introduce variables CSS por slider. Asegúrate de limpiar la caché del navegador tras actualizar.

== License ==

Este plugin es software libre, licenciado bajo la GPLv2 o posterior.

---

## 💡 Recomendaciones para publicar

1. **Nombre del archivo principal:**
   Debe coincidir con el *slug* que usarás en WordPress.org, ej. `clevers-product-slider.php`.

2. **Text Domain:**
   Ya está correcto: `clevers-carousel`.

3. **Dominio del plugin:**
   Carpeta recomendada: `clevers-product-slider`.

4. **Internacionalización (i18n):**
   Crea el `.pot` con:
   ```bash
   wp i18n make-pot . languages/clevers-carousel.pot
   ```

5. **Validación:**
   Usa el [Plugin Check](https://wordpress.org/plugins/plugin-check/) para verificar estándares.

6. **Commit inicial (SVN o GitHub):**
   ```bash
   svn mkdir https://plugins.svn.wordpress.org/clevers-product-slider/trunk
   svn mkdir https://plugins.svn.wordpress.org/clevers-product-slider/tags/0.2.0
   svn add clevers-product-slider.php assets templates languages readme.txt
   svn ci -m "Initial commit version 0.2.0"
   ```

---

¿Quieres que te genere también un **`clevers-carousel.pot`** con las cadenas listas para traducción y el comando para automatizarlo (WP-CLI o Poedit)? Puedo dejarlo preparado.