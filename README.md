# 🧩 Clevers Product Carousel

> **Plugin de WordPress + WooCommerce**  
> Crea sliders de productos profesionales, totalmente personalizables y con presets listos para usar.  
> Desarrollado por [Clevers Devs](https://clevers.dev)

![WordPress Tested](https://img.shields.io/badge/Tested%20up%20to-6.7-blue?logo=wordpress)
![WooCommerce Compatible](https://img.shields.io/badge/WooCommerce-Compatible-success?logo=woocommerce)
![License](https://img.shields.io/badge/license-GPLv2-orange)
![Version](https://img.shields.io/badge/version-1.2.1-blue)

---

## ✨ Características

- 🎨 **4 presets** visuales (personalizables o extendibles).  
- 💅 **Colores configurables** por carrusel (botón, texto, burbuja, borde, fondo, etc.).  
- 🛒 **Filtros dinámicos:** productos destacados, en oferta o por categorías.  
- ⚙️ **Opciones visuales:** autoplay, velocidad, número de slides, flechas, dots.  
- 🚀 **Caché inteligente y variables CSS** por slider.  
- 💾 **Plantillas sobreescribibles** en el tema (estilo WooCommerce).  
- 🧩 **Totalmente responsive** gracias a Slick.js.  

---

## 📦 Instalación

### Desde WordPress
1. Sube el archivo `.zip` desde “Plugins → Añadir nuevo → Subir plugin”.
2. Activa el plugin desde el panel de administración.
3. Crea un nuevo **Product Carousel** desde el menú “Product Carousels”.
4. Inserta el shortcode donde quieras:

```php
[clevers_carousel id="123"]
```

---

## ⚙️ Uso

Cada carrusel permite configurar:
- Diseño (*Preset 1-4*)
- Número de slides visibles
- Autoplay, dots y arrows
- Filtros de productos (categorías, destacados, ofertas)
- Colores personalizados (variables CSS)

### 🎨 Ejemplo de personalización vía CSS

```css
#clevers-product-carousel-123 {
  --clevers-primary: #e63946;
  --clevers-secondary: #1d3557;
  --clevers-button-background: #457b9d;
  --clevers-button-text: #ffffff;
}
```

---

## 🧠 Sobrescribir plantillas

Para personalizar la vista sin tocar el plugin:

Copia el archivo desde:
``` 
wp-content/plugins/clevers-product-carousel/templates/cards/card-1.php
```

a tu tema en:
```
wp-content/themes/tu-tema/clevers-product-carousel/cards/card-1.php
```

El sistema cargará automáticamente tu versión.

---

## 🪝 Hooks y filters para desarrolladores

Puedes extender el plugin con los siguientes hooks principales:

- `clevers_carousel_query_args` *(filter)*: modifica los argumentos de consulta de productos antes de ejecutar la query.
- `clevers_carousel_template_path` *(filter)*: permite ajustar la ruta relativa de templates dentro de `templates/`.
- `clevers_carousel_css_vars` *(filter)*: modifica el arreglo de variables CSS por carrusel antes de imprimirlas inline.
- `clevers_carousel_before_render` *(action)*: se ejecuta justo antes de renderizar el template del carrusel.
- `clevers_carousel_after_render` *(action)*: se ejecuta justo después de renderizar el template del carrusel.

> Nota: También siguen disponibles los hooks con formato namespace como `clevers_carousel/query_args`, `clevers_carousel/before` y `clevers_carousel/after`.

---

## 📷 Capturas

1. Configuración del slider en el panel de administración.  
2. Slider de productos en frontend.  
3. Colores configurables por carrusel.  
4. Ejemplos de presets personalizados.

---

## 🧩 Requisitos

- WordPress 5.8+
- WooCommerce 6.0+
- PHP 7.4 o superior

---

## 🧱 Estructura del plugin

``` 
clevers-product-carousel/
├── clevers-product-carousel.php
├── assets/
│   ├── carousel.css
│   ├── carousel.js
│   ├── block.js
├── templates/
│   ├── carousels/
│   └── cards/
└── languages/
```

---

## 📜 Licencia

GPLv2 o posterior  
https://www.gnu.org/licenses/gpl-2.0.html

---

## 🧩 Créditos

Desarrollado con ❤️ por [Clevers Devs](https://clevers.dev)

¿Quieres contribuir o sugerir mejoras?  
Abre un issue o PR en [github.com/agenciaingenium/Clevers-Woocommerce-Product-Carousel](https://github.com/agenciaingenium/Clevers-Woocommerce-Product-Carousel)
