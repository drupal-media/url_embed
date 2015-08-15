<?php

/**
 * @file
 * Contains \Drupal\url_embed\Plugin\Field\FieldFormatter\UrlFormatter.
 */

namespace Drupal\url_embed\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\SafeMarkup;
use Drupal\Component\Utility\Unicode;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\link\LinkItemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Embed\Embed;
/**
 * Plugin implementation of the 'URL Embed' formatter.
 *
 * @FieldFormatter(
 *   id = "url_embed",
 *   label = @Translation("URL Embed"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class LinkEmbedFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The path validator service.
   *
   * @var \Drupal\Core\Path\PathValidatorInterface
   */
  protected $pathValidator;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('path.validator')
    );
  }

  /**
   * Constructs a new LinkFormatter.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Third party settings.
   * @param \Drupal\Core\Path\PathValidatorInterface $path_validatoUrlEmbed *   The path validator service.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, PathValidatorInterface $path_validator) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->pathValidator = $path_validator;
  }


  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    $summary[] = t('Link embeded URL using Embed library');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    $elements = array();

    foreach ($items as $delta => $item) {
      // By default use the full URL as the link text.
      $url = $this->buildUrl($item);
      $url_string = $url->toString();
      $info = Embed::create($url_string);
      $elements[$delta] = array(
          '#markup' => SafeMarkup::set($info->code),
       );

    return $elements;
    }
  }

  /**
   * Builds the \Drupal\Core\Url object for a link field item.
   *
   * @param \Drupal\link\LinkItemInterface $item
   *   The link field item being rendered.
   *
   * @return \Drupal\Core\Url
   *   An Url object.
   */
  protected function buildUrl(LinkItemInterface $item) {
    $url = $item->getUrl() ?: Url::fromRoute('<none>');

    $settings = $this->getSettings();
    $options = $item->options;

    // Add optional 'rel' attribute to link options.
    if (!empty($settings['rel'])) {
      $options['attributes']['rel'] = $settings['rel'];
    }
    // Add optional 'target' attribute to link options.
    if (!empty($settings['target'])) {
      $options['attributes']['target'] = $settings['target'];
    }
    $url->setOptions($options);

    return $url;
  }

}