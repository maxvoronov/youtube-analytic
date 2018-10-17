<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Video;
use App\Mapping\VideoMapping;
use MongoDB\Database;
use MongoDB\BSON\ObjectId;

class VideoRepository implements VideoRepositoryInterface
{
    /** @var VideoMapping */
    protected $videoMapping;

    /** @var Database */
    protected $mongoDb;

    public function __construct(VideoMapping $videoMapping, Database $mongoDb)
    {
        $this->videoMapping = $videoMapping;
        $this->mongoDb = $mongoDb;
    }

    /**
     * Find video in collection by Mongo ID
     *
     * @param string $id
     * @return Video
     * @throws \Exception
     */
    public function get(string $id): Video
    {
        $result = $this->mongoDb->videos->findOne(['_id' => new ObjectId($id)]);
        if ($result === null) {
            throw new \Exception('Video not found');
        }

        $video = $this->videoMapping->mapMongoResultToObject($result);
        $video->setId((string)$result['_id']);

        return $video;
    }

    /**
     * Add video to collection
     *
     * @param Video $video
     * @throws \Exception
     */
    public function add(Video $video): void
    {
        $params = $this->videoMapping->mapObjectToMongoParams($video);

        $result = $this->mongoDb->videos->insertOne($params);
        if ($result->getInsertedCount() === 0) {
            throw new \Exception('Can\'t add video');
        }

        $video->setId((string)$result->getInsertedId());
    }

    /**
     * Update or add video to collection
     *
     * @param Video $video
     * @param bool $upsert
     * @throws \Exception
     */
    public function save(Video $video, bool $upsert = true): void
    {
        $params = $this->videoMapping->mapObjectToMongoParams($video);

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

    /**
     * Remove video from collection
     *
     * @param Video $video
     * @throws \Exception
     */
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
