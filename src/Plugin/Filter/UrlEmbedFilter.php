<?php

/**
 * @file
 * Contains \Drupal\url_embed\Plugin\Filter\UrlEmbedFilter.
 */

namespace Drupal\url_embed\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\embed\DomHelperTrait;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\url_embed\UrlEmbedHelperTrait;
use Drupal\url_embed\UrlEmbedInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Renderer;

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
class UrlEmbedFilter extends FilterBase implements ContainerFactoryPluginInterface {
  use DomHelperTrait;
  use UrlEmbedHelperTrait;

  /**
   * The Renderer service.
   *
   * @var \Drupal\Core\Render\Renderer.
   */
  protected $renderer;


  /**
   * Constructs a UrlEmbedFilter object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\url_embed\UrlEmbedInterface $url_embed
   *   The URL embed service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, UrlEmbedInterface $url_embed, Renderer $renderer) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->setUrlEmbed($url_embed);
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('url_embed'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $result = new FilterProcessResult($text);
    if (strpos($text, 'data-embed-url') !== FALSE) {
      $dom = Html::load($text);
      $xpath = new \DOMXPath($dom);
      $config = $this->getConfiguration();

      foreach ($xpath->query('//drupal-url[@data-embed-url]') as $node) {
        /** @var \DOMElement $node */
        $url = $node->getAttribute('data-embed-url');
        $url_output = '';
        $ratio = '';
        try {
          if ($info = $this->urlEmbed()->getEmbed($url)) {
            $url_output = $info->getCode();
            $ratio = $info->aspectRatio;
          }
        }
        catch (\Exception $e) {
          watchdog_exception('url_embed', $e);
        }

        // Wrap the embed code in a container to make it responsive
        if ($ratio && !empty($config['settings']['enable_responsive'])) {
          $responsive_embed = [
            '#theme' => 'responsive_embed',
            '#ratio' => $ratio,
            '#url_output' => $url_output,
            '#attached' => array(
              'library' =>  array(
                'url_embed/url_embed.responsive_styles'
              ),
            ),
          ];

          $url_output = $this->renderer->render($responsive_embed);
        }

        $this->replaceNodeContent($node, $url_output);
      }

      $result->setProcessedText(Html::serialize($dom));
    }
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    if ($long) {
      return $this->t('
        <p>You can embed URLs. Additional properties can be added to the URL tag like data-caption and data-align if supported. Examples:</p>
        <ul>
          <li><code>&lt;drupal-url data-embed-url="https://www.youtube.com/watch?v=xxXXxxXxxxX" data-url-provider="YouTube" /&gt;</code></li>
        </ul>');
    }
    else {
      return $this->t('You can embed URLs.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    $form['enable_responsive'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Responsive Wrappers'),
      '#description' => $this->t('Automatically wrap embedded iframes with a container which will allow the embedded media to scale appropriately to the size of the page.'),
      '#default_value' => !empty($config['settings']['enable_responsive']) ? $config['settings']['enable_responsive'] : FALSE,
    );

    return $form;
  }

}
