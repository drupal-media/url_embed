<?php

/**
 * @file
 * Contains Drupal\url_embed\UrlEmbedService.
 */

namespace Drupal\url_embed;

use Embed\Embed;

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
   * @param string|\Embed\Request $request The url or a request with the url
   * @param array          $config  Options passed to the adapter
   *
   * @throws \Embed\Exceptions\InvalidUrlException If the urls is not valid
   * @throws \InvalidArgumentException      If any config argument is not valid
   *
   * @return \Embed\Adapters\AdapterInterface
   */
  public function getEmbed($request) {
    return Embed::create($request, $this->config);
  }

}
