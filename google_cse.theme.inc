<?php
// $Id$

/**
 * @file
 * Themeable functions for Google Custom Search Engine.
 */

/**
 * The search results page can be themed/customized.
 */
function template_preprocess_google_cse_results(&$variables) {

  $query = array(
    'q' => isset($_GET['query']) ? $_GET['query'] : '',
    'cx' => variable_get('google_cse_cx', ''),
    'cof' => variable_get('google_cse_cof_google', 'FORID:0'),
    'sitesearch' => google_cse_sitesearch_default(),
  ) + google_cse_advanced_settings();

  $variables['results_searchbox_form'] = $variables['form'] ? drupal_get_form('google_cse_results_searchbox_form') : '';
  $variables['noscript'] = t('!google, or enable JavaScript to view them here.', array('!google' => l(t('View the results at Google'), 'http://'. variable_get('google_cse_domain', 'www.google.com') .'/cse', array('query' => $query))));
  $variables['prefix'] = filter_xss_admin(variable_get('google_cse_results_prefix', ''));
  $variables['suffix'] = filter_xss_admin(variable_get('google_cse_results_suffix', ''));

  if (google_cse_validate_request()) {
    drupal_add_js($variables['path'] . '/google_cse_results.js', array('scope' => 'footer'));
    drupal_add_js('https://www.google.com/afsonline/show_afs_search.js', array('type' => 'external', 'scope' => 'footer'));
    drupal_add_css('https://www.google.com/cse/style/look/default.css', 'external');
  }
}

/**
 * Display an Add-to-Google button.
 */
function template_preprocess_google_cse_results_gadget(&$variables) {
  $cx = explode(':', variable_get('google_cse_cx', ''));
  $variables['creator'] = rawurlencode($cx[0]);
  $variables['id'] = isset($cx[1]) ? rawurlencode($cx[1]) : '';
}

/**
 * Validate GET parameters to avoid displaying inappropriate search results.
 */
function google_cse_validate_request() {
  return (empty($_GET['cx']) || $_GET['cx'] == variable_get('google_cse_cx', '')) &&
    (empty($_GET['safe']) || $_GET['safe'] == variable_get('google_cse_safe', '')) &&
    (empty($_GET['sitesearch']) || $_GET['sitesearch'] == google_cse_sitesearch_default())
    ? TRUE : FALSE;
}
