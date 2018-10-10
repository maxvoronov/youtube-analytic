<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Thumbnail;
use App\Entity\Video;
use App\Entity\VideoStatistics;
use MongoDB\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class VideoRepository implements VideoRepositoryInterface
{
    protected $mongoDb;

    public function __construct(Database $mongoDb)
    {
        $this->mongoDb = $mongoDb;
    }

    public function get(string $id): Video
    {
        $result = $this->mongoDb->videos->findOne(['_id' => new ObjectId($id)]);
        if ($result === null) {
            throw new \Exception('Video not found');
        }

        $video = new Video(
            $result['channel_id'] ?? '',
            $result['video_id'] ?? '',
            $result['title'] ?? '',
            $result['description'] ?? '',
            $result['published_at']->toDateTime()
        );

        foreach ($result['thumbnails'] as $type => $thumbnail) {
            $video->addThumbnail(new Thumbnail(
                $type,
                $thumbnail['url'] ?? '',
                $thumbnail['width'] ?? 0,
                $thumbnail['height'] ?? 0
            ));
        }

        $video->setStatistics(new VideoStatistics(
            $result['like_count'] ?? 0,
            $result['dislike_count'] ?? 0,
            $result['comment_count'] ?? 0,
            $result['favorite_count'] ?? 0
        ));
        $video->setId((string)$result['_id']);

        return $video;
    }

    public function add(Video $video): void
    {
        $params = [
            'channel_id' => $video->getChannelId(),
            'video_id' => $video->getVideoId(),
            'title' => $video->getTitle(),
            'description' => $video->getDescription(),
            'published_at' => new UTCDateTime($video->getPublishedAt()->getTimestamp() * 1000),
            'thumbnails' => [],
        ];

        foreach ($video->getThumbnails() as $thumbnail) {
            /** @var Thumbnail $thumbnail */
            $params['thumbnails'][$thumbnail->getType()] = [
                'url' => $thumbnail->getImageUrl(),
                'width' => $thumbnail->getWidth(),
                'height' => $thumbnail->getHeight(),
            ];
        }

        $stats = $video->getStatistics();
        $params['statistics'] = [
            'like_count' => $stats->getLikeCount(),
            'dislike_count' => $stats->getDislikeCount(),
            'comment_count' => $stats->getCommentCount(),
            'favorite_count' => $stats->getFavoriteCount(),
        ];

        $result = $this->mongoDb->videos->insertOne($params);
        if ($result->getInsertedCount() === 0) {
            throw new \Exception('Can\'t add video');
        }

        $video->setId((string)$result->getInsertedId());
    }

    public function save(Video $video, bool $upsert = true): void
    {
        $params = [
            'channel_id' => $video->getChannelId(),
            'video_id' => $video->getVideoId(),
            'title' => $video->getTitle(),
            'description' => $video->getDescription(),
            'published_at' => new UTCDateTime($video->getPublishedAt()->getTimestamp() * 1000),
            'thumbnails' => [],
        ];

        foreach ($video->getThumbnails() as $thumbnail) {
            /** @var Thumbnail $thumbnail */
            $params['thumbnails'][$thumbnail->getType()] = [
                'url' => $thumbnail->getImageUrl(),
                'width' => $thumbnail->getWidth(),
                'height' => $thumbnail->getHeight(),
            ];
        }

        $stats = $video->getStatistics();
        $params['statistics'] = [
            'like_count' => $stats->getLikeCount(),
            'dislike_count' => $stats->getDislikeCount(),
            'comment_count' => $stats->getCommentCount(),
            'favorite_count' => $stats->getFavoriteCount(),
        ];

        $result = $this->mongoDb->videos->updateOne(
            ['_id' => new ObjectId($video->getId())],
            ['$set' => $params],
            ['upsert' => $upsert]
        );

        if ($result->getModifiedCount() === 0) {
            throw new \Exception('Can\'t update video');
        }

        if ($upsert && $result->getUpsertedCount() > 0) {
            $video->setId((string)$result->getUpsertedId());
        }
    }

    public function remove(Video $video): void
    {
        $result = $this->mongoDb->videos->deleteOne(
            ['_id' => new ObjectId($video->getId())]
        );

        if ($result->getDeletedCount() === 0) {
            throw new \Exception('Can\'t delete video');
        }
    }
}
