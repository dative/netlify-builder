<?php
/**
 * NetlifyBuilder plugin for Craft CMS 3.x
 *
 * NetlifyBuilder helps you build & track differences between the Craft's entries and what's live.
 *
 * @link      https://hellodative.com
 * @copyright Copyright (c) 2022 Rodrigo Passos
 */

namespace dative\netlifybuilder\widgets;

use dative\netlifybuilder\NetlifyBuilder;
use dative\netlifybuilder\assetbundles\netlifybuilderwidgetwidget\NetlifyBuilderWidgetWidgetAsset;

use Craft;
use craft\base\Widget;

/**
 * NetlifyBuilder Widget
 *
 * Dashboard widgets allow you to display information in the Admin CP Dashboard.
 * Adding new types of widgets to the dashboard couldn’t be easier in Craft
 *
 * https://craftcms.com/docs/plugins/widgets
 *
 * @author    Rodrigo Passos
 * @package   NetlifyBuilder
 * @since     1.0.0
 */
class NetlifyBuilderWidget extends Widget
{
    public static function maxColspan(): int
    {
        return 1;
    }

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('netlify-builder', 'Netlify Builder Widget');
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return "";
    }

    /**
     * Returns the path to the widget’s SVG icon.
     *
     * @return string|null The path to the widget’s SVG icon
     */
    public static function iconPath()
    {
        return Craft::getAlias("@dative/netlifybuilder/assetbundles/netlifybuilderwidgetwidget/dist/img/NetlifyBuilderWidget-icon.svg");
    }

    /**
     * Returns the widget's body HTML.
     *
     * @return string|false The widget’s body HTML, or `false` if the widget
     *                      should not be visible. (If you don’t want the widget
     *                      to be selectable in the first place, use {@link isSelectable()}.)
     */
    public function getBodyHtml()
    {
        $badgeSrc = Craft::parseEnv(NetlifyBuilder::getInstance()->getSettings()->buildStatusBadgeSrc);

        Craft::$app->getView()->registerAssetBundle(NetlifyBuilderWidgetWidgetAsset::class);

        $deltaCount = NetlifyBuilder::getInstance()->netlifyBuilder->getDeltaCount();

        $view = Craft::$app->getView();

        $lastBuild = NetlifyBuilder::getInstance()->netlifyBuilder->getLastBuildDate();

        return $view->renderTemplate(
            'netlify-builder/_components/widgets/NetlifyBuilderWidget_body',
            [
                'widget' => $this,
                'badgeSrc' => $badgeSrc,
                'deltaCount' => $deltaCount,
                'lastBuild' => $lastBuild,
            ]
        );
    }

    protected static function allowMultipleInstances(): bool
    {
        return false;
    }
}
