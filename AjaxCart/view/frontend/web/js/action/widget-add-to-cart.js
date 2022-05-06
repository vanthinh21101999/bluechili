define([
    'jquery',
    'BlueChili_AjaxCart/js/config',
    'BlueChili_AjaxCart/js/action'
], function($, mgsConfig) {
    "use strict";

    jQuery.widget('mgs.widgetAddToCart', jQuery.mgs.action, {
        options: {
            formKey: ''
        },
        _create: function() {
            var self = this;
            this._super();
            this.element.one('mouseup', function() {
                if (!self.element.closest('form').is(":data('mgsCatalogAddToCart')")){
                    self.element.off('click');
                    self._on({
                        'click': self.onClick
                    });
                }
                
            });
        },
        getActionId: function() {
            return 'widget-add-to-cart-' + jQuery.now()
        },
        onClick: function(event) {
            event.preventDefault();
            event.stopImmediatePropagation();
            var postData = this.element.data('post');
            if (postData) {
                this.fire(this.element, this.getActionId(), postData.action, this._preparePostData(postData));
            } else if (this.element.is(":data('mageRedirectUrl')")) {
                var url = this.element.redirectUrl('option', 'url');
                if (url) {
                    this.fire(this.element, this.getActionId(), url, this._preparePostData({action: url}));
                }
            }
        },
        _preparePostData: function(postData) {
            var formData = new FormData();
            var data = postData.data || {};
            for (var name in data) {
                formData.append(name, data[name]);
            }
            formData.append('action_url', postData.action);
            formData.append('form_key', this.options.formKey);
            return formData;
        }
    });

    return jQuery.mgs.widgetAddToCart;
});
