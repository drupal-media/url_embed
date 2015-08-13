<?php

/**
 * @file
 * Contains \Drupal\url_embed\Form\UrlEmbedDialog.
 */

namespace Drupal\url_embed\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\Ajax\EditorDialogSave;
use Drupal\editor\Entity\Editor;
use Drupal\url_embed\UrlButtonInterface;
use Drupal\filter\FilterFormatInterface;
use Drupal\Component\Serialization\Json;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form to embed URLs.
 */
class UrlEmbedDialog extends FormBase {

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs a UrlEmbedDialog object.
   *
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The Form Builder.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(FormBuilderInterface $form_builder, LoggerInterface $logger) {
    $this->formBuilder = $form_builder;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder'),
      $container->get('logger.factory')->get('url_embed')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'url_embed_dialog';
  }

  /**
   * {@inheritdoc}
   *
   * @param \Drupal\filter\Entity\FilterFormatInterface $filter_format
   *   The filter format to which this dialog corresponds.
   * @param \Drupal\url_embed\Entity\UrlButtonInterface $url_button
   *   The URL button to which this dialog corresponds.
   */
  public function buildForm(array $form, FormStateInterface $form_state, FilterFormatInterface $filter_format = NULL, UrlButtonInterface $url_button = NULL) {
    $values = $form_state->getValues();
    $input = $form_state->getUserInput();
    // Set URL button element in form state, so that it can be used later in
    // validateForm() function.
    $form_state->set('url_button', $url_button);
    // Initialize URL element with form attributes, if present.
    $url_element = empty($values['attributes']) ? array() : $values['attributes'];
    // The default values are set directly from \Drupal::request()->request,
    // provided by the editor plugin opening the dialog.
    if (!$form_state->get('url_element')) {
      $form_state->set('url_element', isset($input['editor_object']) ? $input['editor_object'] : array());
    }
    $url_element += $form_state->get('url_element');
    $url_element += array(
      'data-embed-url' => '',
    );

    $form['#tree'] = TRUE;
    $form['#attached']['library'][] = 'editor/drupal.editor.dialog';
    $form['#prefix'] = '<div id="url-embed-dialog-form">';
    $form['#suffix'] = '</div>';

    $form['attributes']['data-embed-url'] = array(
      '#type' => 'textfield',
      '#title' => 'URL',
      '#default_value' => $url_element['data-embed-url'],
      '#required' => TRUE,
    );
    $form['actions'] = array(
      '#type' => 'actions',
    );
    $form['actions']['save_modal'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      // No regular submit-handler. This form only works via JavaScript.
      '#submit' => array(),
      '#ajax' => array(
        'callback' => '::submitForm',
        'event' => 'click',
      ),
    );

    $form['attributes']['data-url-button'] = array(
      '#type' => 'value',
      '#value' => $url_button->id(),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $values = $form_state->getValues();
    // Display errors in form, if any.
    if ($form_state->hasAnyErrors()) {
      unset($form['#prefix'], $form['#suffix']);
      $form['status_messages'] = array(
        '#type' => 'status_messages',
        '#weight' => -10,
      );
      $response->addCommand(new HtmlCommand('#url-embed-dialog-form', $form));
    }
    else {
      $response->addCommand(new EditorDialogSave($values));
      $response->addCommand(new CloseModalDialogCommand());
    }

    return $response;
  }

  /**
   * Checks whether or not the URL button is enabled for given text format.
   *
   * Returns allowed if the editor toolbar contains the URL button and neutral
   * otherwise.
   *
   * @param \Drupal\filter\Entity\FilterFormatInterface $filter_format
   *   The filter format to which this dialog corresponds.
   * @param \Drupal\url_embed\Entity\UrlButtonInterface $url_button
   *   The URL button to which this dialog corresponds.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function buttonIsEnabled(FilterFormatInterface $filter_format, UrlButtonInterface $url_button) {
    $button_id = $url_button->id();
    $editor = Editor::load($filter_format->id());
    $settings = $editor->getSettings();
    foreach ($settings['toolbar']['rows'] as $row_number => $row) {
      $button_groups[$row_number] = array();
      foreach ($row as $group) {
        if (in_array($button_id, $group['items'])) {
          return AccessResult::allowed();
        }
      }
    }

    return AccessResult::neutral();
  }
}
