'use strict';

define(['jquery', 'underscore', 'routing'], function($, _, Routing) {
  return {
    attributePromises: [],

    /**
     * @return {Promise}
     */
    getAttribute: function(attributeCode, useCache = true) {
      if (!_.has(this.attributePromises, attributeCode) || !useCache) {
        this.attributePromises[attributeCode] = $.getJSON(Routing.generate(
          'pim_enrich_attribute_rest_get',
          { identifier: attributeCode }
        ));
      }
      return this.attributePromises[attributeCode];
    },

    clear: function() {
      this.attributePromises = null;
    },
  }
});
