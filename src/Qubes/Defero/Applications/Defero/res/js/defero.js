var Defero = {
  Typeahead: {
    all: function(query, process) {
      return (new jQuery.Typeahead("all", query)).getResults();
    },
    contacts: function(query, process) {
      return (new jQuery.Typeahead("contacts", query)).getResults();
    },
    campaigns: function(query, process) {
      return (new jQuery.Typeahead("campaigns", query)).getResults();
    }
  }
};

jQuery(document).ready(function() {
  (function($) {
    $("#nav-search").typeahead({source: Defero.Typeahead.all})
  })(jQuery);
});

