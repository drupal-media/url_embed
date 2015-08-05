<?php

/**
 * @file
 * Contains \Drupal\url_embed\UrlButtonListBuilder.
 */

namespace Drupal\url_embed;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of UrlButton.
 */
class UrlButtonListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('URL Button');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $this->getLabel($entity);
    return $row + parent::buildRow($entity);
  }

}
