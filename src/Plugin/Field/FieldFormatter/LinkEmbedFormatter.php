<?php

/**
 * @file
 * Contains \Drupal\url_embed\Plugin\Field\FieldFormatter\UrlFormatter.
 */

namespace Drupal\url_embed\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
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
      $configuration['third_party_settings']
    );
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
      $url = $item->getUrl() ?: Url::fromRoute('<none>');
      $url_string = $url->toString();
      $info = Embed::create($url_string);
      $elements[$delta] = array(
          '#markup' => SafeMarkup::set($info->code),
       );

    return $elements;
    }
  }


}
