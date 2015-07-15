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
    // Tests url embed using sample youtube url.
    $content = '<div data-embed-url="' . $this->sample_url . '">This placeholder should not be rendered.</div>';
    $settings = array();
    $settings['type'] = 'page';
    $settings['title'] = 'Test url embed with sample youtube url';
    $settings['body'] = array(array('value' => $content, 'format' => 'custom_format'));
    $node = $this->drupalCreateNode($settings);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw('<iframe width="480" height="270" src="https://www.youtube.com/embed/7ipydm8guz4?feature=oembed" frameborder="0" allowfullscreen=""></iframe>');
    $this->assertNoRaw('Placeholder does not appears in the output when embed is successful.');

    // Test that tag of container element is replaced when it's 'drupal-url'.
    $content = '<drupal-url data-embed-url="' . $this->sample_url . '">this placeholder should not be rendered.</drupal-url>';
    $settings = array();
    $settings['type'] = 'page';
    $settings['title'] = 'test url embed with entity-id and view-mode';
    $settings['body'] = array(array('value' => $content, 'format' => 'custom_format'));
    $node = $this->drupalCreateNode($settings);
    $this->drupalget('node/' . $node->id());
    $this->assertRaw('<iframe width="480" height="270" src="https://www.youtube.com/embed/7ipydm8guz4?feature=oembed" frameborder="0" allowfullscreen=""></iframe>');
    $this->assertNoRaw('</drupal-url>');

    // Test that tag of container element is not replaced when it's not
    // 'drupal-url'.
    $content = '<not-drupal-url data-embed-url="' . $this->sample_url . '">this placeholder should not be rendered.</not-drupal-url>';
    $settings = array();
    $settings['type'] = 'page';
    $settings['title'] = 'test url embed with entity-id and view-mode';
    $settings['body'] = array(array('value' => $content, 'format' => 'custom_format'));
    $node = $this->drupalCreateNode($settings);
    $this->drupalget('node/' . $node->id());
    $this->assertRaw('<iframe width="480" height="270" src="https://www.youtube.com/embed/7ipydm8guz4?feature=oembed" frameborder="0" allowfullscreen=""></iframe>');
    $this->assertRaw('</not-drupal-url>');
  }

}
