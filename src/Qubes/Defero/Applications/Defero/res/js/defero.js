var Defero = {};

jQuery(document).ready(
  function() {
    (function($) {
      $("#nav-search").typeahead({source: Defero.Typeahead.all})
    })(jQuery);
  }
);

