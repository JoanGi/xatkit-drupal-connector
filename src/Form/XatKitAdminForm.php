<?php

namespace Drupal\xatkit\Form;

/**
 * @file
 * Contains \Drupal\xatkit\Form\XatKitAdminForm.
 */


use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Serialization\Json;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Drupal\xatkit\Controller\Api;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactory;

/**
 * MealPlanner form Class.
 */
class XatKitAdminForm extends ConfigFormBase {

  /**
   * Configuration state Drupal Site.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;
  /**
   * Serialization service.
   *
   * @var \Drupal\Component\Serialization\Json
   */
  protected $serialization;
  /**
   * Serialization service.
   *
   * @var \Drupal\Component\Serialization\Json
   */
  protected $client;

  /**
   * Construct method.
   */
  public function __construct(ConfigFactory $configFactory, Json $serialization, Client $client) {
    $this->configFactory = $configFactory;
    $this->serialization = $serialization;
    $this->client = $client;

  }

  /**
   * Create method.
   */
  public static function create(ContainerInterface $container) {
    // SET DEPENDENCY INJECTION.
    return new static(
      $container->get('config.factory'),
      $container->get('serialization.json'),
      $container->get('http_client'),
    );
  }

  /**
   * Gets the configuration names that will be editable.
   */
  protected function getEditableConfigNames() {
    return [
      'xatkit.settings',
    ];
  }

  /**
   * Main buil function.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->configFactory->getEditable('xatkit.settings');
    $form = parent::buildForm($form, $form_state);

    $form['server_conf'] = [
      '#type'        => 'fieldset',
      '#title'       => $this->t('Configure your bot connexion'),
      '#collapsible' => TRUE,
      '#collapsed'   => FALSE,
    ];
    $form['server_conf']['xatkitServer'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL of the Xatkit Server'),
      '#description' => $this->t('Do NOT change unless you have deployed your own server'),
      '#default_value' => $config->get('xatkit.serverUrl'),
      '#required' => TRUE,
    ];
    $form['server_conf']['xatkitStart'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('URL of the Xatkit Server'),
      '#description' => $this->t('Do NOT change unless you have deployed your own server'),
      '#default_value' => $config->get('xatkit.serverStart'),
    ];

    $form['bot_conf'] = [
      '#type'        => 'fieldset',
      '#title'       => $this->t('Configure the aspect of your bot'),
      '#collapsible' => TRUE,
      '#collapsed'   => FALSE,
    ];
    $form['bot_conf']['windowTitle'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title of the chat window'),
      '#description' => $this->t('Set the title of your chat window'),
      '#default_value' => $config->get('xatkit.windowTitle'),
      '#required' => FALSE,
    ];
    $form['bot_conf']['windowSubtitle'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subtitle of the chat window'),
      '#default_value' => $config->get('xatkit.windowSubtitle'),
      '#required' => FALSE,
    ];
    $form['bot_conf']['alternativeLogo'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Alternative Logo'),
      '#description' => $this->t('Ideal size 46x46'),
      '#default_value' => $config->get('xatkit.altLogo'),
      '#required' => FALSE,
    ];
    $form['bot_conf']['customColor'] = [
      '#type' => 'color',
      '#title' => $this->t('Window customn color'),
      '#description' => $this->t('Pick de color of bot window'),
      '#default_value' => $config->get('xatkit.color'),
      '#required' => FALSE,
    ];
    $form['bot_conf']['languageSelect'] = [
      '#type' => 'language_select',
      '#title' => $this->t('Language'),
      '#description' => $this->t('Language the bot should use to speak with your visitors'),
      '#default_value' => $config->get('xatkit.language'),
      '#required' => FALSE,
    ];

    return $form;
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'mealplanner_form';
  }

  /**
   * Example data to check if the provided settings are okay.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $validation = TRUE;
    if ($validation != FALSE) {
    }
    else {
      $form_state->setErrorByName('xatkitServer', $this->t('Server URL is not correct, please fit it'));
    }
  }

  /**
   * Example data to check if the provided settings are okay.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = $this->configFactory->getEditable('xatkit.settings');
    $config->set('xatkit.serverUrl', $this->getValue($form_state, 'xatkitServer'));
    $config->set('xatkit.serverStart', $this->getValue($form_state, 'xatkitStart'));
    $config->set('xatkit.windowTitle', $form_state->getValue('windowTitle'));
    $config->set('xatkit.windowSubtitle', $form_state->getValue('windowSubtitle'));
    $config->set('xatkit.altLogo', $form_state->getValue('alternativeLogo'));
    $config->set('xatkit.color', $form_state->getValue('customColor'));
    $config->set('xatkit.language', $form_state->getValue('languageSelect'));

    $config->save();
    return parent::submitForm($form, $form_state);
  }

  /**
   * Returns value of specific form element.
   */
  private function getValue($form_state, $prop_name) {
    return trim($form_state->getValue($prop_name));
  }

}