<?php
/**
 * NetlifyBuilder plugin for Craft CMS 3.x
 *
 * NetlifyBuilder helps you build & track differences between the Craft's entries and what's live.
 *
 * @link      https://hellodative.com
 * @copyright Copyright (c) 2022 Rodrigo Passos
 */

namespace dative\netlifybuilder\controllers;

use dative\netlifybuilder\NetlifyBuilder;

use Craft;
use craft\web\Controller;

/**
 * Default Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Rodrigo Passos
 * @package   NetlifyBuilder
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/netlify-builder/default
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'Welcome to the DefaultController actionIndex() method';

        return $result;
    }

    /**
     * Handle a request going to our plugin's actionTriggerBuild URL,
     * e.g.: actions/netlify-builder/default/trigger-build
     *
     * @return mixed
     */
    public function actionTriggerBuild()
    {
        if ($this->_checkSettings()) {
            NetlifyBuilder::$plugin->netlifyBuilder->triggerBuild();
            Craft::$app->getSession()->setNotice(Craft::t('netlify-builder', 'A new netlify build has started.'));
        } else {
            Craft::$app->getSession()->setError(Craft::t('netlify-builder', 'Netlify webhook endpoint not set.'));
        }
    }

    private function _checkSettings(): bool
    {
        return !empty(Craft::parseEnv(NetlifyBuilder::getInstance()->getSettings()->buildWebhookUrl));
    }
}
