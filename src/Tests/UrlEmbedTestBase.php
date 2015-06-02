<?php

/**
 * @file
 * Contains \Drupal\url_embed\Tests\UrlEmbedTestBase.
 */

namespace Drupal\url_embed\Tests;

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
  public static $modules = array('url_embed');

  /**
   * The test user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  /**
   * A test url to be used for embedding.
   */
  protected $url;

  /**
   * A set up for all tests.
   */
  protected function setUp() {
    parent::setUp();

    // Create a page content type.
    $this->drupalCreateContentType(array('type' => 'page', 'name' => 'Basic page'));

    // Create Filtered HTML text format and enable url_embed filter.
    $format = entity_create('filter_format', array(
      'format' => 'custom_format',
      'name' => 'Custom format',
      'filters' => array(
        'url_embed' => array(
          'status' => 1,
        ),
      ),
    ));
    $format->save();

    // Create a user with required permissions.
    $this->webUser = $this->drupalCreateUser(array(
      'access content',
      'create page content',
      'use text format custom_format',
    ));
    $this->drupalLogin($this->webUser);

    // Create a sample url to be embedded.
    $this->url = 'https://youtu.be/7ipydm8guz4';
  }
}
