<?php

/**
 * @file
 * Contains \Drupal\url_embed\UrlButtonInterface.
 */

namespace Drupal\url_embed;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\editor\EditorInterface;

/**
 * Provides an interface defining a url button entity.
 */
interface UrlButtonInterface extends ConfigEntityInterface {

  /**
   * Returns the URL of the button's icon.
   *
   * @return string
   *   URL for the button's icon.
   */
  public function getButtonImage();

  /**
   * Checks if the entity embed button is enabled in an editor configuration.
   *
   * @param \Drupal\editor\EditorInterface $editor
   *   The editor object to check.
   *
   * @return bool
   *   TRUE if this entity embed button is enabled in $editor. FALSE otherwise.
   */
  public function isEnabledInEditor(EditorInterface $editor);

}
