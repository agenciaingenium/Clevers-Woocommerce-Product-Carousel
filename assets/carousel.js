(function ($) {
  function initCleversSliders(ctx) {
    var $wraps = $('.clevers-product-carousel .slick-carousel', ctx || document);
    if (!$wraps.length) return;

    $wraps.each(function () {
      var $el = $(this);
      if ($el.hasClass('slick-initialized')) return;

      var slides = parseInt($el.data('slides')) || 4;
      var autoplay = String($el.data('autoplay')) === 'true';
      var speed = parseInt($el.data('speed')) || 3000;
      var dots = String($el.data('dots')) === 'true';
      var arrows = String($el.data('arrows')) === 'true';

      // Ensure slick is available
      if (typeof $el.slick !== 'function') {
        console.warn('Clevers Product Carousel: Slick not loaded');
        return;
      }

      $el.slick({
        slidesToShow: slides,
        slidesToScroll: 1,
        autoplay: autoplay,
        autoplaySpeed: speed,
        dots: dots,
        arrows: arrows,
        responsive: [
          { breakpoint: 1024, settings: { slidesToShow: Math.min(slides, 3) } },
          { breakpoint: 768, settings: { slidesToShow: Math.min(slides, 2) } },
          { breakpoint: 480, settings: { slidesToShow: 1 } }
        ]
      });
    });
  }

  $(function () {
    initCleversSliders(document);
  });

  $(document.body).on('updated_wc_div wc_fragments_refreshed post-load', function () {
    initCleversSliders(document);
  });

})(jQuery);