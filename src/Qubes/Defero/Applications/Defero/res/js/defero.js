// Defero is a parent container object for any object that need to be written
// specifically for this project.
var Defero = {};

jQuery(document).ready(function() {
  (function($) {

    // All js triggers should be added here.

    // Defero Triggers
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

    /**
     * HREF helper. Allow redirecting without an anchor tag.
     *
     * @handle js-href
     * @data-href
     */
    $(document).on("click", ".js-href", function() {
      var href = $(this).data("href");
      if(href !== undefined) {
        window.location = href;
      }
    });

  })(jQuery);
});
