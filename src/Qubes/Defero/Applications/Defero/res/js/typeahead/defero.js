Defero.Typeahead = {
  const:     {
    all:        "all",
    contacts:   "contacts",
    campaigns:  "campaigns",
    processors: "processors"
  },
  all:       function (query, process)
  {
    return (
      new jQuery.DeferoTypeahead(
        Defero.Typeahead.const.all, query
      )
      ).getResults();
  },
  contacts:  function (query, process)
  {
    return (
      new jQuery.DeferoTypeahead(
        Defero.Typeahead.const.contacts, query
      )
      ).getResults();
  },
  campaigns: function (query, process)
  {
    return (
      new jQuery.DeferoTypeahead(
        Defero.Typeahead.const.campaigns, query
      )
      ).getResults();
  },
  processors: function (query, process)
  {
    return (
      new jQuery.DeferoTypeahead(
        Defero.Typeahead.const.processors, query
      )
      ).getResults();
  }
};
