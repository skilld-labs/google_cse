<?php

namespace Drupal\google_cse\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Google CSE form.
 */
class GoogleCseForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'google_cse';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['keys'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
    );
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Search'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = \Drupal::configFactory()->get('GoogleCSE.settings');
    $path = '/search/' . $config->get('GoogleCSE.wildcard') . '/' . $form_state->getValues()['keys'];
    $form_state->setRedirectUrl($path);
  }

}
