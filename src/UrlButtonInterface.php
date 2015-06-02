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
interface UrlButtonInterface{
  // extends ConfigEntityInterface {
  /**
   * Returns the label for the button to be shown in CKEditor toolbar.
   *
   * @return string
   *   Label for the button.
   */
  public function getLabel();

  /**
   * Returns the URL of the button's icon.
   *
   * @return string
   *   URL for the button'icon.
   */
  public function getButtonImage();

}
