<?php

/**
 * @file
 * Themeable functions for Google Custom Search Engine.
 */

function google_cse_get_cse_tag() {
  $display = \Drupal::config('google_cse.settings')->get('google_cse_custom_results_display');

  switch ($display) {
    case 'overlay':
    case 'compact':
    case 'full-width':
      return '<gcse:search gname="google_cse"></gcse:search>';

    case 'two-page':
      return '<gcse:searchbox-only gname="google_cse"></gcse:searchbox-only><gcse:searchresults-only gname="google_cse"></gcse:searchresults-only>';


    case 'two-column':
      return '<gcse:searchbox gname="google_cse"></gcse:searchbox><gcse:searchresults gname="google_cse"></gcse:searchresults>';


    case 'results-only':
      return '<gcse:searchresults-only gname="google_cse"></gcse:searchresults-only>';

    case 'google-hosted':
      return '<gcse:searchbox-only gname="google_cse"></gcse:searchbox-only>';

    default:
      \Drupal::logger('google_cse')->critical('Invalid custom result display %display', array('%display' => $display));
  }
}

/**
 * The search results page can be themed/customized.
 */
function template_preprocess_google_cse_results(&$variables) {
  $query = google_cse_build_query(isset($_GET['query']) ? $_GET['query'] : '', NULL, FALSE);
  $variables['results_searchbox_form'] = $variables['form'] ? \Drupal::formBuilder()->getForm('google_cse_results_searchbox_form') : '';
  $variables['cse_tag'] = google_cse_get_cse_tag();
  // @FIXME
// l() expects a Url object, created from a route name or external URI.
// $variables['noscript'] = t('!google, or enable JavaScript to view them here.', array(
//     '!google' => l(t('View the results at Google'), 'http://' . variable_get('google_cse_domain', 'www.google.com') . '/cse', array(
//       'query' => $query,
//     ))));

  $variables['prefix'] = \Drupal\Component\Utility\Xss::filterAdmin(\Drupal::config('google_cse.settings')->get('google_cse_results_prefix'));
  $variables['suffix'] = \Drupal\Component\Utility\Xss::filterAdmin(\Drupal::config('google_cse.settings')->get('google_cse_results_suffix'));

  if (google_cse_validate_request()) {
    // @FIXME
// The Assets API has totally changed. CSS, JavaScript, and libraries are now
// attached directly to render arrays using the #attached property.
// 
// 
// @see https://www.drupal.org/node/2169605
// @see https://www.drupal.org/node/2408597
// drupal_add_js(drupal_get_path('module', 'google_cse') . '/google_cse_results.js',
                  //array('scope' => 'footer'));

    // @FIXME
// The Assets API has totally changed. CSS, JavaScript, and libraries are now
// attached directly to render arrays using the #attached property.
// 
// 
// @see https://www.drupal.org/node/2169605
// @see https://www.drupal.org/node/2408597
// drupal_add_css('' . variable_get('google_cse_custom_css', '') . '', 'external');

  }
}

/**
 * Validate GET parameters to avoid displaying inappropriate search results.
 */
function google_cse_validate_request() {
  return (
    (empty($_GET['cx']) || $_GET['cx'] == \Drupal::config('google_cse.settings')->get('google_cse_cx')) &&
    (empty($_GET['safe']) || $_GET['safe'] == \Drupal::config('google_cse.settings')->get('google_cse_safe')) &&
    (empty($_GET['sitesearch']) || (($options = google_cse_sitesearch_options()) && isset($options[$_GET['sitesearch']])))
  );
}

/**
 * Form builder for the searchbox forms.
 */
function google_cse_results_searchbox_form($form, &$form_state) {
  $form = array();
  if (\Drupal::config('google_cse.settings')->get('google_cse_results_display') == 'here') {
    $cof = \Drupal::config('google_cse.settings')->get('google_cse_cof_here');
  }
  else {
    $form['#action'] = 'http://' . \Drupal::config('google_cse.settings')->get('google_cse_domain') . '/cse';
    $cof = \Drupal::config('google_cse.settings')->get('google_cse_cof_google');
  }
  $form['#method'] = 'get';
  $form['cx'] = array(
    '#type' => 'hidden',
    '#value' => \Drupal::config('google_cse.settings')->get('google_cse_cx'),
  );
  $form['cof'] = array(
    '#type' => 'hidden',
    '#value' => $cof,
  );
  $form['query'] = array(
    '#type' => 'textfield',
    '#default_value' => isset($_GET['query']) ? $_GET['query'] : '',
  );
  $form['sa'] = array(
    '#type' => 'submit',
    '#value' => t('Search'),
  );
  foreach (google_cse_advanced_settings() as $parameter => $setting) {
    $form[$parameter] = array(
      '#type' => 'hidden',
      '#value' => $setting,
    );
  }
  $form['query']['#size'] = intval(\Drupal::config('google_cse.settings')->get('google_cse_results_searchbox_width'));
  $form['query']['#title'] = t('Enter your keywords');

  google_cse_sitesearch_form($form);
  return $form;
}
