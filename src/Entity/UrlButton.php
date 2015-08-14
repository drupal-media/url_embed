<?php

/**
 * @file
 * Contains \Drupal\url_embed\Entity\UrlButton.
 */

namespace Drupal\url_embed\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\editor\EditorInterface;
use Drupal\url_embed\UrlButtonInterface;
use Drupal\file\FileUsage\FileUsageInterface;

/**
 * Defines the UrlButton entity.
 *
 * @ConfigEntityType(
 *   id = "url_embed_button",
 *   label = @Translation("Url embed button"),
 *   handlers = {
 *     "list_builder" = "Drupal\url_embed\UrlButtonListBuilder",
 *     "form" = {
 *       "add" = "Drupal\url_embed\Form\UrlButtonForm",
 *       "edit" = "Drupal\url_embed\Form\UrlButtonForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   config_prefix = "url_button",
 *   admin_permission = "administer url buttons",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   config_export = {
 *     "label",
 *     "id",
 *     "button_icon_uuid",
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/content/url-button/{url_embed_button}",
 *     "delete-form" = "/admin/config/content/url-button/{url_embed_button}/delete"
 *   }
 * )
 */
class UrlButton extends ConfigEntityBase implements UrlButtonInterface {

  /**
   * The UrlButton ID.
   *
   * @var string
   */
  public $id;

  /**
   * Label of UrlButton.
   *
   * @var string
   */
  public $label;

  /**
   * UUID of the button's icon fili.
   *
   * @var string
   */
  public $button_icon_uuid;

  /**
   * {@inheritdoc}
   */
  public function getButtonImage() {
    if ($this->button_icon_uuid && $image = $this->entityManager()->loadEntityByUuid('file', $this->button_icon_uuid)) {
      return $image->url();
    }
    else {
      return file_create_url(drupal_get_path('module', 'url_embed') . '/js/plugins/drupalurl/urlembed.png');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    parent::calculateDependencies();

    // Add the file icon entity as dependency if an UUID was specified.
    if ($this->button_icon_uuid && $file_icon = $this->entityManager()->loadEntityByUuid('file', $this->button_icon_uuid)) {
      $this->addDependency($file_icon->getConfigDependencyKey(), $file_icon->getConfigDependencyName());
    }

    return $this->dependencies;
  }

  /**
   * {@inheritdoc}
   */
  public function isEnabledInEditor(EditorInterface $editor) {
    if ($id = $this->id()) {
      $settings = $editor->getSettings();
      foreach ($settings['toolbar']['rows'] as $row_number => $row) {
        foreach ($row as $group) {
          if (in_array($id, $group['items'])) {
            return TRUE;
          }
        }
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    $new_button_icon_uuid = $this->get('button_icon_uuid');
    if (isset($this->original)) {
      $old_button_icon_uuid = $this->original->get('button_icon_uuid');

      if (!empty($old_button_icon_uuid) && $old_button_icon_uuid != $new_button_icon_uuid) {
        if ($file = $this->entityManager()->loadEntityByUuid('file', $old_button_icon_uuid)) {
          $this->fileUsage()->delete($file, 'entity_embed', $this->getEntityTypeId(), $this->id());
        }
      }
    }

    if ($new_button_icon_uuid) {
      if ($file = $this->entityManager()->loadEntityByUuid('file', $new_button_icon_uuid)) {
        $usage = $this->fileUsage()->listUsage($file);
        if (empty($usage['entity_embed'][$this->getEntityTypeId()][$this->id()])) {
          $this->fileUsage()->add($file, 'entity_embed', $this->getEntityTypeId(), $this->id());
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);

    foreach ($entities as $entity) {
      $button_icon_uuid = $entity->get('button_icon_uuid');
      if ($button_icon_uuid) {
        if ($file = \Drupal::entityManager()->loadEntityByUuid('file', $button_icon_uuid)) {
          \Drupal::service('file.usage')->delete($file, 'entity_embed', $entity->getEntityTypeId(), $entity->id());
        }
      }
    }
  }

  /**
   * Returns the file usage service.
   *
   * @return \Drupal\file\FileUsage\FileUsageInterface
   *   The file usage service.
   */
  protected function fileUsage() {
    if (!isset($this->fileUsage)) {
      $this->fileUsage = \Drupal::service('file.usage');
    }
    return $this->fileUsage;
  }

  /**
   * Sets the file usage service.
   *
   * @param \Drupal\file\FileUsage\FileUsageInterface $file_usage
   *   The file usage service.
   *
   * @return self
   */
  public function setFileUsage(FileUsageInterface $file_usage) {
    $this->fileUsage = $file_usage;
    return $this;
  }

}
