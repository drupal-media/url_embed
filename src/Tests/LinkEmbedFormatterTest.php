<?php

/**
 * @file
 * Contains \Drupal\url_embed\Tests\LinkEmbedFormatterTest.
 */

namespace Drupal\url_embed\Tests; 

use Drupal\link\Tests\LinkFieldTest;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Url;
use Drupal\link\LinkItemInterface;
use Drupal\simpletest\WebTestBase;
use Drupal\url_embed\Tests\UrlEmbedTestBase;
/**
 * Tests link field widgets and formatters.
 *
 * @group link
 */
class LinkEmbedFormatterTest extends LinkFieldTest{


  /**
   * Tests the 'link_embed' formatter.
   *
   * This test is mostly the same as testLinkFormatter(), but they cannot be
   * merged, since they involve different configuration and output.
   */
  function testLinkEmbedFormatter() {
    $field_name = Unicode::strtolower($this->randomMachineName());
    // Create a field with settings to validate.
    $this->fieldStorage = entity_create('field_storage_config', array(
      'field_name' => $field_name,
      'entity_type' => 'entity_test',
      'type' => 'link',
      'cardinality' => 2,
    ));
    $this->fieldStorage->save();
    entity_create('field_config', array(
      'field_storage' => $this->fieldStorage,
      'bundle' => 'entity_test',
      'settings' => array(
        'title' => DRUPAL_OPTIONAL,
        'link_type' => LinkItemInterface::LINK_GENERIC,
      ),
    ))->save();
    $display_options = array(
      'type' => 'link_separate',
      'label' => 'hidden',
    );
    entity_get_form_display('entity_test', 'entity_test', 'default')
      ->setComponent($field_name, array(
        'type' => 'link_default',
      ))
      ->save();
    entity_get_display('entity_test', 'entity_test', 'full')
      ->setComponent($field_name, $display_options)
      ->save();

    // Create an entity with one link field values:
    // - The first field item uses a URL only.
    // - The second field item uses a URL and link text.
    // For consistency in assertion code below, the URL is assigned to the title
    // variable for the first field.
    $this->drupalGet('entity_test/add');
    $url = UrlEmbedTestBase::getSampleUrl();
    $edit = array(
      "{$field_name}[0][uri]" => $url1,
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));
    preg_match('|entity_test/manage/(\d+)|', $this->url, $match);
    $id = $match[1];
    $this->assertText(t('entity_test @id has been created.', array('@id' => $id)));

    entity_get_display('entity_test', 'entity_test', 'full')
          ->setComponent($field_name, $display_options)
          ->save();

    $this->renderTestEntity($id);
    $this->assertRaw('<a data-flickr-embed="true" href="https://www.flickr.com/photos/bees/2341623661/" title="ZB8T0193 by <202e><202d><202c>bees<202c>, on Flickr"><img src="https://farm4.staticflickr.com/3123/2341623661_7c99f48bbf_b.jpg" width="1024" height="683" alt="ZB8T0193" /></a>');
  }
}
