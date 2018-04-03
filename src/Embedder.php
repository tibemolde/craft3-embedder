<?php
/**
 * Embedder plugin for Craft CMS 3.x
 *
 * Embed videos (YouTube, Vimeo, Facebook) or playlists (Spotify, Soundcloud) easily!
 *
 * @link      https://github.com/tibemolde
 * @copyright Copyright (c) 2018 TIBE Molde
 */

namespace tibemolde\embedder;

use Craft;
use craft\base\Plugin;
use craft\events\PluginEvent;
use craft\services\Plugins;
use craft\web\twig\variables\CraftVariable;
use tibemolde\embedder\services\VideoEmbedder as VideoEmbedderService;
use tibemolde\embedder\variables\EmbedderVariable;
use yii\base\Event;

/**
 * Class Embedder
 *
 * @author    TIBE Molde
 * @package   Embedder
 * @since     1.0.0
 *
 * @property  VideoEmbedderService    $videoEmbedder
 * @property  PlaylistEmbedderService $playlistEmbedder
 */
class Embedder extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Embedder
     */
    public static $plugin;
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function (Event $event) {
            /** @var CraftVariable $variable */
            $variable = $event->sender;
            $variable->set('embedder', EmbedderVariable::class);
        });

        Craft::info(Craft::t('embedder', '{name} plugin loaded', ['name' => $this->name]), __METHOD__);
    }

    // Protected Methods
    // =========================================================================

}
