// Defero is a parent container object for any object that need to be written
// specifically for this project.
var Defero = {};

jQuery(document).ready(function() {
  (function($) {

    // All js triggers should be added here.
    $(".js-defero-typeahead-all").typeahead({source: Defero.Typeahead.all});
    $(".js-defero-typeahead-contacts").typeahead(
      {source: Defero.Typeahead.contacts}
    );
    $(".js-defero-typeahead-campaigns").typeahead(
      {source: Defero.Typeahead.campaigns}
    );
    $(".js-defero-typeahead-processors").typeahead(
      {source: Defero.Typeahead.processors}
    );

  })(jQuery);
});

