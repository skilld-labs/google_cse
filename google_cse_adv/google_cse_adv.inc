<?php

/**
 * @file
 * Uses Google Custom Search Engine (CSE) without frames and ads.
 */


/**
 * Sends query to Google's Custom Search Engine and returns response.
 *
 * @param string $keys
 *   The search terms.
 *
 * @param int $offset
 *   The result number to start at.
 *
 * @return string
 *   XML response string.
 */
function google_cse_adv_service($keys, $offset = 0) {
  $page = 0;
  $response = array();

  if (isset($_GET['page'])) {
    $page = $_GET['page'];
  }
  if (isset($response[$keys])) {
    return $response[$keys];
  }

  // Number of results per page. 10 is the default for Google CSE.
  $rows = (int) variable_get('google_cse_adv_results_per_page', 10);

  $query = array(
    'cx' => variable_get('google_cse_cx', ''),
    'client' => 'google-csbe',
    'output' => 'xml_no_dtd',
    'filter' => '1',
    'hl' => google_cse_adv_param_hl(),
    'lr' => google_cse_adv_param_lr(),
    'q' => $keys,
    'num' => $rows,
    'start' => ($page * $rows) + $offset,
    'as_sitesearch' => variable_get('google_cse_limit_domain', ''),
  );

  if (isset($_GET['more'])) {
    $query['+more:'] = urlencode($_GET['more']);
  }

  $url = url('http://www.google.com/cse', array('query' => $query));

  // Get the google response.
  $response = google_cse_adv_get_response($url);

  return $response;
}


/**
 * Given the url with the search we try to do, get response from Google.
 *
 * @param string $url
 *   The Google URL to query.
 *
 * @return string
 *   The response from Google.
 */
function google_cse_adv_get_response($url) {

  $request = new stdClass();
  $request->url = $url;

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $request->url);
  // Return into a variable.
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_TIMEOUT, 30);

  if (module_exists('proxy_settings') && $proxy_host = proxy_settings_host('google_cse_adv')) {

    if ($proxy_port = proxy_settings_port('google_cse_adv')) {
      curl_setopt($curl, CURLOPT_PROXY, $proxy_host . ':' . $proxy_port);
    }
    else {
      curl_setopt($curl, CURLOPT_PROXY, $proxy_host);
    }
    if ($user = proxy_settings_username('google_cse_adv') && $password = proxy_settings_password('google_cse_adv')) {
      curl_setopt($curl, CURLOPT_PROXYUSERPWD, $user . ':' . $password);
    }
  }

  $response[] = curl_exec($curl);
  curl_close($curl);

  return $response;
}


/**
 * Returns the thumbnail properly themed.
 *
 * @param array $img
 *   Image array.
 *
 * @return string
 *   The HTML for the image.
 */
function _google_cse_adv_thumbnail($img) {
  return theme('image', array(
    'path' => $img['value'],
    'alt' => $img['title'],
    'title' => $img['title'],
    'attributes' => array('width' => '100px'),
    'getsize' => FALSE
  ));
}

/**
 * Returns "hl" language param for search request.
 *
 * @return string
 *   The language code.
 */
function google_cse_adv_param_hl() {

  $language = variable_get('google_cse_adv_language', '');
  switch ($language) {
    case 'active':
      global $language;
      return $language->language;

    default:
      return '';
  }
}


/**
 * Returns "lr" language param for search request.
 *
 * @return string
 */
function google_cse_adv_param_lr() {
  switch (variable_get('google_cse_adv_language', '')) {
    case 'active':
      global $language;
      return 'lang_' . $language->language;

    default:
      return '';
  }
}


/**
 * Function to fetch the results xml from Google.
 *
 * @param string $response
 * @param string $keys
 * @param string $conditions
 *
 * @return string
 */
