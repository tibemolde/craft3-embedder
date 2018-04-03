<?php
/**
 * Embedder plugin for Craft CMS 3.x
 *
 * Embed videos (YouTube, Vimeo, Facebook) or playlists (Spotify, Soundcloud) easily!
 *
 * @link      https://github.com/tibemolde
 * @copyright Copyright (c) 2018 TIBE Molde
 */

namespace tibemolde\embedder\variables;

use tibemolde\embedder\Embedder;

use Craft;

/**
 * @author    TIBE Molde
 * @package   Embedder
 * @since     1.0.0
 */
class EmbedderVariable
{
    public function getVideoType($url)
    {
        return Embedder::$plugin->videoEmbedder->getType($url);
    }

    public function getVideoPoster($url)
    {
        return Embedder::$plugin->videoEmbedder->getPoster($url);
    }

    public function getVideoEmbedUrl($url)
    {
        return Embedder::$plugin->videoEmbedder->getEmbedUrl($url);
    }
}
