<?php
/**
 * Embedder plugin for Craft CMS 3.x
 *
 * Embed videos (YouTube, Vimeo, Facebook) easily!
 *
 * @link      https://github.com/tibemolde
 * @copyright Copyright (c) 2018 TIBE Molde
 */

namespace tibemolde\embedder;

use Craft;
use craft\base\Plugin;
use craft\web\twig\variables\CraftVariable;
use tibemolde\embedder\models\Settings;
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
 * @property  VideoEmbedderService $videoEmbedder
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class Embedder extends Plugin
{
    public static $plugin;
    public        $schemaVersion = '1.0.6';

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
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate('embedder/settings', [
            'settings' => $this->getSettings(),
        ]);
    }
}
