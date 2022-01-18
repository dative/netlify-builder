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

use craft\base\Model;

/**
 * NetlifyBuilder ElementBuildDelta Model
 *
 * @author    Rodrigo Passos
 * @package   NetlifyBuilder
 * @since     1.0.0
 */
class ElementBuildLog extends Model
{
    /**
     * @var string|null JSON string
     */
    public $logJson;

    /**
     * @var \DateTime|null Date created
     */
    public $dateCreated;

    /**
     * @var \DateTime|null Date updated
     */
    public $dateUpdated;

    public function rules()
    {
        return [
            ['logJson', 'string'],
            [['logJson'], 'required'],
        ];
    }
}
