'use strict';

/*
 * product-info.js
 * Hier werden die Attribute, die das Produkt in der Form hat, dem Field zur Verfügung gestellt
 * Siehe auch: https://docs.akeneo.com/1.6/cookbook/ui_customization/add_custom_information_to_a_field.html#how-to-add-custom-information-to-a-field
 *
 * @author    Matthias Mahler <m.mahler@eikona.de>
 * @copyright 2017 Eikona AG (http://www.eikona.de)
 */
define(
  [
    'jquery',
    'underscore',
    'pim/form'
  ],
  function ($, _, BaseForm) {
    return BaseForm.extend({
      fields: [],

      configure (...args) {
        this.listenTo(this.getRoot(), 'pim_enrich:form:field:extension:add', this.addFieldExtension);
        this.listenTo(this.getRoot(), 'pim_enrich:form:entity:post_save', this.triggerSave);

        return BaseForm.prototype.configure.apply(this, args);
      },
      addFieldExtension (event) {
        if (!this.fields.includes(event.field)) {
          this.fields.push(event.field);
        }

        const product = this.getFormData();
        event.promises.push($.Deferred()
          .resolve()
          .then(function () {
            const field = event.field;
            field.productAttributes = product.meta;
          }.bind(this))
          .promise());

        return this;
      },

      /**
       * Ruft die Methode `onFormSaved` auf allen in `addFieldExtension` registrierten Feldern auf,
       * sofort diese die Methode `onFormSaved` definiert haben
       */
      triggerSave () {
        this.fields.forEach((field) => {
          if (_.isObject(field) && _.isFunction(field.onFormSaved)) {
            field.onFormSaved();
          }
        });
      }
    });
  }
);
