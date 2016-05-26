<?php

namespace Drupal\google_cse\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Google CSE global settings.
 *
 * @todo Move part of settings to search plugin configuration.
 */
class AdminForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'google_cse_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['google_cse.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('google_cse.settings');

    $form['google_cse'] = array(
      '#title' => $this->t('Google CSE'),
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    );

    $form['google_cse']['cx'] = array(
      '#title' => $this->t('Google Custom Search Engine ID'),
      '#type' => 'textfield',
      '#default_value' => $config->get('cx'),
      '#description' => $this->t('Enter your <a target="_blank" href="http://www.google.com/cse/manage/all">Google CSE unique ID</a> (click on control panel).'),
    );

    $form['google_cse']['results_tab'] = array(
      '#title' => $this->t('Search results tab name'),
      '#type' => 'textfield',
      '#maxlength' => 50,
      '#size' => 60,
      '#description' => $this->t('Enter a custom name of the tab where search results are displayed (defaults to %google).', array('%google' => $this->t('Google'))),
      '#default_value' => $config->get('results_tab'),
    );

    $form['google_cse']['results_width'] = array(
      '#title' => $this->t('Search results frame width'),
      '#type' => 'textfield',
      '#maxlength' => 4,
      '#size' => 6,
      '#description' => $this->t('Enter the desired width, in pixels, of the search frame.'),
      '#default_value' => $config->get('results_width'),
    );

    $form['google_cse']['cof_here'] = array(
      '#title' => $this->t('Ad format on this site'),
      '#type' => 'radios',
      '#default_value' => $config->get('cof_here'),
      '#options' => array(
        'FORID:9' => $this->t('Right'),
        'FORID:10' => $this->t('Top and right'),
        'FORID:11' => $this->t('Top and bottom'),
      ),
      '#description' => $this->t('Ads on the right increase the width of the iframe. Non-profit organizations can disable ads in the Google CSE control panel.'),
    );

    $form['google_cse']['cof_google'] = array(
      '#title' => $this->t('Ad format on Google'),
      '#type' => 'radios',
      '#default_value' => $config->get('cof_google'),
      '#options' => array(
        'FORID:0' => $this->t('Right'),
        'FORID:1' => $this->t('Top and bottom'),
      ),
      '#description' => $this->t('AdSense ads are also displayed when the CSE links or redirects to Google.'),
    );

    $form['google_cse']['results_prefix'] = array(
      '#title' => $this->t('Search results prefix text'),
      '#type' => 'textarea',
      '#cols' => 50,
      '#rows' => 4,
      '#description' => $this->t('Enter text to appear on the search page before the search form.'),
      '#default_value' => $config->get('results_prefix'),
    );

    $form['google_cse']['results_suffix'] = array(
      '#title' => $this->t('Search results suffix text'),
      '#type' => 'textarea',
      '#cols' => 50,
      '#rows' => 4,
      '#description' => $this->t('Enter text to appear on the search page after the search form and results.'),
      '#default_value' => $config->get('results_suffix'),
    );

    $form['google_cse']['results_searchbox_width'] = array(
      '#title' => $this->t('Google CSE block searchbox width'),
      '#type' => 'textfield',
      '#maxlength' => 4,
      '#size' => 6,
      '#description' => $this->t('Enter the desired width, in characters, of the searchbox on the Google CSE block.'),
      '#default_value' => $config->get('results_searchbox_width'),
    );

    $form['google_cse']['results_display'] = array(
      '#title' => $this->t('Display search results'),
      '#type' => 'radios',
      '#default_value' => $config->get('results_display'),
      '#options' => array(
        'here' => $this->t('On this site (requires JavaScript)'),
        'google' => $this->t('On Google'),
      ),
      '#description' => $this->t('Search results for the Google CSE block can be displayed on this site, using JavaScript, or on Google, which does not require JavaScript.'),
    );

    $form['google_cse']['results_display_images'] = array(
      '#title' => $this->t('Display thumbnail images in the search results'),
      '#type' => 'checkbox',
      '#description' => $this->t('If set, search result snippets will contain a thumbnail image'),
      '#default_value' => $config->get('results_display_images'),
    );

    $form['google_cse']['sitesearch'] = array(
      '#title' => $this->t('SiteSearch settings'),
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );

    $form['google_cse']['sitesearch']['sitesearch'] = array(
      '#title' => $this->t('SiteSearch domain'),
      '#type' => 'textarea',
      '#cols' => 50,
      '#rows' => 4,
      '#description' => $this->t('If set, users will be presented with the option of searching only on the domain(s) specified rather than using the CSE. Enter one domain or URL path followed by a description (e.g. <em>example.com/user Search users</em>) on each line.'),
      '#default_value' => $config->get('sitesearch'),
    );

    $form['google_cse']['sitesearch']['sitesearch_form'] = array(
      '#title' => $this->t('SiteSearch form element'),
      '#type' => 'radios',
      '#options' => array(
        'radios' => $this->t('Radio buttons'),
        'select' => $this->t('Select'),
      ),
      '#description' => $this->t('Select the type of form element used to present the SiteSearch option(s).'),
      '#default_value' => $config->get('sitesearch_form'),
    );

    $form['google_cse']['sitesearch']['sitesearch_option'] = array(
      '#title' => $this->t('CSE search option label'),
      '#type' => 'textfield',
      '#maxlength' => 50,
      '#size' => 60,
      '#description' => $this->t('Customize the label for CSE search if SiteSearch is enabled (defaults to %search-web).', array('%search-web' => $this->t('Search the web'))),
      '#default_value' => $config->get('sitesearch_option'),
    );

    $form['google_cse']['sitesearch']['sitesearch_default'] = array(
      '#title' => $this->t('Default to using the SiteSearch domain'),
      '#type' => 'checkbox',
      '#description' => $this->t('If set, searches will default to using the first listed SiteSearch domain rather than the CSE.'),
      '#default_value' => $config->get('sitesearch_default'),
    );

    $form['google_cse']['advanced'] = array(
      '#title' => $this->t('Advanced settings'),
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );

    $form['google_cse']['advanced']['domain'] = array(
      '#title' => $this->t('Search domain'),
      '#type' => 'textfield',
      '#maxlength' => 64,
      '#description' => $this->t('Enter the Google domain to use for search results, e.g. <em>www.google.com</em>.'),
      '#default_value' => $config->get('domain'),
    );

    $form['google_cse']['advanced']['limit_domain'] = array(
      '#title' => $this->t('Limit results to this domain'),
      '#type' => 'textfield',
      '#maxlength' => 64,
      '#description' => $this->t('Enter the domain to limit results on
      (only display results for this domain) <em>www.google.com</em>.'),
      '#default_value' => $config->get('limit_domain'),
    );

    $form['google_cse']['advanced']['cr'] = array(
      '#title' => $this->t('Country restriction'),
      '#type' => 'textfield',
      '#default_value' => $config->get('cr'),
      '#description' => $this->t('Enter a 9-letter country code, e.g. <em>countryNZ</em>, and optional boolean operators, to restrict search results to documents (not) originating in particular countries. See the <a target="_blank" href="https://developers.google.com/custom-search/docs/xml_results#crsp"><em>cr</em> parameter</a>.'),
    );

    $form['google_cse']['advanced']['gl'] = array(
      '#title' => $this->t('Country boost'),
      '#type' => 'textfield',
      '#default_value' => $config->get('gl'),
      '#description' => $this->t('Enter a 2-letter country code, e.g. <em>uk</em>, to boost documents written in a particular country. See the <a target="_blank" href="https://developers.google.com/custom-search/docs/xml_results#glsp"><em>gl</em> parameter</a>.'),
    );

    $form['google_cse']['advanced']['hl'] = array(
      '#title' => $this->t('Interface language'),
      '#type' => 'textfield',
      '#default_value' => $config->get('hl'),
      '#description' => $this->t('Enter a supported 2- or 5-character language code, e.g. <em>fr</em>, to set the language of the user interface. See the <a target="_blank" href="https://developers.google.com/custom-search/docs/xml_results#hlsp"><em>hl</em> parameter</a>.'),
    );

    $form['google_cse']['advanced']['locale_hl'] = array(
      '#title' => $this->t('Set interface language dynamically'),
      '#type' => 'checkbox',
      '#default_value' => $config->get('locale_hl'),
      '#description' => $this->t('The language restriction can be set dynamically if the locale module is enabled. Note the locale language code must match one of the <a target="_blank" href="https://developers.google.com/custom-search/docs/xml_results#interfaceLanguages">supported language codes</a>.'),
    );

    $form['google_cse']['advanced']['ie'] = array(
      '#title' => $this->t('Input encoding'),
      '#type' => 'textfield',
      '#default_value' => $config->get('ie'),
      '#description' => $this->t('The default <em>utf-8</em> is recommended. See the <a target="_blank" href="https://developers.google.com/custom-search/docs/xml_results#iesp"><em>ie</em> parameter</a>.'),
    );

    $form['google_cse']['advanced']['lr'] = array(
      '#title' => $this->t('Language restriction'),
      '#type' => 'textfield',
      '#default_value' => $config->get('lr'),
      '#description' => $this->t('Enter a supported 7- or 10-character language code, e.g. <em>lang_en</em>, and optional boolean operators, to restrict search results to documents (not) written in particular languages. See the <a target="_blank" href="https://developers.google.com/custom-search/docs/xml_results#lrsp"><em>lr</em> parameter</a>.'),
    );

    $form['google_cse']['advanced']['locale_lr'] = array(
      '#title' => $this->t('Set language restriction dynamically'),
      '#type' => 'checkbox',
      '#default_value' => $config->get('locale_lr'),
      '#description' => $this->t('The language restriction can be set dynamically if the locale module is enabled. Note the locale language code must match one of the <a target="_blank" href="https://developers.google.com/custom-search/docs/xml_results#languageCollections">supported language codes</a>.'),
    );

    $form['google_cse']['advanced']['oe'] = array(
      '#title' => $this->t('Output encoding'),
      '#type' => 'textfield',
      '#default_value' => $config->get('oe'),
      '#description' => $this->t('The default <em>utf-8</em> is recommended. See the <a target="_blank" href="https://developers.google.com/custom-search/docs/xml_results#oesp"><em>oe</em> parameter</a>.'),
    );

    $form['google_cse']['advanced']['safe'] = array(
      '#title' => $this->t('SafeSearch filter'),
      '#type' => 'select',
      '#options' => array(
        '' => '',
        'off' => $this->t('Off'),
        'medium' => $this->t('Medium'),
        'high' => $this->t('High'),
      ),
      '#default_value' => $config->get('safe'),
      '#description' => $this->t('SafeSearch filters search results for adult content. See the <a target="_blank" href="https://developers.google.com/custom-search/docs/xml_results#safesp"><em>safe</em> parameter</a>.'),
    );

    $form['google_cse']['advanced']['custom_css'] = array(
      '#title' => $this->t('Stylesheet Override'),
      '#type' => 'textfield',
      '#default_value' => $config->get('custom_css'),
      '#description' => $this->t('Set a custom stylesheet to override or add any styles not allowed in the CSE settings (such as "background-color: none;"). Include <span style="color:red; font-weight:bold;">!important</span> for overrides.<br/>Example: <em>//replacewithrealsite.com/sites/all/modules/google_cse/default.css</em>'),
    );

    $form['google_cse']['advanced']['custom_results_display'] = array(
      '#title' => $this->t('Layout of Search Engine'),
      '#type' => 'radios',
      '#default_value' => $config->get('custom_results_display'),
      '#options' => array(
        'overlay' => $this->t('Overlay'),
        'two-page' => $this->t('Two page'),
        'full-width' => $this->t('Full width'),
        'two-column' => $this->t('Two column'),
        'compact' => $this->t('Compact'),
        'results-only' => $this->t('Results only'),
        'google-hosted' => $this->t('Google hosted'),
      ),
      '#description' => $this->t('Set the search engine layout, as found in the Layout tab of <a target="_blank" href=":url">Custom Search settings</a>.', array(
        ':url' => 'https://www.google.com/cse/lookandfeel/layout?cx=' . $config->get('cx'),
      )),
    );

    $form['google_cse_adv'] = array(
      '#title' => $this->t('Google CSE Advanced'),
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    );

    $form['google_cse_adv']['use_adv'] = array(
      '#title' => $this->t('Use advanced, ad-free version, search engine (You will need a paid account with Google)'),
      '#type' => 'checkbox',
      '#default_value' => $config->get('use_adv'),
      '#description' => $this->t('If enabled, search results will be fetch using Adv engine.'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('google_cse.settings')
      ->set('cx', $form_state->getValue('cx'))
      ->set('results_tab', $form_state->getValue('results_tab'))
      ->set('results_width', $form_state->getValue('results_width'))
      ->set('cof_here', $form_state->getValue('cof_here'))
      ->set('cof_google', $form_state->getValue('cof_google'))
      ->set('results_prefix', $form_state->getValue('results_prefix'))
      ->set('results_suffix', $form_state->getValue('results_suffix'))
      ->set('results_searchbox_width', $form_state->getValue('results_searchbox_width'))
      ->set('results_display', $form_state->getValue('results_display'))
      ->set('results_display_images', $form_state->getValue('results_display_images'))
      ->set('sitesearch', $form_state->getValue('sitesearch'))
      ->set('sitesearch_form', $form_state->getValue('sitesearch_form'))
      ->set('sitesearch_option', $form_state->getValue('sitesearch_option'))
      ->set('sitesearch_default', $form_state->getValue('sitesearch_default'))
      ->set('domain', $form_state->getValue('domain'))
      ->set('limit_domain', $form_state->getValue('limit_domain'))
      ->set('cr', $form_state->getValue('cr'))
      ->set('gl', $form_state->getValue('gl'))
      ->set('hl', $form_state->getValue('hl'))
      ->set('locale_hl', $form_state->getValue('locale_hl'))
      ->set('ie', $form_state->getValue('ie'))
      ->set('lr', $form_state->getValue('lr'))
      ->set('locale_lr', $form_state->getValue('locale_lr'))
      ->set('oe', $form_state->getValue('oe'))
      ->set('safe', $form_state->getValue('safe'))
      ->set('custom_css', $form_state->getValue('custom_css'))
      ->set('custom_results_display', $form_state->getValue('custom_results_display'))
      ->set('use_adv', $form_state->getValue('use_adv'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
