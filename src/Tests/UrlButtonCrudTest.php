<?php

/**
 * @file
 * Contains \Drupal\url_embed\Tests\UrlButtonCrudTest.
 */

namespace Drupal\url_embed\Tests;

use Drupal\Core\Config\Entity\ConfigEntityStorage;
use Drupal\url_embed\UrlButtonInterface;
use Drupal\simpletest\KernelTestBase;

/**
 * Tests creation, loading and deletion of url buttons.
 *
 * @group url_embed
 */
class UrlButtonCrudTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('url_embed');

  /**
   * The url button storage.
   *
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface.
   */
  protected $controller;

  protected function setUp() {
    parent::setUp();

    $this->controller = $this->container->get('entity.manager')->getStorage('url_embed_button');
  }

  /**
   * Tests CRUD operations for url buttons.
   */
  public function testUrlEmbedCrud() {
    $this->assertTrue($this->controller instanceof ConfigEntityStorage, 'The url_button storage is loaded.');

    // Run each test method in the same installation.
    $this->createTests();
    $this->loadTests();
    $this->deleteTests();
  }

  /**
   * Tests the creation of url_button.
   */
  protected function createTests() {
    $plugin = array(
      'id' => 'test_button',
      'label' => 'Testing url button instance',
      'button_icon_uuid' => '',
    );

    // Create an url_button with required values.
    $entity = $this->controller->create($plugin);
    $entity->save();

    $this->assertTrue($entity instanceof UrlButtonInterface, 'The newly created entity is an URL Button.');

    // Verify all the properties.
    $actual_properties = $this->container->get('config.factory')->get('url_embed.url_button.test_button')->get();

    $this->assertTrue(!empty($actual_properties['uuid']), 'The url button UUID is set.');
    unset($actual_properties['uuid']);

    $expected_properties = array(
      'langcode' => $this->container->get('language_manager')->getDefaultLanguage()->getId(),
      'status' => TRUE,
      'dependencies' => array(),
      'label' => 'Testing url button instance',
      'id' => 'test_button',
      'button_icon_uuid' => '',
    );

    $this->assertIdentical($actual_properties, $expected_properties, 'Actual config properties are structured as expected.');
  }

  /**
   * Tests the loading of url_button.
   */
  protected function loadTests() {
    $entity = $this->controller->load('test_button');

    $this->assertTrue($entity instanceof UrlButtonInterface, 'The loaded entity is an url button.');

    // Verify several properties of the url button.
    $this->assertEqual($entity->label(), 'Testing url button instance');
    $this->assertTrue($entity->uuid());
  }

  /**
   * Tests the deletion of url_button.
   */
  protected function deleteTests() {
    $entity = $this->controller->load('test_button');

    // Ensure that the storage isn't currently empty.
    $config_storage = $this->container->get('config.storage');
    $config = $config_storage->listAll('url_embed.url_button.');
    $this->assertFalse(empty($config), 'There are url buttons in config storage.');

    // Delete the url button.
    $entity->delete();

    // Ensure that the storage is now empty.
    $config = $config_storage->listAll('url_embed.url_button.');
    $this->assertTrue(empty($config), 'There are no url buttons in config storage.');
  }

}
