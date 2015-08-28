<?php

/**
 * @file
 * Contains \Drupal\url_embed\Plugin\Field\FieldFormatter\LinkEmbedFormatter.
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
 *   label = @Translation("Preview URL using Embed library"),
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
  public function viewElements(FieldItemListInterface $items) {
    $elements = array();
    foreach ($items as $delta => $item) {
      if ($url = $item->getUrl()->toString()) {
        try {
          if ($info = Embed::create($url)) {
            if ($code = $info->getCode()) {
              $elements[$delta] = array(
                '#markup' => SafeMarkup::set($code),
              );
            }
          }
        }
        catch (\Exception $exception) {
          watchdog_exception('url_embed', $exception);
        }
      }
    }
    return $elements;
  }

}
