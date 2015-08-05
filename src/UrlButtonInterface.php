<?php

/**
 * @file
 * Contains \Drupal\url_embed\UrlButtonInterface.
 */

namespace Drupal\url_embed;

// use Drupal\Core\Config\Entity\ConfigEntityInterface;
/**
 * Provides an interface defining a url button entity.
 */
interface UrlButtonInterface extends ConfigEntityInterface {

  /**
   * Returns the URL of the button's icon.
   *
   * @return string
   *   URL for the button'icon.
   */
  public function getButtonImage();

}
