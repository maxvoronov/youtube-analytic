<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Channel;
use App\Entity\ChannelStatistics;
use App\Entity\Thumbnail;
use MongoDB\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class ChannelRepository implements ChannelRepositoryInterface
{
    protected $mongoDb;

    public function __construct(Database $mongoDb)
    {
        $this->mongoDb = $mongoDb;
    }

    public function get(string $id): Channel
    {
        $result = $this->mongoDb->channels->findOne(['_id' => new ObjectId($id)]);
        if ($result === null) {
            throw new \Exception('Channel not found');
        }

        $channel = new Channel(
            $result['channel_id'] ?? '',
            $result['title'] ?? '',
            $result['description'] ?? '',
            $result['url_slug'] ?? '',
            $result['published_at']->toDateTime(),
            new ChannelStatistics(
                $result['view_count'] ?? 0,
                $result['video_count'] ?? 0,
                $result['subscriber_count'] ?? 0
            )
        );
        foreach ($result['thumbnails'] as $type => $thumbnail) {
            $channel->addThumbnail(new Thumbnail(
                $type,
                $thumbnail['url'] ?? '',
                $thumbnail['width'] ?? 0,
                $thumbnail['height'] ?? 0
            ));
        }
        $channel->setId((string)$result['_id']);

        return $channel;
    }

    public function add(Channel $channel): void
    {
        $params = [
            'channel_id' => $channel->getChannelId(),
            'title' => $channel->getTitle(),
            'description' => $channel->getDescription(),
            'url_slug' => $channel->getUrlSlug(),
            'published_at' => new UTCDateTime($channel->getPublishedAt()->getTimestamp() * 1000),
            'thumbnails' => [],
        ];

        foreach ($channel->getThumbnails() as $thumbnail) {
            /** @var Thumbnail $thumbnail */
            $params['thumbnails'][$thumbnail->getType()] = [
                'url' => $thumbnail->getImageUrl(),
                'width' => $thumbnail->getWidth(),
                'height' => $thumbnail->getHeight(),
            ];
        }

        $stats = $channel->getStatistics();
        $params['statistics'] = [
            'view_count' => $stats->getViewCount(),
            'video_count' => $stats->getVideoCount(),
            'subscriber_count' => $stats->getSubscriberCount(),
        ];

        $result = $this->mongoDb->channels->insertOne($params);
        if ($result->getInsertedCount() === 0) {
            throw new \Exception('Can\'t add channel');
        }

        $channel->setId((string)$result->getInsertedId());
    }

    public function save(Channel $channel, bool $upsert = true): void
    {
        $params = [
            'channel_id' => $channel->getChannelId(),
            'title' => $channel->getTitle(),
            'description' => $channel->getDescription(),
            'url_slug' => $channel->getUrlSlug(),
            'published_at' => new UTCDateTime($channel->getPublishedAt()->getTimestamp() * 1000),
            'thumbnails' => [],
        ];

        foreach ($channel->getThumbnails() as $thumbnail) {
            /** @var Thumbnail $thumbnail */
            $params['thumbnails'][$thumbnail->getType()] = [
                'url' => $thumbnail->getImageUrl(),
                'width' => $thumbnail->getWidth(),
                'height' => $thumbnail->getHeight(),
            ];
        }

        $stats = $channel->getStatistics();
        $params['statistics'] = [
            'view_count' => $stats->getViewCount(),
            'video_count' => $stats->getVideoCount(),
            'subscriber_count' => $stats->getSubscriberCount(),
        ];

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

    public function remove(Channel $channel): void
    {
        $result = $this->mongoDb->channels->deleteOne(
            ['_id' => new ObjectId($channel->getId())]
        );

        if ($result->getDeletedCount() === 0) {
            throw new \Exception('Can\'t delete channel');
        }
    }
}
