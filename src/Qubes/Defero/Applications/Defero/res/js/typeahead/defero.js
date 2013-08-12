Defero.Typeahead = {
  const:     {
    all:        "all",
    contacts:   "contacts",
    campaigns:  "campaigns",
    processors: "processors"
  },
  all:       function (query, process) {
    var th = Defero.Typeahead;
    return th._deferoTypeahead(th.const.all, query);
  },
  contacts:  function (query, process) {
    var th = Defero.Typeahead;
    return th._deferoTypeahead(th.const.contacts, query);
  },
  campaigns: function (query, process) {
    var th = Defero.Typeahead;
    return th._deferoTypeahead(th.const.campaigns, query);
  },
  processors: function (query, process) {
    var th = Defero.Typeahead;
    return th._deferoTypeahead(th.const.processors, query);
  },
  _deferoTypeahead: function(type, query) {
    return (new jQuery.DeferoTypeahead(type, query)).getResults();
  }
};
