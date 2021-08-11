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
    'routing',
    'eikona/tessa/fetcher/attribute'
  ],
  function (_, fieldTemplate, StringCell, Routing, AttributeFetcher) {
    return StringCell.extend({
      fieldTemplate: _.template(fieldTemplate),
      render () {
        const attributeCode = this.column.get('name');

        AttributeFetcher.getAttribute(attributeCode).then((attributeDefinition) => {
          let maxDisplayedAssets = parseInt(attributeDefinition.max_displayed_assets, 10);
          maxDisplayedAssets = Number.isNaN(maxDisplayedAssets) ? 1 : maxDisplayedAssets;

          const value = this.model.get(attributeCode);
          const tessaAssetIds = value ? value.split(',') : [];
          if (tessaAssetIds.length === 0) {
            return this;
          }

          const displayedAssets = tessaAssetIds
            .slice(0, maxDisplayedAssets)
            .map((tessaAssetId) => ({
              url: Routing.generate('eikona_tessa_media_preview', {assetId: tessaAssetId}),
              assetId: tessaAssetId
            }))

          const additionalAssets = tessaAssetIds
            .slice(maxDisplayedAssets)
            .map((tessaAssetId) => ({
              assetId: tessaAssetId
            }))

          this.$el.empty()
            .html(this.fieldTemplate({
              displayedAssets,
              additionalAssets
            }));
        });

        return this;
      }
    });
  });
