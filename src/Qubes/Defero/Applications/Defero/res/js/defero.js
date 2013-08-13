// Defero is a parent container object for any object that need to be written
// specifically for this project.
var Defero = {};

jQuery(document).ready(function() {
  (function($, window, undefined) {

    // All js triggers should be added here.

    // Defero Triggers
    $(".js-defero-typeahead-all").typeahead(
      {source: Defero.TypeAhead.all});
    $(".js-defero-typeahead-contacts").typeahead(
      {source: Defero.TypeAhead.contacts});
    $(".js-defero-typeahead-campaigns").typeahead(
      {source: Defero.TypeAhead.campaigns});
    $(".js-defero-typeahead-processors").typeahead(
      {source: Defero.TypeAhead.processors});

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

  })(jQuery, window);
});
