Defero.Typeahead = {
  const:     {
    all:        "all",
    contacts:   "contacts",
    campaigns:  "campaigns",
    processors: "processors"
  },
  all:       function (query, process) {
    return this._deferoTypeahead(Defero.Typeahead.const.all, query);
  },
  contacts:  function (query, process) {
    return this._deferoTypeahead(Defero.Typeahead.const.contacts, query);
  },
  campaigns: function (query, process) {
    return this._deferoTypeahead(Defero.Typeahead.const.campaigns, query);
  },
  processors: function (query, process) {
    return this._deferoTypeahead(Defero.Typeahead.const.processors, query);
  },
  _deferoTypeahead: function(type, query) {
    return (new jQuery.DeferoTypeahead(type, query)).getResults();
  }
};
