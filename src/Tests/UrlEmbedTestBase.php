<?php

/**
 * @file
 * Contains \Drupal\url_embed\Tests\UrlEmbedTestBase.
 */

namespace Drupal\url_embed\Tests;

use Drupal\editor\Entity\Editor;
use Drupal\filter\Entity\FilterFormat;
use Drupal\simpletest\WebTestBase;

/**
 * Base class for all url_embed tests.
 */
abstract class UrlEmbedTestBase extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('url_embed', 'node', 'ckeditor');

  /**
   * The test user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  /**
   * A test url to be used for embedding.
   */
  protected $sample_url;

  /**
   * A set up for all tests.
   */
  protected function setUp() {
    parent::setUp();

    // Create a page content type.
    $this->drupalCreateContentType(array('type' => 'page', 'name' => 'Basic page'));


    // Create a text format and enable the url_embed filter.
    $format = FilterFormat::create([
      'format' => 'custom_format',
      'name' => 'Custom format',
      'filters' => [
        'url_embed' => [
          'status' => 1,
        ],
      ],
    ]);
    $format->save();

    $editor_group = [
      'name' => 'URL Embed',
      'items' => [
        'url',
      ],
    ];
    $editor = Editor::create([
      'format' => 'custom_format',
      'editor' => 'ckeditor',
      'settings' => [
        'toolbar' => [
          'rows' => [[$editor_group]],
        ],
      ],
    ]);
    $editor->save();

    // Create a user with required permissions.
    $this->webUser = $this->drupalCreateUser(array(
      'access content',
      'create page content',
      'use text format custom_format',
    ));
    $this->drupalLogin($this->webUser);

    // Create a sample url to be embedded.
    $this->sample_url = 'http://www.flickr.com/photos/bees/2341623661/';
  }
}
