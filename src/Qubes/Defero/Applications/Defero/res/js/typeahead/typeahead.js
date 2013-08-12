(function($, window, undefined) {

  "use strict";

  $.Typeahead = function(action, query) {
    this._action = action;
    this._query = query;
    this._run();
  };

  $.Typeahead.Cache = {};

  $.Typeahead.prototype = {

    _action: null,
    _query: null,
    _results: [],
    _callback: null,

    _run: function() {
      if(this._inCache()) {
        this._results = this._getFromCache();
      } else {
        this._results = this._call();
        this._storeInCache(this._results);
      }
    },

    _call: function() {
      var result = [];

      $.ajax(
        "/typeahead/" + this._action + "/?q=" + this._query,
        {"async": false, "dataType": "json"}
      ).done(function(data) {
        result = data;
      });

      return result;
    },

    _inCache: function() {
      return this._getFromCache() !== undefined;
    },

    _getFromCache: function() {
      return $.Typeahead.Cache[this._action + this._query];
    },

    _storeInCache: function(results) {
      $.Typeahead.Cache[this._action + this._query] = results;
    },

    getResults: function() {
      return this._results;
    },

    setQuery: function(query) {
      this._query = query;
      this._run();
    }
  };
})(jQuery, window);
