<?php

namespace Drupal\google_cse\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Google CSE' block.
 *
 * @Block(
 *   id = "google_cse",
 *   admin_label = @Translation("Google CSE"),
 *   category = @Translation("Forms"),
 * )
 */
class GoogleCseBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Stores the configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The language manager.
   *
   * @var\Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a new GoogleCseBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory, LanguageManagerInterface $language_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->configFactory = $config_factory;
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'search Google CSE');
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['label' => $this->t('Search')];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->configFactory->get('google_cse.settings');
    return [
      //'#theme' => 'google_cse_results',
      //'#form' => TRUE,
      '#markup' => 'search',
      '#attached' => [
        'library' => [
          'core/drupalSettings',
        ],
        'drupalSettings' => [
          'googleCSE' => [
            'cx' => $config->get('cx'),
            'language' => $this->getCseLanguage(),
            'resultsWidth' => intval($config->get('google_cse_results_width', 600)),
            'domain' => $config->get('google_cse_domain', 'www.google.com'),
          ],
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(
      parent::getCacheTags(),
      $this->configFactory->get('google_cse.settings')->getCacheTags()
    );
  }

  protected function getCseLanguage() {
    // @todo Properly inject and add cache tag.
    return google_cse_language();
  }

}
