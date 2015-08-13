<?php

/**
 * @file
 * Contains \Drupal\url_embed\Ajax\UrlEmbedInsertCommand.
 */

namespace Drupal\url_embed\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * AJAX command for inserting an embedded URL in a CKEditor.
 *
 * @ingroup ajax
 */
class UrlEmbedInsertCommand implements CommandInterface {

  /**
   * The HTML content that will replace the matched element(s).
   *
   * @var string
   */
  protected $html;

  /**
   * Constructs an UrlEmbedCommand object.
   *
   * @param string $html
   *   String of HTML to be inserted.
   */
  public function __construct($html) {
    $this->html = $html;
  }

  /**
   * Implements Drupal\Core\Ajax\CommandInterface:render().
   */
  public function render() {
    return array(
      'command' => 'url_embed_insert',
      'html' => $this->html,
    );
  }

}
