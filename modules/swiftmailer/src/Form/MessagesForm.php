<?php

namespace Drupal\swiftmailer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Configuration form for SwiftMailer message settings.
 */
class MessagesForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'swiftmailer_messages_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'swiftmailer.message',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('swiftmailer.message');

    $form['#tree'] = TRUE;

    $form['description'] = [
      '#markup' => '<p>' . $this->t('This page allows you to configure settings which determines how e-mail messages are created.') . '</p>',
    ];

    $form['sender_options'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Sender options'),
    ];

    $form['sender_options']['sender_email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Sender e-mail'),
      '#default_value' => $config->get('sender_email'),
      '#description' => $this->t('The e-mail address that all e-mails will be from.'),
    ];

    $form['sender_options']['sender_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Sender name'),
      '#default_value' => $config->get('sender_name'),
      '#description' => $this->t('The name that all e-mails will be from. If left blank will use a default of: @name',
        ['@name' => \Drupal::config('system.site')->get('name')]),
    ];


    $form['format'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Message format'),
      '#description' => $this->t('You can set the default message format which should be applied to e-mail
        messages.'),
    ];

    $form['format']['type'] = [
      '#type' => 'radios',
      '#options' => [SWIFTMAILER_FORMAT_PLAIN => $this->t('Plain Text'), SWIFTMAILER_FORMAT_HTML => $this->t('HTML')],
      '#default_value' => $config->get('format'),
    ];

    $form['format']['respect'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Respect provided e-mail format.'),
      '#default_value' => $config->get('respect_format'),
      '#description' => $this->t('The header "Content-Type", if available, will be respected if you enable this setting.
        Settings such as e-mail format ("text/plain" or "text/html") and character set may be provided through this
        header. Unless your site somehow alters e-mails, enabling this setting will result in all e-mails to be sent
        as plain text as this is the content type Drupal by default will apply to all e-mails.'),
    ];

    $form['convert'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Plain Text Version'),
      '#description' => $this->t('An alternative plain text version can be generated based on the HTML version if no plain text version
        has been explicitly set. The plain text version will be used by e-mail clients not capable of displaying HTML content.'),
      '#states' => [
        'visible' => [
          'input[type=radio][name=format[type]]' => ['value' => SWIFTMAILER_FORMAT_HTML],
        ],
      ],
    ];

    $form['convert']['mode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Generate alternative plain text version.'),
      '#default_value' => $config->get('convert_mode'),
      '#description' => $this->t('Please refer to @link for more details about how the alternative plain text version will be generated.', ['@link' => Link::fromTextAndUrl('html2text', Url::fromUri('http://www.chuggnutt.com/html2text'))->toString()]),
    ];

    $form['character_set'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Character Set'),
      '#description' => '<p>' . $this->t('E-mails need to carry details about the character set which the
        receiving client should use to understand the content of the e-mail.
        The default character set is UTF-8.') . '</p>',
    ];

    $form['character_set']['type'] = [
      '#type' => 'select',
      '#options' => swiftmailer_get_character_set_options(),
      '#default_value' => $config->get('character_set'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $sender_email = $form_state->getValue(['sender_options', 'sender_email']);

    if (!empty($sender_email) && !\Drupal::service('email.validator')->isValid($sender_email)) {
      // Setting up 'name' manually because the element is an array.
      $form_state->setErrorByName('sender_options][sender_email', $this->t('The provided from e-mail address is not valid.'));
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('swiftmailer.message');
    $config->set('sender_email', $form_state->getValue(['sender_options', 'sender_email']));
    $config->set('sender_name', $form_state->getValue(['sender_options', 'sender_name']));
    $config->set('format', $form_state->getValue(['format', 'type']));
    $config->set('respect_format', $form_state->getValue(['format', 'respect']));
    $config->set('convert_mode', $form_state->getValue(['convert', 'mode']));
    $config->set('character_set', $form_state->getValue(['character_set', 'type']));

    $config->save();
    parent::submitForm($form, $form_state);
  }

}
