<?php

namespace Drupal\invite\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityInterface;

/**
 * Class InviteConfig.
 *
 * @package Drupal\invite\Form
 */
class InviteConfig extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'invite.invite_config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'invite_config';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('invite.invite_config');

    $form['invite_expiration'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Invite Expiration'),
      '#description' => $this->t('Enter the number of days before the invitation expires.'),
      '#maxlength' => 6,
      '#size' => 6,
      '#default_value' => $config->get('invite_expiration'),
    );

    $form['accept_redirect'] = array(
      '#type' => 'textfield',
      '#required' => FALSE,
      '#title' => t('Accept Redirect'),
      '#description' => t('The route the user will be redirected to when registering. Defaults to "user.register"'),
      '#default_value' => $config->get('accept_redirect'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $route_name = $form_state->getValue('accept_redirect');

    $route_provider = \Drupal::service('router.route_provider');

    $route_exists = count($route_provider->getRoutesByNames([$route_name])) === 1;

    if (!$route_exists) {
      $form_state->setErrorByName('accept_redirect', t('Route "@route" does not exist.', ['@route' => $route_name]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('invite.invite_config')
        ->set('invite_expiration', $form_state->getValue('invite_expiration'))
        ->set('accept_redirect', $form_state->getValue('accept_redirect'))
        ->save();
  }

}
