<?php

/**
 * @file
 * Contains Drupal\url_embed\UrlEmbedService.
 */

namespace Drupal\url_embed;

use Embed\Embed;

/**
 * A service class for handling URL embeds.
 */
class UrlEmbedService {

  public $config;

  public function __construct(array $config = []) {
    $this->config = $config;
  }

  public function getConfig() {
    return $this->config;
  }

  public function setConfig(array $config) {
    $this->config = $config;
  }

  /**
   * @param string|\Embed\Request $request
   *   The url or a request with the url
   * @param array $config
   *   (optional) Options passed to the adapter. If not provided the default
   *   options on the service will be used.
   *
   * @throws \Embed\Exceptions\InvalidUrlException
   *   If the urls is not valid
   * @throws \InvalidArgumentException
   *   If any config argument is not valid
   *
   * @return \Embed\Adapters\AdapterInterface
   */
  public function getEmbed($request, array $config = []) {
    return Embed::create($request, $config ?: $this->config);
  }

}
