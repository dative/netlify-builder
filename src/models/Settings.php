<?php
/**
 * NetlifyBuilder plugin for Craft CMS 3.x
 *
 * NetlifyBuilder helps you build & track differences between the Craft's entries and what's live.
 *
 * @link      https://hellodative.com
 * @copyright Copyright (c) 2022 Rodrigo Passos
 */

namespace dative\netlifybuilder\models;

use dative\netlifybuilder\NetlifyBuilder;

use Craft;
use craft\base\Model;

/**
 * NetlifyBuilder Settings Model
 *
 * @author    Rodrigo Passos
 * @package   NetlifyBuilder
 * @since     1.0.0
 */
class Settings extends Model
{
    /**
     * The full URL where the plugin should let
     * Netlify know to trigger a site build
     * @var string
     */
    public $buildWebhookUrl = '';
    public $buildStatusBadgeSrc = '';
}
