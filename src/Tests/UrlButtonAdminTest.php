<?php

/**
 * @file
 * Contains \Drupal\url_embed\Tests\UrlButtonAdminTest.
 */

namespace Drupal\url_embed\Tests;

Use \Drupal\Component\Utility\Unicode;

/**
 * Tests the administrative UI.
 *
 * @group url_embed
 */
class UrlButtonAdminTest extends UrlEmbedTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('editor', 'ckeditor', 'url_embed', 'node');

  /**
   * A user with permission to administer url buttons.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  protected function setUp() {
    parent::setUp();

    // Create a user with admin permissions.
    $this->adminUser = $this->drupalCreateUser(array(
      'access content',
      'create page content',
      'use text format custom_format',
      'administer url embed buttons',
    ));
  }

  /**
   * Tests the url_button administration functionality.
   */
  public function testUrlButtonAdmin() {
    $this->drupalGet('admin/config/content/url-button');
    $this->assertResponse(403, 'User without admin permissions are not able to visit the configuration page.');

    // Swtich to admin user.
    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/config/content/url-button');
    $this->assertResponse(200, 'User with admin permissions is able to visit the configuration page.');

    // Add url button.
    $this->clickLink('Add URL Button');
    $button_id = Unicode::strtolower($this->randomMachineName());
    $name = $this->randomMachineName();
    $edit = array(
      'id' => $button_id,
      'label' => $name,
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));
    // Ensure that the newly created button exists.
    $this->drupalGet('admin/config/content/url-button/' . $button_id);
    $this->assertResponse(200, 'Added url button exists.');
    // Ensure that the newly created button is listed.
    $this->drupalGet('admin/config/content/url-button');
    $this->assertText($name, 'Test url_button appears on the list page');

    // Edit url button.
    $this->drupalGet('admin/config/content/url-button/' . $button_id);
    $new_name = $this->randomMachineName();
    $edit = array(
      'label' => $new_name,
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));
    // Ensure that name and label has been changed.
    $this->drupalGet('admin/config/content/url-button');
    $this->assertText($new_name, 'New label appears on the list page');
    $this->assertNoText($name, 'Old label does not appears on the list page');

    // Delete url button.
    $this->drupalGet('admin/config/content/url-button/' . $button_id . '/delete');
    $this->drupalPostForm(NULL, array(), t('Delete'));
    // Ensure that the deleted url button no longer exists.
    $this->drupalGet('admin/config/content/url-button/' . $button_id);
    $this->assertResponse(404, 'Deleted url button no longer exists.');
    // Ensure that the deleted button is no longer listed.
    $this->drupalGet('admin/config/content/url-button');
    $this->assertNoText($name, 'Test url_button does not appears on the list page');
  }
}
