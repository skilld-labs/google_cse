<?php

namespace Drupal\google_cse\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a 'Google CSE' block.
 *
 * @Block(
 *   id = "google_cse",
 *   admin_label = @Translation("Google CSE"),
 *   category = @Translation("Forms"),
 * )
 */
class GoogleCseBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'search Google CSE');
  }

  /**
   * {@inheritdoc}
   */
  protected function baseConfigurationDefaults() {
    // Override default block label.
    return parent::baseConfigurationDefaults() + ['label' => $this->t('Search')];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'google_cse_results',
      '#form' => TRUE,
    ];
  }

}
