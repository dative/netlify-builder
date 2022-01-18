<?php
/**
 * NetlifyBuilder plugin for Craft CMS 3.x
 *
 * NetlifyBuilder helps you build & track differences between the Craft's entries and what's live.
 *
 * @link      https://hellodative.com
 * @copyright Copyright (c) 2022 Rodrigo Passos
 */

namespace dative\netlifybuilder\services;

use dative\netlifybuilder\NetlifyBuilder;

use Craft;
use craft\base\Component;
use craft\elements\db\EntryQuery;
use dative\netlifybuilder\records\BuildDeltaRecord;
use dative\netlifybuilder\records\BuildLogRecord;
use yii\base\Application;

/**
 * NetlifyBuilderService Service
 *
 * @author    Rodrigo Passos
 * @package   NetlifyBuilder
 * @since     1.0.0
 */
class NetlifyBuilderService extends Component
{

    private $_isBuildQueued = false;

    public function triggerBuild()
    {
        // $this->_sendBuildRequest();

        $logJson = [
            'elementsUpdated' => $this->getDeltaCount(),
        ];

        $this->_saveBuildLog(json_encode($logJson));

        BuildDeltaRecord::deleteAll();

        // Add to build log
    }

    public function getLastBuildDate()
    {
        $lastBuild = BuildLogRecord::find()
            ->orderBy('dateCreated DESC')
            ->one();

        if ($lastBuild) {
            return $lastBuild->dateCreated;
        }

        return null;
    }

    public function isBuildUpToDate(): bool
    {
        return !(bool) $this->getDeltaCount();
    }

    public function getDeltaCount(): int
    {
        // $lastBuild = $this->_getLastBuild();

        return BuildDeltaRecord::find()
            ->count();
    }

    /**
     * return changed entries
     *
     * @return EntryQuery
     **/
    public function getBuildDelta()
    {
        $entryDeltaRecords = BuildDeltaRecord::find()
            ->orderBy(['dateCreated' => SORT_DESC])
            ->all();

        return $entryDeltaRecords;
    }

    public function setCreated($element)
    {
        $this->_registerEntryDelta($element, 'created');
    }

    public function setUpdated($element)
    {
        $this->_registerEntryDelta($element, 'updated');
    }

    public function setMoved($element)
    {
        $this->_registerEntryDelta($element, 'moved');
    }

    public function setDeleted($element)
    {
        $this->_registerEntryDelta($element, 'deleted');
    }

    /**
     * reset delta records
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function resetDelta()
    {
        $entryDeltaRecords = BuildDeltaRecord::find()
            ->orderBy(['dateCreated' => SORT_DESC])
            ->all();
    }

    private function _saveBuildLog($logJson)
    {
        $buildLog = new BuildLogRecord();
        $buildLog->logJson = $logJson;
        $buildLog->save();
    }

    /**
     * register element delta
     *
     * @return int $element
     * @return string $actionType
     **/
    private function _registerEntryDelta($element, $actionType)
    {
        $entryDeltaRecord = new BuildDeltaRecord();
        $entryDeltaRecord->setAttribute('elementId', $element->id);
        $entryDeltaRecord->setAttribute('elementType',get_class($element));
        $entryDeltaRecord->setAttribute('actionType', $actionType);
        $entryDeltaRecord->save();
    }

    private function _sendBuildRequest()
    {
        $buildWebhookUrl = Craft::parseEnv(NetlifyBuilder::getInstance()->getSettings()->buildWebhookUrl);

        if (!empty($buildWebhookUrl) && $this->_isBuildQueued === false) {
            $this->_isBuildQueued = true;
            Craft::$app->on(Application::EVENT_AFTER_REQUEST, function() use ($buildWebhookUrl) {
                $guzzle = Craft::createGuzzleClient([
                    'headers' => [
                        'x-preview-update-source' => 'Craft CMS / NetlifyBuilder Plugin',
                        'Content-type' => 'application/json'
                    ]
                ]);
                $guzzle->request('POST', $buildWebhookUrl);
            }, null, false);
        }
    }
}
