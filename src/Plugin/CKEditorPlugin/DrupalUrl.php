<?php

/**
 * @file
 * Contains \Drupal\url_embed\Plugin\CKEditorPlugin\DrupalUrl.
 */

namespace Drupal\url_embed\Plugin\CKEditorPlugin;

use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;
use Drupal\url_embed\Entity\UrlButton;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the "drupalurl" plugin.
 *
 * @CKEditorPlugin(
 *   id = "drupalurl",
 *   label = @Translation("URL"),
 *   module = "url_embed"
 * )
 */
class DrupalUrl extends CKEditorPluginBase implements ContainerFactoryPluginInterface {

  /**
   * All URL button configuration entities.
   *
   * An associative array that stores the description of all URL button
   * configuration entities keyed by the button id.
   *
   * @var array
   */
  protected $urlButtons;

  /**
   * Constructs a Drupal\url_embed\Plugin\CKEditorPlugin\DrupalUrl object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\Query\QueryInterface $url_button_query
   *   The entity query object for URL button.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, QueryInterface $url_button_query) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->urlButtons = $url_button_query->execute();
    debug($this->urlButtons);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity.query')->get('url_button')
      );
    }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    $buttons = array();

    foreach ($this->urlButtons as $url_button) {
      $button = UrlButton::load($url_button);
      $buttons[$button->id()] = array(
        'id' => SafeMarkup::checkPlain($button->id()),
        'name' => SafeMarkup::checkPlain($button->label()),
        'label' => SafeMarkup::checkPlain($button->label()),
        'image' => $button->getButtonImage(),
      );
    }

    return $buttons;
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'url_embed') . '/js/plugins/drupalurl/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return array(
      'core/drupal.ajax',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    $buttons = $this->getButtons();

    return array(
      'DrupalUrl_dialogTitleAdd' => t('Insert Url'),
      'DrupalUrl_dialogTitleEdit' => t('Edit Url'),
      'DrupalUrl_buttons' => $buttons,
    );
  }

}
