<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Channel;
use App\Mapping\ChannelMapping;
use MongoDB\Database;
use MongoDB\BSON\ObjectId;

class ChannelRepository implements ChannelRepositoryInterface
{
    /** @var ChannelMapping */
    protected $channelMapping;

    /** @var Database */
    protected $mongoDb;

    public function __construct(ChannelMapping $channelMapping, Database $mongoDb)
    {
        $this->channelMapping = $channelMapping;
        $this->mongoDb = $mongoDb;
    }

    /**
     * Find channel in collection by Mongo ID
     *
     * @param string $id
     * @return Channel
     * @throws \Exception
     */
    public function get(string $id): Channel
    {
        $result = $this->mongoDb->channels->findOne(['_id' => new ObjectId($id)]);
        if ($result === null) {
            throw new \Exception('Channel not found');
        }

        $channel = $this->channelMapping->mapMongoResultToObject($result);
        $channel->setId((string)$result['_id']);

        return $channel;
    }

    /**
     * Add channel to collection
     *
     * @param Channel $channel
     * @throws \Exception
     */
    public function add(Channel $channel): void
    {
        $params = $this->channelMapping->mapObjectToMongoParams($channel);

        $result = $this->mongoDb->channels->insertOne($params);
        if ($result->getInsertedCount() === 0) {
            throw new \Exception('Can\'t add channel');
        }

        $channel->setId((string)$result->getInsertedId());
    }

    /**
     * Update or add channel to collection
     *
     * @param Channel $channel
     * @param bool $upsert
     * @throws \Exception
     */
    public function save(Channel $channel, bool $upsert = true): void
    {
        $params = $this->channelMapping->mapObjectToMongoParams($channel);

        $result = $this->mongoDb->channels->updateOne(
            ['_id' => new ObjectId($channel->getId())],
            ['$set' => $params],
            ['upsert' => $upsert]
        );

        if ($result->getModifiedCount() === 0) {
            throw new \Exception('Can\'t update channel');
        }

        if ($upsert && $result->getUpsertedCount() > 0) {
            $channel->setId((string)$result->getUpsertedId());
        }
    }

    /**
     * Remove channel from collection
     *
     * @param Channel $channel
     * @throws \Exception
     */
    public function remove(Channel $channel): void
    {
        $result = $this->mongoDb->channels->deleteOne(
            ['_id' => new ObjectId($channel->getId())]
        );

        if ($result->getDeletedCount() === 0) {
            throw new \Exception('Can\'t delete channel');
        }
    }

    /**
     * Find channel in collection by channel ID
     *
     * @param string $channelId
     * @return Channel
     * @throws \Exception
     */
    public function findByChannelId(string $channelId): Channel
    {
        $result = $this->mongoDb->channels->findOne(['channel_id' => $channelId]);
        if ($result === null) {
            throw new \Exception('Channel not found');
        }

        $channel = $this->channelMapping->mapMongoResultToObject($result);
        $channel->setId((string)$result['_id']);

        return $channel;
    }
}
