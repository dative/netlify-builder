<?php
/**
 * NetlifyBuilder plugin for Craft CMS 3.x
 *
 * NetlifyBuilder helps you build & track differences between the Craft's entries and what's live.
 *
 * @link      https://hellodative.com
 * @copyright Copyright (c) 2022 Rodrigo Passos
 */

namespace dative\netlifybuilder;

use dative\netlifybuilder\services\NetlifyBuilderService;
use dative\netlifybuilder\models\Settings;
use dative\netlifybuilder\widgets\NetlifyBuilderWidget;

use Craft;
use craft\base\Element;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\console\Application as ConsoleApplication;
use craft\events\ElementStructureEvent;
use craft\events\ModelEvent;
use craft\services\Dashboard;
use craft\events\RegisterComponentTypesEvent;
use craft\helpers\ElementHelper;
use craft\helpers\UrlHelper;
use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Rodrigo Passos
 * @package   NetlifyBuilder
 * @since     1.0.0
 *
 * @property  NetlifyBuilderService $netlifyBuilder
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class NetlifyBuilder extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * NetlifyBuilder::$plugin
     *
     * @var NetlifyBuilder
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * NetlifyBuilder::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'netlifyBuilder' => NetlifyBuilderService::class,
        ]);

        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'dative\netlifybuilder\console\controllers';
        }

        $this->_registerWidget();
        $this->_registerElementListeners();

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    $request = Craft::$app->getRequest();
                    if ($request->isCpRequest) {
                        Craft::$app->getResponse()->redirect(UrlHelper::cpUrl(
                            'settings/plugins/netlify-builder'
                        ))->send();
                    }
                }
            }
        );

        /**
         * Logging in Craft involves using one of the following methods:
         *
         * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
         * Craft::info(): record a message that conveys some useful information.
         * Craft::warning(): record a warning message that indicates something unexpected has happened.
         * Craft::error(): record a fatal error that should be investigated as soon as possible.
         *
         * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
         *
         * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
         * the category to the method (prefixed with the fully qualified class name) where the constant appears.
         *
         * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
         * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
         *
         * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
         */
        Craft::info(
            Craft::t(
                'netlify-builder',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'netlify-builder/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }

    private function _registerWidget()
    {
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = NetlifyBuilderWidget::class;
            }
        );
    }

    private function _registerElementListeners()
    {
        // Saved
        Event::on(
            Element::class,
            Element::EVENT_AFTER_PROPAGATE,
            function (ModelEvent $event) {

            if (
                !ElementHelper::isDraft($event->sender) &&
                !ElementHelper::isRevision($event->sender) &&
                !$event->sender->propagating &&
                !$event->sender->resaving
            ) {

                if ($event->sender->firstSave) {
                    $this->netlifyBuilder->setCreated($event->sender);
                    return;
                }

                $this->netlifyBuilder->setUpdated($event->sender);
            }
        });

        // Deleted
        Event::on(
            Element::class,
            Element::EVENT_AFTER_DELETE,
            function(Event $event) {

            if (
                !(
                    !ElementHelper::isDraft($event->sender) &&
                    !ElementHelper::isRevision($event->sender) &&
                    $event->sender->duplicateOf &&
                    $event->sender->getIsCanonical() &&
                    !$event->sender->updatingFromDerivative
                )
                && (
                    $event->sender->enabled &&
                    $event->sender->getEnabledForSite()
                )
                && !$event->sender->firstSave
            ) {
                $this->netlifyBuilder->setDeleted($event->sender);
            }
        });

        // Moved
        Event::on(
            Element::class,
            Element::EVENT_AFTER_MOVE_IN_STRUCTURE,
            function (ElementStructureEvent $event) {
                if (
                    !ElementHelper::isDraft($event->sender) &&
                    !($event->sender->duplicateOf && $event->sender->getIsCanonical() && !$event->sender->updatingFromDerivative) &&
                    !$event->sender->firstSave &&
                    !$event->isNew &&
                    !$event->sender->propagating &&
                    !$event->sender->resaving
                ) {
                    $this->netlifyBuilder->setMoved($event->sender);
                }
            }
        );

    }
}
