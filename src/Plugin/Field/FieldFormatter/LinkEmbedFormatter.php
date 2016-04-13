<?php

/**
 * @file
 * Contains \Drupal\url_embed\Plugin\Field\FieldFormatter\LinkEmbedFormatter.
 */

namespace Drupal\url_embed\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\url_embed\UrlEmbedHelperTrait;

/**
 * Plugin implementation of the 'url_embed' formatter.
 *
 * @FieldFormatter(
 *   id = "url_embed",
 *   label = @Translation("Embedded URL"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class LinkEmbedFormatter extends FormatterBase {
  use UrlEmbedHelperTrait;

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = array();

    foreach ($items as $delta => $item) {
      if ($url = $item->getUrl()->toString()) {
        try {
          if ($info = $this->urlEmbed()->getEmbed($url)) {
            $element[$delta] = array(
              '#type' => 'inline_template',
              '#template' => $info->getCode(),
            );
          }
        }
        catch (\Exception $exception) {
          watchdog_exception('url_embed', $exception);
        }
      }
    }

    return $element;
  }

}
