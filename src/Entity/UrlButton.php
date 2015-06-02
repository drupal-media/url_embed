<?php

/**
 * @file
 * Contains \Drupal\url_embed\Entity\UrlButton.
 */

namespace Drupal\url_embed\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\url_embed\UrlButtonInterface;

/**
 * Defines the UrlButton entity.
 *
 * @ConfigEntityType(
 *   id = "url_button",
 *   label = @Translation("Url Button"),
 *   handlers = {
 *     "list_builder" = "Drupal\url_embed\UrlButtonListBuilder",
 *     "form" = {
 *       "add" = "Drupal\url_embed\Form\UrlButtonForm",
 *       "edit" = "Drupal\url_embed\Form\UrlButtonForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   config_prefix = "url_button",
 *   admin_permission = "administer url buttons",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   config_export = {
 *     "label",
 *     "id",
 *     "button_icon_uuid",
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/content/url-button/{url_button}",
 *     "delete-form" = "/admin/config/content/url-button/{url_button}/delete"
 *   }
 * )
 */
class UrlButton extends ConfigEntityBase implements UrlButtonInterface {

  /**
   * The UrlButton ID.
   *
   * @var string
   */
  public $id;

  /**
   * Label of UrlButton.
   *
   * @var string
   */
  public $label;

  /**
   * UUID of the button's icon fili.
   *
   * @var string
   */
  public $button_icon_uuid;

  /**
   * Array of allowed display plugins for the URL.
   *
   * An empty array signifies that all are allowed.
   *
   * @var array
   */
  public $display_plugins;

  /**
   * {@inheritdoc}
   */
  public function getSource(){
  }

  /**
   * {@inheritdoc}
   */
  public function getOembedProvider(){
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->label;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtonImage() {
    if ($this->button_icon_uuid && $image = $this->entityManager()->loadEntityByUuid('file', $this->button_icon_uuid)) {
      return $image->url();
    }
    else {
      return file_create_url(drupal_get_path('module', 'url_embed') . '/js/plugins/drupalentity/entity.png');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getAllowedDisplayPlugins() {
    $allowed_display_plugins = array();
    // Include only those plugin ids in result whose value is set.
    foreach ($this->display_plugins as $key => $value) {
      if ($value) {
        $allowed_display_plugins[$key] = $value;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    parent::calculateDependencies();

    // Add the file icon entity as dependency if an UUID was specified.
    if ($this->button_icon_uuid && $file_icon = $this->entityManager()->loadEntityByUuid('file', $this->button_icon_uuid)) {
      $this->addDependency($file_icon->getConfigDependencyKey(), $file_icon->getConfigDependencyName());
    }

    return $this->dependencies;
  }

}
