// Defero is a parent container object for any object that need to be written
// specifically for this project.
var Defero = {};

jQuery(document).ready(function() {
  (function($, window, undefined) {

    // All js triggers should be added here.

    // Defero Triggers
    $(".js-defero-typeahead-all").typeahead(
      {source: Defero.TypeAhead.all, updater: Defero.TypeAhead.update});
    $(".js-defero-typeahead-contacts").typeahead(
      {source: Defero.TypeAhead.contacts, updater: Defero.TypeAhead.update});
    $(".js-defero-typeahead-campaigns").typeahead(
      {source: Defero.TypeAhead.campaigns, updater: Defero.TypeAhead.update});
    $(".js-defero-typeahead-processors").typeahead(
      {source: Defero.TypeAhead.processors, updater: Defero.TypeAhead.update});

    $(document).on("blur", ".navbar-search .search-query", function() {
      $(this).val("");
    });

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
