<?php declare(strict_types=1);

namespace App\Mapping;

use App\Entity\Channel;
use App\ValueObject\ChannelStatistics;
use App\ValueObject\Thumbnail;
use MongoDB\BSON\UTCDateTime;

class ChannelMapping
{
    public function mapMongoResultToObject($result): Channel
    {
        $channelStats = new ChannelStatistics(
            $result['statistics']['view_count'] ?? 0,
            $result['statistics']['video_count'] ?? 0,
            $result['statistics']['subscriber_count'] ?? 0
        );

        $channel = new Channel(
            $result['channel_id'] ?? '',
            $result['title'] ?? '',
            $result['description'] ?? '',
            $result['url_slug'] ?? '',
            $result['published_at']->toDateTime(),
            $channelStats
        );
        foreach ($result['thumbnails'] as $type => $thumbnail) {
            $channel->addThumbnail(new Thumbnail(
                $type,
                $thumbnail['url'] ?? '',
                $thumbnail['width'] ?? 0,
                $thumbnail['height'] ?? 0
            ));
        }

        return $channel;
    }

    public function mapObjectToMongoParams(Channel $channel): array
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

        return $params;
    }
}
