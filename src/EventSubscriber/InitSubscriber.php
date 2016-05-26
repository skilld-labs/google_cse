<?php /**
 * @file
 * Contains \Drupal\google_cse\EventSubscriber\InitSubscriber.
 */

namespace Drupal\google_cse\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InitSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [KernelEvents::REQUEST => ['onEvent', 0]];
  }

  public function onEvent() {
    // @FIXME
// The Assets API has totally changed. CSS, JavaScript, and libraries are now
// attached directly to render arrays using the #attached property.
// 
// 
// @see https://www.drupal.org/node/2169605
// @see https://www.drupal.org/node/2408597
// drupal_add_js(array(
//     'googleCSE' => array(
//       'cx' => variable_get('google_cse_cx', ''),
//       'language' => google_cse_language(),
//       'resultsWidth' => intval(variable_get('google_cse_results_width', 600)),
//       'domain' => variable_get('google_cse_domain', 'www.google.com'),
//     ),
//   ), 'setting');

  }

}
