<?php

/**
 * @file
 * Contains \Drupal\url_embed\Tests\UrlEmbedFilterTest.
 */

namespace Drupal\url_embed\Tests;

/**
 * Tests the url_embed filter.
 *
 * @group url_embed
 */
class UrlEmbedFilterTest extends UrlEmbedTestBase {

  /**
   * Tests the url_embed filter.
   *
   * Ensures that iframes are getting rendered when valid urls
   * are passed. Also tests situations when embed fails.
   */
  public function testFilter() {
    // Tests url embed using sample flickr url.
    $content = '<div data-embed-url="' . $this->sample_url . '">This placeholder should not be rendered.</div>';
    $settings = array();
    $settings['type'] = 'page';
    $settings['title'] = 'Test url embed with sample flickr url';
    $settings['body'] = array(array('value' => $content, 'format' => 'custom_format'));
    $node = $this->drupalCreateNode($settings);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw('<a data-flickr-embed="true" href="https://www.flickr.com/photos/bees/2341623661/" title="ZB8T0193 by ‮‭‬bees‬, on Flickr"><img src="https://farm4.staticflickr.com/3123/2341623661_7c99f48bbf_b.jpg" width="1024" height="683" alt="ZB8T0193" /></a>');
    $this->assertNoRaw('This placeholder should not be rendered.', 'Placeholder does not appears in the output when embed is successful.');

    // Test that tag of container element is replaced when it's 'drupal-url'.
    $content = '<drupal-url data-embed-url="' . $this->sample_url . '">this placeholder should not be rendered.</drupal-url>';
    $settings = array();
    $settings['type'] = 'page';
    $settings['title'] = 'Test url embed with sample flickr url';
    $settings['body'] = array(array('value' => $content, 'format' => 'custom_format'));
    $node = $this->drupalCreateNode($settings);
    $this->drupalget('node/' . $node->id());
    $this->assertRaw('<a data-flickr-embed="true" href="https://www.flickr.com/photos/bees/2341623661/" title="ZB8T0193 by ‮‭‬bees‬, on Flickr"><img src="https://farm4.staticflickr.com/3123/2341623661_7c99f48bbf_b.jpg" width="1024" height="683" alt="ZB8T0193" /></a>');
    $this->assertNoRaw('</drupal-url>');

    // Test that tag of container element is not replaced when it's not
    // 'drupal-url'.
    $content = '<not-drupal-url data-embed-url="' . $this->sample_url . '">this placeholder should not be rendered.</not-drupal-url>';
    $settings = array();
    $settings['type'] = 'page';
    $settings['title'] = 'Test url embed with sample flickr url';
    $settings['body'] = array(array('value' => $content, 'format' => 'custom_format'));
    $node = $this->drupalCreateNode($settings);
    $this->drupalget('node/' . $node->id());
    $this->assertRaw('<a data-flickr-embed="true" href="https://www.flickr.com/photos/bees/2341623661/" title="ZB8T0193 by ‮‭‬bees‬, on Flickr"><img src="https://farm4.staticflickr.com/3123/2341623661_7c99f48bbf_b.jpg" width="1024" height="683" alt="ZB8T0193" /></a>');
    $this->assertRaw('</not-drupal-url>');
  }

}
