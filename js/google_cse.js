(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.googleCSECustomSearch = {
    attach: function (context, settings) {
      var googleCSEWatermark = function (id) {
        var f = $(id)[0];
        if (f) {
          console.log(f);
          console.log(f.query);
          console.log(f['edit-keys']);
        }
        if (f && (f.query || f['edit-search-block-form--2'] || f['edit-keys'] || f['[data-drupal-selector="search-block-form"]'])) {
          var q = f.query ? f.query : (f['edit-search-block-form--2'] ? f['edit-search-block-form--2'] : f['edit-keys']);
          var n = navigator;
          var l = location;
          if (n.platform == 'Win32') {
            q.style.cssText = 'border: 1px solid #7e9db9; padding: 2px;';
          }
          var b = function () {
            if (q.value == '') {
              q.style.background = '#FFFFFF url(https://www.google.com/cse/intl/' + drupalSettings.googleCSE.language + '/images/google_custom_search_watermark.gif) left no-repeat';
            }
          };
          var f = function () {
            q.style.background = '#ffffff';
          };
          q.onfocus = f;
          q.onblur = b;
  //      if (!/[&?]query=[^&]/.test(l.search)) {
          b();
  //      }
        }
      };
      googleCSEWatermark('[data-drupal-selector="search-block-form"] [data-drupal-form-fields="edit-keys"]');
      googleCSEWatermark('.search-block-form.google-cse');
      googleCSEWatermark('#search-form.google-cse');
      googleCSEWatermark('#google-cse-results-searchbox-form');
    }
  };
})(jQuery, Drupal, drupalSettings);