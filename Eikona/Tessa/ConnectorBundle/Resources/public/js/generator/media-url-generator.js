'use strict';

define(
  [
    'jquery',
    'underscore',
    'routing',
    'pim/media-url-generator-base'
  ], function (
    $,
    _,
    Routing,
    Base
  ) {
    return {
      tessaPrefix: '%tessa%_',

      /**
       * Get the show media URL
       *
       * @param {string} filePath
       * @param {string} filter
       *
       * @return {string}
       */
      getMediaShowUrl (filePath, filter) {
        if (this.isTessaImage(filePath)) {
          const assetId = filePath.substr(this.tessaPrefix.length);
          return Routing.generate('eikona_tessa_media_preview', {assetId});
        }

        return Base.getMediaShowUrl(filePath, filter);
      },

      /**
       * Get the download media URL
       *
       * @param {string} filePath
       *
       * @return {string}
       */
      getMediaDownloadUrl (filePath) {
        if (this.isTessaImage(filePath)) {
          const assetId = filePath.substr(this.tessaPrefix.length);
          return Routing.generate('eikona_tessa_media_detail', {assetId});
        }

        return Base.getMediaDownloadUrl(filePath);
      },

      isTessaImage (filePath) {
        return typeof filePath === 'string' && filePath.startsWith(this.tessaPrefix);
      }
    };
  }
);
