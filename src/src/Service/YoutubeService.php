<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Channel;
use App\ValueObject\ChannelVotes;
use App\Repository\ChannelRepositoryInterface;
use App\Repository\StatsRepositoryInterface;
use App\Repository\VideoRepositoryInterface;
use App\Repository\YoutubeRepositoryInterface;
use MongoDB\Database;

class YoutubeService
{
    /** @var YoutubeRepositoryInterface */
    protected $youtubeRepository;

    /** @var ChannelRepositoryInterface */
    protected $channelRepository;

    /** @var VideoRepositoryInterface */
    protected $videoRepository;

    /** @var StatsRepositoryInterface */
    protected $statsRepository;

    /** @var Database */
    protected $mongoDb;

    public function __construct(
        YoutubeRepositoryInterface $youtubeRepository,
        ChannelRepositoryInterface $channelRepository,
        VideoRepositoryInterface $videoRepository,
        StatsRepositoryInterface $statsRepository,
        Database $mongoDb
    ) {
        $this->youtubeRepository = $youtubeRepository;
        $this->channelRepository = $channelRepository;
        $this->videoRepository = $videoRepository;
        $this->statsRepository = $statsRepository;
        $this->mongoDb = $mongoDb;
    }

    /**
     * Load and add to database channel by ID
     *
     * @param string $id
     * @return Channel
     */
    public function addChannelById(string $id): Channel
    {
        $channel = $this->youtubeRepository->findChannelById($id);
        $this->channelRepository->add($channel);

        return $channel;
    }

    /**
     * Load and add to database channels by username
     *
     * @param string $username
     * @return Channel[]
     */
    public function addChannelsByUsername(string $username): array
    {
        $newChannels = [];
        $channels = $this->youtubeRepository->findChannelsByUser($username);

        /** @var Channel $channel */
        foreach ($channels as $channel) {
            // Try to find channel. Add new only if channel doesn't exist
            try {
                $this->getChannelById($channel->getId());
            } catch (\Exception $exception) {
                $this->channelRepository->add($channel);
                $newChannels[] = $channel;
            }
        }

        return $newChannels;
    }

    /**
     * Load videos of channel and add to database
     *
     * @param Channel $channel
     * @param int $limit
     * @return array
     */
    public function addVideosByChannel(Channel $channel, int $limit = 50): array
    {
        $videos = $this->youtubeRepository->findVideosByChannel($channel, $limit);
        foreach ($videos as $video) {
            $this->videoRepository->add($video);
        }

        return $videos;
    }

    /**
     * Load channel by ID
     *
     * @param string $channelId
     * @return Channel
     */
    public function getChannelById(string $channelId): Channel
    {
        return $this->channelRepository->findByChannelId($channelId);
    }

    /**
     * Return top channels by rate (likes / dislikes)
     *
     * @param int $limit
     * @return array
     */
    public function getTopChannels(int $limit = 10): array
    {
        return $this->statsRepository->findTopChannelsByRate($limit);
    }

    /**
     * Return channel total likes and dislike for all videos
     *
     * @param Channel $channel
     * @return ChannelVotes
     */
    public function getChannelVotes(Channel $channel): ChannelVotes
    {
        return $this->statsRepository->getVotesByChannelId($channel);
    }
}
