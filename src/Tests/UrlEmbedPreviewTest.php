<?php

/**
 * @file
 * Contains \Drupal\url_embed\Tests\UrlEmbedPreviewTest.
 */

namespace Drupal\url_embed\Tests;

/**
 * Tests the url_embed preview controller and route.
 *
 * @group url_embed
 */
class UrlEmbedPreviewTest extends UrlEmbedTestBase {

  /**
   * URL of the preview route.
   *
   * @var string
   */
  protected $previewUrl;

  protected function setUp() {
    parent::setUp();

    // Define URL that will be used to access the preview route.
    $this->previewUrl = 'url-embed/preview/custom_format';
  }

  /**
   * Tests the route used for generating preview of embedding entities.
   */
  public function testPreviewRoute() {
    // Test preview route with a valid request.
    $content = '<div data-embed-url="' . $this->sample_url . '">This placeholder should not be rendered.</div>';
    $this->drupalGet($this->previewUrl, array('query' => array('value' => $content)));
    $this->assertResponse(200, 'The preview route exists.');
    $this->assertNoRaw('This placeholder should not be rendered.', 'Placeholder does not appears in the output when embed is successful.');
    $respose = json_decode($this->getRawContent());
    $this->assertEqual($respose[0]->command, 'url_embed_insert', 'Correct ajax command is returned by the preview route.');
    $this->assertEqual($respose[0]->html, '<div data-embed-url="http://www.flickr.com/photos/bees/2341623661/"><a data-flickr-embed="true" href="https://www.flickr.com/photos/bees/2341623661/" title="ZB8T0193 by ‮‭‬bees‬, on Flickr"><img src="https://farm4.staticflickr.com/3123/2341623661_7c99f48bbf_b.jpg" width="1024" height="683" alt="ZB8T0193" /></a></div>', 'Correct html is returned by the preview route.');

    // Test preview route with an invalid request.
    $content = 'Testing preview route without valid values';
    $this->drupalGet($this->previewUrl, array('query' => array('value' => $content)));
    $this->assertResponse(200, 'The preview route exists.');
    $this->assertText($content, 'Placeholder appears in the output when embed is unsuccessful.');

    // Test preview route with an empty request.
    $this->drupalGet($this->previewUrl);
    $this->assertResponse(404, "The preview returns 'Page not found' when GET parameters are not provided.");
  }

}