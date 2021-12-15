define(function (require) {

  var Backbone = require('backbone'),
    Pager = require('model/filter/pager');

  /**
   * Filter model
   *
   * @constructor
   */
  var Filter = Backbone.Model.extend({
    defaults: {
      reload: 0,
      sort: {
        column: null,
        order: null
      }
    },
    initialize: function () {
      this.values = new Backbone.Collection();
      this.values.on('change:value', this.onFieldValueChange, this);

      this.Pager = new Pager(this.get('pagination') || {});
      this.Pager.url = _.bind(this.url, this);
      this.Pager.on('change', this.updatePager, this);

      this.updatePager();

      this.on('change:view', this.onViewChange, this);
    },
    getPager: function () {
      return this.Pager;
    },
    /**
     *
     * @returns {String}
     */
    url: function (params) {
      var baseUrl = this.get('section').url;
      var separator = baseUrl.indexOf("?") === -1 ? "?" : "&";

      return baseUrl + separator + this.getStringParameters(params);
    },
    /**
     *
     * @returns {undefined}
     */
    getContent: function () {
      var self = this;
      Backbone.sync("read", this, {

        success: function (data) {
          self.set({
            view: data.filter.view,
            pagination: data.filter.pagination,
            sort: data.filter.sort
          });


          self.trigger("filtered", data);

          self.Pager.set(data.filter.pagination);

        },

        error: function (e) {
          //console.log(e);
        }
      });
    },

    updatePager: function () {
      if (this.get('section') === undefined)
        return false;

      //this.Pager.set(this.get('pagination') || {});

      var self = this,
        baseUrl = this.get('section') ? this.get('section').url : null,
        pages = this.Pager.get('links') || [],
        separator = baseUrl.indexOf("?") === -1 ? "?" : "&",
        count = this.Pager.get('per_page');

      var links = {};

      $.each(pages, function (i, page) {
        var params = {count: count};

        if (page > 1)
          params.page = page;

        var parametersString = self.getStringParameters(params);
        links[page] = parametersString.length ? baseUrl + separator + parametersString : baseUrl;

      });

      this.Pager.get('pages').links = links;


      var parametersString = self.getStringParameters({count: count});
      this.Pager.get('pages').first_link = parametersString.length ?
        baseUrl + separator + parametersString : baseUrl;

      var parametersString = self.getStringParameters({count: count, page: this.Pager.get('pages').last});
      this.Pager.get('pages').last_link = parametersString.length ?
        baseUrl + separator + parametersString : baseUrl;
    },

    getFilterState: function (params) {
      var sort = this.get('sort');
      var defaults = this.get('defaults');

      var values = {};

      this.values.each(function (model) {
        var val = model.get('value');
        if (val && 'object' === typeof val) {
          if (val.hasOwnProperty('min')) {
            if (!val.min) {
              delete (val.min);
            }
          }
          if (val.hasOwnProperty('max')) {
            if (!val.max) {
              delete (val.max);
            }
          }
          if (_.isEmpty(val)) {
            val = null
          }
        }
        if (null !== val) {
          values[model.get('name')] = val
        }
      });

      var state = _.extend(
        {},
        values,
        params || {}
      );

      if ($.inArray(sort.order, ['desc', 'asc']) !== -1 && (sort.order != defaults.order || sort.column != defaults.column)) {
        state.order = sort.order;
        state.column = sort.column;
      }

      var page_count = (params ? params.count : null) || this.Pager.get('per_page');

      if (page_count && page_count != defaults.count)
        state.count = page_count;
      else
        delete state.count;

      return state;
    },
    getStringParameters: function (params) {
      return jQuery.param(this.getFilterState(params));
    },
    nextSort: function (sort) {
      var nextSort = 'none';
      switch (sort) {
        case 'asc':
          nextSort = 'desc';
          break;
        case 'none':
          nextSort = 'asc';
          break;
      }

      return nextSort;
    },

    reset: function (data) {
      this.set({
        defaults: data.defaults,
        enabled_views: data.enabled_views,
        filterState: data.filterState,
        pagination: data.pagination,
        schema: data.schema,
        state: data.state,
        section: data.section,
        sort: data.sort
      }, {silent: true});


      this.Pager.set(data.pagination);

      this.trigger('reset', this);

      this.updatePager();
    },
    onViewChange: function () {
      this.Pager.trigger('change');
    },
    onFieldValueChange: function (value) {
      this.trigger('change:value', value)
    }
  });

  return Filter;
});