'use strict';

define(
  [
    'pim/field',
    'underscore',
    'eikona/tessa/connector/templates/product/field/mam',
    'pim/form-builder',
    'routing',
    'oro/loading-mask'
  ], function (
    Field,
    _,
    fieldTemplate,
    FormBuilder,
    Routing,
    LoadingMask) {

    return Field.extend({
      productAttributes: {}, // wird über product-info.js injected
      fieldTemplate: _.template(fieldTemplate),
      tessaAssets: {},
      modalBox: null,
      model: null,

      events: {
        'change input[name=asset-sids]': 'updateModel',
        'click .add-asset': 'openModal',
        'click .js-remove-asset': 'onToggleAssetClick'
      },

      /**
       * Initialize
       *
       * @param args
       */
      initialize (...args) {
        this.resetTessaAssets();

        Field.prototype.initialize.apply(this, args);

        this.modalTemplate = _.template('\
          <div class="AknFullPage">\
            <div class="AknFullPage-content">\
              <div>\
                <div class="AknFullPage-titleContainer">\
                  <% if (typeof subtitle !== \'undefined\') { %>\
                    <div class="AknFullPage-subTitle"><%- subtitle %></div>\
                  <% } %>\
                  <div class="AknFullPage-title"><%- title %></div>\
                  <% if (typeof innerDescription !== \'undefined\') { %>\
                    <div class="AknFullPage-description">\
                      <%- innerDescription %>\
                    </div>\
                  <% } %>\
                </div>\
                <div class="modal-body"><%= content %></div>\
              </div>\
            </div>\
          </div>\
          <div class="AknFullPage-cancel cancel"></div>\
        ');
      },

      /**
       * Render
       *
       * @param context
       * @returns {*}
       */
      renderInput (context) {
        const value = context.value.data === null ? '' : context.value.data;

        if (!this.tessaAssets.hasOwnProperty(this.getCurrentTessaAssetKey())) {
          this.setTessaAssetsFromSids(value);
        }

        const assets = this.tessaAssets[this.getCurrentTessaAssetKey()].map((asset) => ({
          id: asset.id,
          url: Routing.generate('eikona_tessa_media_preview', {assetId: asset.id}),
          linkUrl: Routing.generate('eikona_tessa_media_detail', {assetId: asset.id}),
          markedToRemove: asset.markedToRemove
        }));

        return this.fieldTemplate({
          value,
          assets,
          canEditAssetsInAkeneoUi: context.attribute.meta.canEditAssetsInAkeneoUi,
          isReadOnly: !this.isEditable()
        });
      },

      updateModel () {
        // Nothing to do
      },

      /**
       * Öffnet den Tessa-Dialog zum Auswählen von Assets
       *
       * @returns {*}
       */
      openModal () {
        if (!this.isEditable()) {
          return;
        }

        $(window)
          .on(`message.${this.getCurrentTessaAssetKey()}`, this.receiveMessage.bind(this));

        FormBuilder.build('eikon-tessa-asset-selection-form')
          .then(function (form) {

            // Modal
            this.modalBox = new Backbone.BootstrapModal({
              modalOptions: {
                backdrop: 'static',
                keyboard: false
              },
              allowCancel: true,
              okCloses: false,
              title: _.__('tessa.asset management.title'),
              content: '',
              cancelText: _.__('tessa.asset management.cancel'),
              okText: _.__('tessa.asset management.confirm'),
              template: this.modalTemplate
            });
            this.modalBox.$el.addClass('EikonModalAssetsSelection');
            this.modalBox.open();

            form.setElement(this.modalBox.$('.modal-body'))
              .render();

            this.modalBox.$el.find('iframe')
              .on('load', this.onIframeReady.bind(this))
              .prop('src', this.getUrl());

            this.modalBox.on('hidden', () => {
              $(window)
                .off(`message.${this.getCurrentTessaAssetKey()}`);
            });

            // Lademaske
            const loadingMask = new LoadingMask();
            loadingMask.render()
              .$el
              .appendTo(this.modalBox.$el.find('.modal-body'))
              .css({
                'position': 'absolute',
                'width': '100%',
                'height': '100%',
                'top': '0',
                'left': '0'
              });
            loadingMask.show();

            setTimeout(function () {
              loadingMask.hide()
                .$el
                .remove();
            }, 5000);

          }.bind(this));

      },

      /**
       * Wird gerufen, wenn neue Assets im Tessa-Dialog
       * ausgewählt und gespeichert wurden
       *
       * @param event
       */
      receiveMessage (event) {
        const receivedData = JSON.parse(event.originalEvent.data);
        const sids = receivedData.map((value) => value.position_asset_system_id);

        this.setTessaAssetsFromSids(sids);
        this.syncModelFromTessaAssets();

        if (this.modalBox) {
          this.modalBox.close();
        }
      },

      /**
       * Wird gerufen, wenn die iFrame für den Tessa-Dialog
       * bereit ist. Sendet die aktuellen Assets an den Dialog.
       *
       * @param e
       */
      onIframeReady (e) {
        const iframe = e.target;
        const iframeContent = iframe.contentWindow;

        if (!iframe.src) {
          return;
        }

        const currentValue = this.getCurrentValue();
        if (currentValue.data) {
          iframeContent.postMessage(JSON.stringify({
            'selected': this.getCurrentValue()
              .data
              .split(',')
          }), '*');
        } else {
          iframeContent.postMessage(JSON.stringify({}), '*');
        }
      },

      /**
       * Erzeugt die URL für den Tessa-Dialog
       *
       * @returns {string}
       */
      getUrl () {
        let prefix = 'P-';
        if (this.productAttributes.model_type === 'product_model') {
          prefix = 'PM-';
        }

        const identifier = (this.productAttributes.model_type === 'product_model')
          ? this.context.entity.code
          : this.context.entity.identifier;

        const data = {
          ProductId: prefix + this.productAttributes.id,
          identifier,
          attribute: JSON.stringify({
            code: this.attribute.code,
            type: this.attribute.type,
            labels: this.getAttributeLabelsForTessa(this.attribute.labels),
            'allowed_extensions': this.attribute.allowed_extensions,
            'max_assets': this.attribute.max_characters
          }),
          context: JSON.stringify({
            locale: this.getCurrentValue().locale,
            scope: this.getCurrentValue().scope,
            data: this.getCurrentValue().data
          })
        };

        return Routing.generate('eikona_tessa_media_select', {
          data: jQuery.param(data)
        });
      },

      getAttributeLabelsForTessa(labels) {
        const priorities = {
          de: ['de_DE', 'de_AT', 'de_CH'],
          en: ['en_US', 'en_GB'],
          fr: ['fr_FR', 'fr_BE', 'fr_CH'],
          es: ['es_ES'],
        };

        return ['de', 'en', 'fr', 'es'].reduce((labelsForTessa, language) => {
          const priority = priorities[language];
          // Try to find a label from the priority list
          for (let i = 0; i < priority.length; i++) {
            const localeCode = priority[i];
            if (labels[localeCode]) {
              labelsForTessa[localeCode] = labels[localeCode];
              return labelsForTessa;
            }
          }

          // Fallback: Try to find a label for the locale
          const localeCode = Object.keys(labels).find((l) => l.substr(0, 2) === language);
          if (localeCode) {
            labelsForTessa[localeCode] = labels[localeCode];
          }

          return labelsForTessa;
        }, {});
      },

      /**
       * Click-Handler für den Asset-Löschen-Button
       *
       * @param event
       */
      onToggleAssetClick (event) {
        if (!this.isEditable()) {
          return;
        }

        const assetIdToRemove = $(event.currentTarget)
          .attr('data-asset-id');
        this.toggleAssetRemoval(assetIdToRemove);
      },

      /**
       * Markiert/Demarkiert ein Asset zum Löschen
       *
       * @param {string} assetId
       */
      toggleAssetRemoval (assetId) {
        const tessaAssetToToggle = this.tessaAssets[this.getCurrentTessaAssetKey()]
          .find((tessaAsset) => tessaAsset.id === assetId);

        tessaAssetToToggle.markedToRemove = !tessaAssetToToggle.markedToRemove;

        this.syncModelFromTessaAssets();
      },

      /**
       * Füllt das Array "this.tessaAssets" anhand eines Arrays mit SIDs
       * oder einem kommaseparierten String mit SIDs
       *
       * @param {Array<string>|string}sids
       */
      setTessaAssetsFromSids (sids = []) {
        let tessaSids = [];
        if (typeof sids === 'string' && sids.length) {
          tessaSids = sids.split(',');
        } else if (Array.isArray(sids)) {
          tessaSids = sids;
        }
        this.tessaAssets[this.getCurrentTessaAssetKey()] = tessaSids.map((sid) => ({
          id: sid,
          markedToRemove: false
        }));
      },

      /**
       * Wird von product-info.js nach dem Speichern ausgelöst
       */
      onFormSaved () {
        this.resetTessaAssets();
      },

      /**
       * Synchronosiert den Wert des Models mit den Werten aus "this.tessaAssets"
       * (nur für aktuelle Locale und Scope)
       */
      syncModelFromTessaAssets () {
        const assets = this.tessaAssets[this.getCurrentTessaAssetKey()]
          .filter((tessaAsset) => !tessaAsset.markedToRemove)
          .map((tessaAsset) => tessaAsset.id)
          .join(',');

        this.setCurrentValue(assets);
        this.render();
      },

      resetTessaAssets () {
        this.tessaAssets = {};
      },

      /**
       * Gibt den Key für den temporären Cache zurück
       * Beinhaltet locale und scope
       *
       * @returns {string}
       */
      getCurrentTessaAssetKey () {
        const locale = this.attribute.localizable ? this.context.locale : void 0;
        const scope = this.attribute.scopable ? this.context.scope : void 0;
        return this.buildTessaAssetsKey(locale, scope);
      },

      /**
       * Erzeugt einen Key für den temporären Cache
       *
       * @param locale
       * @param scope
       * @returns {string}
       */
      buildTessaAssetsKey (locale, scope) {
        return `${locale || 'null'}-${scope || 'null'}`;
      }
    });
  }
);
