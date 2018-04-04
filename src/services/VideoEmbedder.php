<?php
/**
 * Embedder plugin for Craft CMS 3.x
 *
 * Embed videos (YouTube, Vimeo, Facebook) or playlists (Spotify, Soundcloud) easily!
 *
 * @link      https://github.com/tibemolde
 * @copyright Copyright (c) 2018 TIBE Molde
 */

namespace tibemolde\embedder\services;

use Craft;
use craft\base\Component;

/**
 * @author    TIBE Molde
 * @package   Embedder
 * @since     1.0.0
 */
class VideoEmbedder extends Component
{
    const FACEBOOK = 'facebook';
    const VIMEO    = 'vimeo';
    const YOUTUBE  = 'youtube';

    public function getEmbedUrl($url)
    {
        $type = $this->getType($url);

        return $this->_getEmbedUrl($type, $url);
    }

    public function getType($url)
    {
        if (!$url) {
            return null;
        }

        if (strpos($url, 'vimeo.com')) {
            return self::VIMEO;
        }
        if (strpos($url, 'facebook.com')) {
            return self::FACEBOOK;
        }

        return self::YOUTUBE;
    }

    private function _getEmbedUrl($type, $url)
    {
        switch ($type) {
            case self::FACEBOOK:
                $uri = $this->_getFacebookUri($url);

                return "https://www.facebook.com/plugins/video.php$uri";
            case self::VIMEO:
                $id = $this->_getVimeoId($url);

                return "https://player.vimeo.com/video/$id";
        }
        $uri          = $this->_getYouTubeUri($url);
        $queryStarted = mb_strpos($uri, '?') !== false;

        return "https://www.youtube.com/embed/$uri" . ($queryStarted ? '&' : '?') . 'rel=0';
    }

    private function _getFacebookUri($url)
    {
        $parsed = parse_url($url);
        if (empty($parsed['query'])) {
            return '?href=' . urlencode($url) . '&show_text=0&width=720&autoplay=false';
        }
        $params = [];
        parse_str($parsed['query'], $params);
        $append = '';
        if (isset($params['show_text'])) {
            $append .= '&show_text=' . $params['show_text'];
        } else {
            $append .= '&show_text=0';
        }
        if (isset($params['width'])) {
            $append .= '&width=' . $params['width'];
        } else {
            $append .= '&width=0';
        }
        if (isset($params['autoplay'])) {
            $append .= '&autoplay=' . $params['autoplay'];
        } else {
            $append .= '&autoplay=0';
        }

        return '?href=' . urlencode($url) . $append;
    }

    private function _getVimeoId($url)
    {
        $id = parse_url($url, PHP_URL_PATH);
        if (!$id) {
            return null;
        }

        return substr($id, 1);
    }

    private function _getYouTubeUri($url)
    {
        $url = parse_url($url);
        if ($url['host'] === 'youtu.be') {
            $id = substr($url['path'], 1);

            return isset($url['query']) ? "$id?{$url['query']}" : $id;
        }
        $params = [];
        parse_str($url['query'], $params);
        if (!isset($params['v'])) {
            return null;
        }
        $id = $params['v'];

        return isset($params['t']) ? "$id?t={$params['t']}" : $id;
    }

    public function getPoster($url)
    {
        $type = $this->getType($url);
        if (!$type) {
            return null;
        }
        $path = $type === self::VIMEO ? $this->_getVimeoImage($url) : $this->_getYouTubeImage($url);
        if (!$path) {
            return null;
        }

        return $path;
    }

    private function _getVimeoImage($url)
    {
        $id = $this->_getVimeoId(self::VIMEO, $url);
        if (!$id) {
            return null;
        }
        $settings          = Embedder::$plugin->getSettings();
        $storageFolderPath = $settings->storageFolder;
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $storageFolder = Craft::getAlias($storageFolderPath);
        $path          = sprintf('%s/%s', $storageFolder, self::YOUTUBE);
        $fileName      = sprintf('%s.%s', $id, 'jpg');
        $localPath     = sprintf('%s/%s', $path, $fileName);
        if (file_exists($localPath)) {
            $storageUrl = $settings->storageUrl;
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $url = Craft::getAlias($storageUrl);

            return sprintf('%s/%s/%s', $url, self::YOUTUBE, $fileName);
        }
        // how to detect errors from image call?
        // $thumb = @file_get_contents("https://i.vimeocdn.com/video/{$id}_1920.jpg");
        $thumb = false;
        if (!$thumb) {
            $video = $this->_getVimeoApiResponse($url);
            if (!$video) {
                return null;
            }
            $url = $video->thumbnail_url;
            if ($url) {
                $thumb = file_get_contents($url);
            }
        }
        if (!file_exists($path) && !mkdir($path) && !is_dir($path)) {
            return null;
        }
        if ($thumb !== false && file_put_contents($localPath, $thumb)) {
            $storageUrl = $settings->storageUrl;
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $url = Craft::getAlias($storageUrl);

            return sprintf('%s/%s/%s', $url, self::YOUTUBE, $fileName);
        }

        return null;
    }

    private function _getVimeoApiResponse($url)
    {
        if (!$url) {
            return null;
        }

        return @json_decode(file_get_contents("http://vimeo.com/api/oembed.json?url=$url"));
    }

    private function _getYouTubeId($url)
    {
        $url = parse_url($url);
        if ($url['host'] == 'youtu.be') {
            return substr($url['path'], 1);
        }
        $params = array();
        parse_str($url['query'], $params);
        if (!isset($params['v'])) {
            return null;
        }

        return $params['v'];
    }

    private function _getYouTubeImage($url)
    {
        $id = $this->_getYouTubeId($url);
        if (!$id) {
            return null;
        }
        $settings          = Embedder::$plugin->getSettings();
        $storageFolderPath = $settings->storageFolder;
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $storageFolder = Craft::getAlias($storageFolderPath);
        $path          = sprintf('%s/%s', $storageFolder, self::YOUTUBE);
        $fileName      = sprintf('%s.%s', $id, 'jpg');
        $localPath     = sprintf('%s/%s', $path, $fileName);
        if (file_exists($localPath)) {
            $storageUrl = $settings->storageUrl;
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $url = Craft::getAlias($storageUrl);

            return sprintf('%s/%s/%s', $url, self::YOUTUBE, $fileName);
        }
        $thumb = @file_get_contents("https://img.youtube.com/vi/{$id}/maxresdefault.jpg");
        if (!$thumb) {
            $thumb = @file_get_contents("https://img.youtube.com/vi/{$id}/0.jpg");
        }
        if (!file_exists($path) && !mkdir($path) && !is_dir($path)) {
            return null;
        }
        if ($thumb !== false && file_put_contents($localPath, $thumb)) {
            $storageUrl = $settings->storageUrl;
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $url = Craft::getAlias($storageUrl);

            return sprintf('%s/%s/%s', $url, self::YOUTUBE, $fileName);
        }

        return null;
    }
}
