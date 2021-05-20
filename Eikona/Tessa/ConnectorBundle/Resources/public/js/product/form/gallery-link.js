'use strict';

define(
  [
    'underscore',
    'pim/form',
    'eikona/tessa/connector/template/product-edit-form/gallery-link',
    'routing',
    'pim/user-context'
  ],
  function (
    _,
    BaseForm,
    template,
    Routing,
    UserContext
  ) {
    return BaseForm.extend({
      tagName: 'a',

      className: 'AknDropdown-menuLink btn-gallery',

      template: _.template(template),

      /**
       * {@inheritdoc}
       */
      configure (...args) {
        UserContext.off('change:catalogLocale change:catalogScope', this.render);
        this.listenTo(UserContext, 'change:catalogLocale change:catalogScope', this.render);

        return BaseForm.prototype.configure.apply(this, args);
      },

      /**
       * {@inheritdoc}
       */
      render () {
        if (!this.getFormData().meta) {
          return;
        }

        this.$el.html(this.template());
        this.$el.attr('target', '_blank');
        this.$el.attr('href', Routing.generate(
          'eikona_tessa_media_gallery_product',
          {
            id: this.getFormData().meta.id,
            dataLocale: UserContext.get('catalogLocale'),
            dataScope: UserContext.get('catalogScope')
          }
        ));

        return this;
      }
    });
  }
);
