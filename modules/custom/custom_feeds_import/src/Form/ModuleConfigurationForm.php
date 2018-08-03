<?php

namespace Drupal\custom_feeds_import\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures forms module settings.
 */
class ModuleConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_feeds_import_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'custom_feeds_import.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('custom_feeds_import.settings');
    $form['custom_feeds_import_uid'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Feeds Uid'),
      '#default_value' => $config->get('custom_feeds_import_uid'),
    ];
    $form['custom_feeds_import_keyApi'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Feeds API Key'),
      '#default_value' => $config->get('custom_feeds_import_keyApi'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('custom_feeds_import.settings')
      ->set('custom_feeds_import_uid', $values['custom_feeds_import_uid'])
      ->set('custom_feeds_import_keyApi', $values['custom_feeds_import_keyApi'])
      ->save();
    parent::submitForm($form, $form_state);
  }

}