Defero.TypeAhead = {
  const:            {
    all:        "all",
    contacts:   "contacts",
    campaigns:  "campaigns",
    processors: "processors"
  },
  all:              function(query) {
    var th = Defero.TypeAhead;
    return th._deferoTypeAhead(th.const.all, query);
  },
  contacts:         function(query) {
    var th = Defero.TypeAhead;
    return th._deferoTypeAhead(th.const.contacts, query);
  },
  campaigns:        function(query) {
    var th = Defero.TypeAhead;
    return th._deferoTypeAhead(th.const.campaigns, query);
  },
  processors:       function(query) {
    var th = Defero.TypeAhead;
    return th._deferoTypeAhead(th.const.processors, query);
  },
  _deferoTypeAhead: function(type, query) {
    return (new jQuery.DeferoTypeAhead(type, query)).getResults();
  },
  update:           function(item) {
    this.$element[0].value = item;
    this.$element[0].form.submit();
    return item;
  }
};
