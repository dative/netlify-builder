<?php

namespace dative\netlifybuilder\migrations;

use Craft;
use craft\db\Migration;
use dative\netlifybuilder\records\BuildDeltaRecord;
use dative\netlifybuilder\records\BuildLogRecord;

/**
 * Installation Migration
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 1.0.0
 */
class Install extends Migration
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $buildDeltaTable = BuildDeltaRecord::tableName();

        if (!$this->db->tableExists($buildDeltaTable)) {
            $this->createTable($buildDeltaTable, [
                'id' => $this->primaryKey(),
                'elementId' => $this->integer()->notNull(),
                'elementType' => $this->string()->notNull(),
                'actionType' => $this->string()->notNull(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);
        }

        $buildLogTable = BuildLogRecord::tableName();

        if (!$this->db->tableExists($buildLogTable)) {
            $this->createTable($buildLogTable, [
                'id' => $this->primaryKey(),
                'logJson' => $this->string()->notNull(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);
        }

        Craft::$app->db->schema->refresh();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists(BuildDeltaRecord::tableName());
        $this->dropTableIfExists(BuildLogRecord::tableName());
        return true;
    }
}