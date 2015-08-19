#!/bin/bash

set -e $DRUPAL_TI_DEBUG

# Ensure the right Drupal version is installed.
# Note: This function is re-entrant.
drupal_ti_ensure_drupal

# Download dependencies
mkdir -p "$DRUPAL_TI_DRUPAL_DIR/$DRUPAL_TI_MODULES_PATH"
cd "$DRUPAL_TI_DRUPAL_DIR/$DRUPAL_TI_MODULES_PATH"
git clone --depth 1 --branch 8.x-1.x https://github.com/drupal-media/embed.git
git clone --depth 1 --branch 8.x-1.x http://git.drupal.org/project/composer_manager.git

# Initialize composer manager
drush pm-enable composer_manager --yes
cd "$DRUPAL_TI_DRUPAL_DIR/$DRUPAL_TI_MODULES_PATH/composer_manager"
php scripts/init.php
