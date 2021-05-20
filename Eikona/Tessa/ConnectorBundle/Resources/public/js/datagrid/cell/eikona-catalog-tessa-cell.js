/*
 * eikona-catalog-tessa-cell.js
 *
 * Rendert das Tessa-Attribut (eikona_catalog_tessa) im DataGrid
 *
 * @author    Timo Müller <t.mueller@eikona-media.de>
 * @copyright 2018 Eikona AG (http://www.eikona.de)
 */

/* global define */
'use strict';

define(
  [
    'underscore',
    'eikona/tessa/connector/datagrid/cell/template',
    'oro/datagrid/string-cell',
    'routing'
  ],
  function (_, fieldTemplate, StringCell, Routing) {
    return StringCell.extend({
      fieldTemplate: _.template(fieldTemplate),
      render () {
        const value = this.model.get(this.column.get('name'));
        const tessaAssetIds = value ? value.split(',') : [];
        let showUrl = '';

        if (tessaAssetIds.length <= 0) {
          return this;
        }

        const firstAssetId = tessaAssetIds[0];
        showUrl = Routing.generate('eikona_tessa_media_preview', {assetId: firstAssetId});

        this.$el.empty()
          .html(this.fieldTemplate({
            url: showUrl,
            assets: tessaAssetIds
          }));

        return this;
      }
    });
  });
