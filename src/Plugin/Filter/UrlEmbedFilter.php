<?php

/**
 * @file
 * Contains \Drupal\url_embed\Plugin\Filter\UrlEmbedFilter.
 */

namespace Drupal\url_embed\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\embed\DomHelperTrait;
use Drupal\filter\Annotation\Filtere;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Embed\Embed;

/**
 * Provides a filter to display embedded URLs based on data attributes.
 *
 * @Filter(
 *   id = "url_embed",
 *   title = @Translation("Display embedded URLs"),
 *   description = @Translation("Embeds URLs using data attribute: data-embed-url."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE
 * )
 */
class UrlEmbedFilter extends FilterBase {
  use DomHelperTrait;

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $result = new FilterProcessResult($text);
    if (strpos($text, 'data-embed-url') !== FALSE) {
      $dom = Html::load($text);
      $xpath = new \DOMXPath($dom);

      foreach ($xpath->query('//*[@data-embed-url]') as $node) {
        /** @var \DOMElement $node */
        $url = $node->getAttribute('data-embed-url');
        $url_output = '';
        try {
          if ($info = Embed::create($url)) {
            $url_output = $info->getCode();
            $node->setAttribute('data-url-provider', $info->getProviderName());
          }
        }
        catch (\Exception $e) {
          watchdog_exception('url_embed', $e);
        }

        // Ensure this element is using <div> now if it was <drupal-url>.
        if ($node->tagName == 'drupal-url') {
          $this->changeNodeName($node, 'div');
        }
        $this->setNodeContent($node, $url_output);
      }

      $result->setProcessedText(Html::serialize($dom));
    }
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    // @todo Add filter tips.
  }

}
