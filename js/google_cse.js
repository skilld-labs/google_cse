(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.googleCSECustomSearch = {
    attach: function (context, settings) {
      var getWatermarkBackground = function(value) {
        var googleCSEBaseUrl = 'https://www.google.com/cse/intl/';
        var googleCSEImageUrl = 'images/google_custom_search_watermark.gif';
        var language = drupalSettings.googleCSE.language ? '' : drupalSettings.googleCSE.language + '/';
        return value ? '' : ' url(' + googleCSEBaseUrl + language + googleCSEImageUrl + ') left no-repeat';
      };
      var onFocus = function(e) {
        $(e.target).css('background', '#ffffff');
      };
      var onBlur = function(e) {
        $(e.target).css('background', '#ffffff' + getWatermarkBackground($(e.target).val()));
      };
      var googleCSEWatermark = function (selector) {
        var form = jQuery(selector);
        var searchInputs = $('[data-drupal-selector="edit-keys"]', form);
        if (navigator.platform === 'Win32') {
          searchInputs.css('style', 'border: 1px solid #7e9db9; padding: 2px;');
        }

        searchInputs.blur(onBlur);
        searchInputs.focus(onFocus);
        searchInputs.each(function() {
          var event = new Object();
          event.target = this;
          onBlur(event);
        });
      };

      googleCSEWatermark('[data-drupal-selector="search-block-form"] [data-drupal-form-fields="edit-keys--2"]');
      googleCSEWatermark('[data-drupal-selector="search-block-form"] [data-drupal-form-fields="edit-keys"]');
      googleCSEWatermark('[data-drupal-selector="search-form"]');
    }
  };
})(jQuery, Drupal, drupalSettings);