function google_cse_adv_response_results($response, $keys, $conditions) {
  $xml = simplexml_load_string($response);
  $results = array();
  // Number of results.
  $total = 0;

  if (isset($xml->RES->R)) {

    // Cap the result total if necessary.
    // Google will not return more than 1000 results, but RES->M may
    // be higher than this, which messes up our paging. Retain a copy
    // of the original so that themers can still display it.
    // Also, any result beyond pages 8x and 99 tends to repeat themselves, so
    // they are not relevant. Limited then to 150 pages (1500)
    $max_results = variable_get('google_cse_adv_maximum_results', 1500);

    $total = (int) $xml->RES->M;
    $xml->RES->M_ORIGINAL = $total;

    // Is the result accurate?
    if (!google_cse_adv_result_is_accurate($response)) {
      $total = google_cse_adv_get_accurate_num_of_results($keys, $total);
    }

    if ($total > $max_results) {
      $xml->RES->M = $total = $max_results;
    }


    foreach ($xml->RES->R as $result) {

      // Clean the text and remove tags.
      $title = google_cse_adv_clean_str((string) $result->T);

      if ($result->PageMap) {
        $att = $result->PageMap->DataObject->attributes();
        switch ($att['type']) {
          case "cse_image":
            $image_att = $result->PageMap->DataObject->Attribute->attributes();

            // Clean the text.
            $text_snippet = google_cse_adv_clean_str((string) $result->S);

            // Packing image for thumbnail function.
            $img['title'] = $title;
            if (isset($image_att['value'])) {
              $img['value'] = $image_att['value'];
            }

            $snippet = _google_cse_adv_thumbnail($img) . $text_snippet;

            // Clean the text.
            $extra = google_cse_adv_clean_str((string) $result->U);

            $extra = parse_url($extra);
            $extra = $extra['host'];

            break;

          case "metatags":
            // Clean the string.
            $snippet = google_cse_adv_clean_str((string) $result->S);

            // Clean the string.
            $extra = google_cse_adv_clean_str(check_plain((string) $result->U));

            $extra = parse_url($extra);
            $extra = $extra['host'] . " | Document";

            break;
        }
      }
      else {
        if ($result->SL_RESULTS) {
          $snippet = strip_tags((string) $result->SL_RESULTS->SL_MAIN->BODY_LINE->BLOCK->T);
        }
        else {
          $snippet = (string) $result->S;
        }
        // Clean the text.
        $snippet = google_cse_adv_clean_str($snippet);

        // Clean the text.
        $extra = google_cse_adv_clean_str(check_plain((string) $result->U));

        $extra = parse_url($extra);
        $extra = $extra['host'];
      }

      // Results in a Drupal themed way for search.
      $results[] = array(
        'link' => (string) $result->U,
        'title' => $title,
        'snippet' => $snippet,
        'keys' => check_plain($keys),
        'extra' => array($extra),
        'date' => NULL,
      );
    }

    // No pager query was executed - we have to set the pager manually.
    $limit = variable_get('google_cse_adv_results_per_page', 10);
    pager_default_initialize($total, $limit);

  }

  // Allow other modules to alter the number of results.
  drupal_alter('google_cse_num_results', $total);

  return $results;
}

/**
 * Check Return if the response from Google is accurate.
 *
 * Google initially estimates the exact number of results
 * that the search should have.
 *
 * @param string $response
 *   The XML response from Google.
 *
 * @return bool
 *   TRUE if the results are considered accurate.
 */
function google_cse_adv_result_is_accurate($response) {
  $accurate = FALSE;
  // Time to get the response.
  $xml = simplexml_load_string($response);

  // And to check the "accurate" Google variable, if the XT flag exists
  // the search is accurate.
  if (isset($xml->RES->XT)) {
    $accurate = TRUE;
  }

  return $accurate;
}


/**
 * Get the exact (accurate) number of search results to be used in the pager.
 *
 * Otherwise, the pager sometimes will show a different number of pages.
 *
 * @param string $keys
 *   The search keys.
 *
 * @param $total
 *   The initial estimated total.
 *
 * @return int
 *   The accurate total number of results.
 */
function google_cse_adv_get_accurate_num_of_results($keys, $total) {
  // If we do not find an accurate result we will use the first one.
  $num_of_results = $total;
  // We keep this to maximize the accuracy (as google can return != results).
  $max_num_result = 9999999999;
  $result_is_accurate = FALSE;

  $offset = variable_get('google_cse_adv_initial_offset', 400);
  $max_offset = variable_get('google_cse_adv_maximum_results', 1500);
  // Check that results are not being repeated, so no more accuracy possible.
  $repeated_results = 0;

  // Max repetitions before giving up.
  // Too many makes the search slower, too few makes it inaccurate).
  $max_repetitions = 2;
  $previous_result = 0;

  // Loop while result is not accurate, $offset + 100 (limit to avoid infinite).
  while ((!$result_is_accurate) && ($offset < $max_offset) && ($repeated_results < $max_repetitions)) {

    // First, we build the response. The offset will force Google to return.
    // an accurate result.
    $response = google_cse_adv_service($keys, $offset);

    $result_is_accurate = google_cse_adv_result_is_accurate($response[0]);
    $offset += 100;

    $xml = simplexml_load_string($response[0]);
    if (isset($xml->RES->M)) {
      $num_of_results = (int) $xml->RES->M;

      // Sometimes google gives different results. We will keep the biggest.
      if ($max_num_result > $num_of_results) {
        $max_num_result = $num_of_results;
      }

      if ($num_of_results == $previous_result) {
        // Leave if the result is the same.
        $repeated_results += 1;
      }
      $previous_result = $num_of_results;

    }
    else {
      $offset = $max_offset;
    }
  }

  return $max_num_result;
}


/**
 * Clean string of html, tags, etc...
 *
 * @param string $input_str
 *   The original string.
 *
 * @return string
 *   The cleaned output.
 */
function google_cse_adv_clean_str($input_str) {
  $cleaned_str = $input_str;

  if (function_exists('htmlspecialchars_decode')) {
    $cleaned_str = htmlspecialchars_decode($input_str, ENT_QUOTES);
  }

  // Remove possible tags.
  $cleaned_str = strip_tags($cleaned_str);

  return $cleaned_str;
}
