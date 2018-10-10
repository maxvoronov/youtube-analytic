<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Channel;
use App\Entity\ChannelStatistics;
use App\Entity\Thumbnail;
use App\Entity\Video;
use App\Entity\VideoStatistics;
use Google_Client;
use Google_Service_YouTube;

class YoutubeApiRepository implements YoutubeRepositoryInterface
{
    protected $youtubeApi;

    public function __construct(Google_Client $apiClient)
    {
        $this->youtubeApi = new Google_Service_YouTube($apiClient);
    }

    public function findChannelById(string $id): Channel
    {
        // TODO: Implement findChannelById() method.
    }


    public function findChannelsByUser(string $username): array
    {
        $channels = [];
        $result = $this->youtubeApi->channels->listChannels(
            'id,snippet,statistics',
            ['forUsername' => $username]
        );

        foreach ($result->items as $item) {
            $channelStats = new ChannelStatistics(
                (int)$item->statistics->viewCount,
                (int)$item->statistics->videoCount,
                (int)$item->statistics->subscriberCount
            );

            $channel = new Channel(
                $item->id,
                $item->snippet->title,
                $item->snippet->description,
                $item->snippet->customUrl,
                new \DateTime($item->snippet->publishedAt),
                $channelStats
            );
            foreach ($item->snippet->thumbnails as $type => $thumbnailItem) {
                $channel->addThumbnail(new Thumbnail(
                    $type,
                    $thumbnailItem->url,
                    $thumbnailItem->width,
                    $thumbnailItem->height
                ));
            }

            $channels[$item->id] = $channel;
        }

        return $channels;
    }

    public function findVideosByChannel(Channel $channel, int $limit = 0): array
    {
        $videos = [];
        $params = [
            'channelId' => $channel->getChannelId(),
            'type' => 'video',
            'order' => 'date',
            'maxResults' => 50
        ];

        // If limit don't passed, then load only ones time
        if ($limit <= 0 || $limit > 50) {
            $limit = 50;
        }

        $totalProcessed = 0;
        do {
            $result = $this->youtubeApi->search->listSearch('id,snippet', $params);

            foreach ($result->items as $item) {
                if ($totalProcessed >= $limit) {
                    break 2;
                }

                $video = new Video(
                    $channel->getChannelId(),
                    $item->id->videoId,
                    $item->snippet->title,
                    $item->snippet->description,
                    new \DateTime($item->snippet->publishedAt)
                );

                foreach ($item->snippet->thumbnails as $type => $thumbnailItem) {
                    $video->addThumbnail(new Thumbnail(
                        $type,
                        $thumbnailItem->url,
                        $thumbnailItem->width,
                        $thumbnailItem->height
                    ));
                }

                $videos[$item->id->videoId] = $video;
                $params['pageToken'] = $result->nextPageToken;
                $totalProcessed++;
            }
        } while($totalProcessed < $limit);

        return $this->loadStatisticForVideo($videos);
    }

    protected function loadStatisticForVideo(array $videos): array
    {
        $ids = array_keys($videos);
        $result = $this->youtubeApi->videos->listVideos('statistics', ['id' => implode(',', $ids)]);
        foreach ($result->items as $item) {
            $stats = new VideoStatistics(
                (int)$item->statistics->likeCount,
                (int)$item->statistics->dislikeCount,
                (int)$item->statistics->commentCount,
                (int)$item->statistics->favoriteCount
            );
            $videos[$item->id]->setStatistics($stats);
        }

        return $videos;
    }
}
