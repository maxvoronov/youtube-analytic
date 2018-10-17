<?php declare(strict_types=1);

namespace App\Mapping;

use App\Entity\Video;
use App\ValueObject\Thumbnail;
use App\ValueObject\VideoStatistics;
use MongoDB\BSON\UTCDateTime;

class VideoMapping
{
    public function mapMongoResultToObject($result): Video
    {
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

        return $video;
    }

    public function mapObjectToMongoParams(Video $video): array
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

        return $params;
    }
}
