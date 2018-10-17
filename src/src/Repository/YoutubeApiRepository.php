<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Channel;
use App\Entity\Video;
use App\ValueObject\ChannelStatistics;
use App\ValueObject\Thumbnail;
use App\ValueObject\VideoStatistics;
use Google_Client;
use Google_Service_YouTube;

class YoutubeApiRepository implements YoutubeRepositoryInterface
{
    /** @var Google_Service_YouTube */
    protected $youtubeApi;

    public function __construct(Google_Client $apiClient)
    {
        $this->youtubeApi = new Google_Service_YouTube($apiClient);
    }

    /**
     * Try to find channel by ID via API
     *
     * @param string $id
     * @return Channel
     */
    public function findChannelById(string $id): Channel
    {
        $result = $this->youtubeApi->channels->listChannels(
            'id,snippet,statistics',
            ['id' => $id]
        );
        $channels = $this->loadChannelsByApiResult($result);

        return array_shift($channels);
    }

    /**
     * Try to find channels by username (owner) via API
     *
     * @param string $username
     * @return array
     */
    public function findChannelsByUser(string $username): array
    {
        $result = $this->youtubeApi->channels->listChannels(
            'id,snippet,statistics',
            ['forUsername' => $username]
        );

        return $this->loadChannelsByApiResult($result);
    }

    /**
     * Load videos for channel
     *
     * @param Channel $channel
     * @param int $limit
     * @return array
     */
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

                $video = $this->loadVideosByApiResult($channel, $item);
                $videos[$item->id->videoId] = $video;
                $params['pageToken'] = $result->nextPageToken;
                $totalProcessed++;
            }
        } while ($totalProcessed < $limit);

        return $this->loadStatisticForVideos($videos);
    }

    /**
     * Convert API response to array of channel objects
     *
     * @param $result
     * @return array
     */
    protected function loadChannelsByApiResult($result): array
    {
        $channels = [];
        foreach ($result->items as $item) {
            $channelStats = new ChannelStatistics(
                (int)$item->statistics->viewCount,
                (int)$item->statistics->videoCount,
                (int)$item->statistics->subscriberCount
            );

            $channel = new Channel(
                (string)$item->id,
                (string)$item->snippet->title,
                (string)$item->snippet->description,
                (string)$item->snippet->customUrl,
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

    /**
     * Convert API response to array of video object
     *
     * @param Channel $channel
     * @param $result
     * @return Video
     */
    protected function loadVideosByApiResult(Channel $channel, $result): Video
    {
        $video = new Video(
            $channel->getChannelId(),
            (string)$result->id->videoId,
            (string)$result->snippet->title,
            (string)$result->snippet->description,
            new \DateTime($result->snippet->publishedAt)
        );

        foreach ($result->snippet->thumbnails as $type => $thumbnailItem) {
            $video->addThumbnail(new Thumbnail(
                $type,
                $thumbnailItem->url,
                $thumbnailItem->width,
                $thumbnailItem->height
            ));
        }

        return $video;
    }

    /**
     * Additional query for loading statistics of videos
     *
     * @param array $videos
     * @return array
     */
    protected function loadStatisticForVideos(array $videos): array
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
