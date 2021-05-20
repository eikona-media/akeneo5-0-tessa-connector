/*
 * system.js
 *
 * @author    Matthias Mahler <m.mahler@eikona.de>
 * @copyright 2017 Eikona AG (http://www.eikona.de)
 */

'use strict';

define(
  [
    'underscore',
    'oro/translator',
    'routing',
    'pim/form',
    'eikona/tessa/connector/template/system/group/configuration',
    'bootstrap.bootstrapswitch'
  ],
  function (_, __, Routing, BaseForm, template) {
    return BaseForm.extend({
      className: 'AknFormContainer AknFormContainer--withPadding',
      events: {
        'change .tessa-config': 'updateModel'
      },
      isGroup: true,
      label: __('tessa.configuration.tab.label'),
      template: _.template(template),
      code: 'oro_config_tessa',

      configure (...args) {
        this.trigger('tab:register', {
          code: this.code,
          label: this.label
        });

        return BaseForm.prototype.configure.apply(this, args);
      },

      render (...args) {
        this.$el.html(this.template({
          baseUrl: this.getFormData().pim_eikona_tessa_connector___base_url ?
            this.getFormData().pim_eikona_tessa_connector___base_url.value : '',
          uiUrl: this.getFormData().pim_eikona_tessa_connector___ui_url ?
            this.getFormData().pim_eikona_tessa_connector___ui_url.value : '',
          username: this.getFormData().pim_eikona_tessa_connector___username ?
            this.getFormData().pim_eikona_tessa_connector___username.value : '',
          apikey: this.getFormData().pim_eikona_tessa_connector___api_key ?
            this.getFormData().pim_eikona_tessa_connector___api_key.value : '',
          systemidentifier: this.getFormData().pim_eikona_tessa_connector___system_identifier ?
            this.getFormData().pim_eikona_tessa_connector___system_identifier.value : '',
          syncinbackground: this.getFormData().pim_eikona_tessa_connector___sync_in_background ?
            this.getFormData().pim_eikona_tessa_connector___sync_in_background.value : false,
          userUsedByTessa: this.getFormData().pim_eikona_tessa_connector___user_used_by_tessa ?
            this.getFormData().pim_eikona_tessa_connector___user_used_by_tessa.value : '',
          disableAssetEditingInAkeneoInUi: this.getFormData().pim_eikona_tessa_connector___disable_asset_editing_in_akeneo_ui ?
            this.getFormData().pim_eikona_tessa_connector___disable_asset_editing_in_akeneo_ui.value : false
        }));

        this.$el.find('.switch').bootstrapSwitch();

        this.delegateEvents();

        return BaseForm.prototype.render.apply(this, args);
      },

      /**
       * Update model after value change
       *
       * @param {Event} event
       */
      updateModel (event) {
        const name = event.target.name;
        const data = this.getFormData();
        let newValue = event.target.value;

        if ($(event.currentTarget).is('input[type="checkbox"]')) {
          newValue = $(event.currentTarget).is(':checked');
        }

        if (name in data) {
          data[name].value = newValue;
        } else {
          data[name] = {value: newValue};
        }
        this.setData(data);
      }
    });
  }
);
