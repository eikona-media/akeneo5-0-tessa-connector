'use strict';

/**
 * Base extension for tab
 * This represents a main tab of the application, associated with icon, text and column.
 *
 * @author    Julien Sanchez <julien@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define(
  [
    'underscore',
    'oro/translator',
    'pim/form',
    'pim/router',
    'routing',
    'eikona/tessa/connector/templates/menu/tab-tessa'
  ],
  function (
    _,
    __,
    BaseForm,
    router,
    Routing,
    template
  ) {
    return BaseForm.extend({
      template: _.template(template),
      // events: {
      //   'click': 'redirect'
      // },
      className: 'AknHeader-menuItemContainer',

      /**
       * {@inheritdoc}
       */
      initialize (...args) {
        this.config = args[0].config;

        BaseForm.prototype.initialize.apply(this, args);
      },

      /**
       * {@inheritdoc}
       */
      render (...args) {
        this.$el.html(this.template({
          title: this.getLabel(),
          url: Routing.generate('eikona_tessa')
        }));

        return BaseForm.prototype.render.apply(this, args);
      },

      /**
       * Redirect the user to the config destination
       *
       * @param {Event} event
       */
      // redirect (event) {
      //   event.stopPropagation();
      //   event.preventDefault();
      // },

      /**
       * Returns the route of the tab.
       *
       * There is 2 cases here:
       * - The configuration contains a `to` element, so we did a simple redirect to this route.
       * - There is no configuration, so we need to get the first available element of the associated column.
       *   For this, we simply register all the items of the column, sort them by priority then take the first
       *   one.
       *
       * @returns {string|undefined}
       */
      getRoute () {
        if (undefined !== this.config.to) {
          return this.config.to;
        }
        return _.first(_.sortBy(this.items, 'position')).route;

      },

      /**
       * Returns the route parameters.
       *
       * @returns {json}
       */
      getRouteParams () {
        if (undefined !== this.config.to) {
          return this.config.routeParams !== 'undefined' ? this.config.routeParams : {};
        }
        return _.first(_.sortBy(this.items, 'position')).routeParams;

      },

      /**
       * Returns the displayed label of the tab
       *
       * @returns {string}
       */
      getLabel () {
        return __(this.config.title);
      }
    });
  });
