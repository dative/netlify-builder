<?php
/**
 * NetlifyBuilder plugin for Craft CMS 3.x
 *
 * NetlifyBuilder helps you build & track differences between the Craft's entries and what's live.
 *
 * @link      https://hellodative.com
 * @copyright Copyright (c) 2022 Rodrigo Passos
 */

namespace dative\netlifybuilder\records;

use craft\db\ActiveRecord;

/**
 * BuildDeltaRecord
 *
 * @property int $id
 * @property int $entryId
 * @property string $type
 * @property \DateTime $dateCreated
 * @property \DateTime $dateUpdated
 */
class BuildDeltaRecord extends ActiveRecord
{
    /**
     * @inheritdoc
     *
     * @return string the table name
     */
    public static function tableName(): string
    {
        return '{{%netlifybuilder_build_delta}}';
    }
}