<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Channel;
use App\Entity\ChannelStatistics;
use App\Entity\Thumbnail;
use App\Entity\Video;
use App\Entity\VideoStatistics;
use App\Repository\ChannelRepositoryInterface;
use App\Repository\VideoRepositoryInterface;
use App\Repository\YoutubeRepositoryInterface;
use MongoDB\Database;

class YoutubeService
{
    protected $youtubeRepository;
    protected $channelRepository;
    protected $videoRepository;
    protected $mongoDb;

    public function __construct(
        YoutubeRepositoryInterface $youtubeRepository,
        ChannelRepositoryInterface $channelRepository,
        VideoRepositoryInterface $videoRepository,
        Database $mongoDb
    ) {
        $this->youtubeRepository = $youtubeRepository;
        $this->channelRepository = $channelRepository;
        $this->videoRepository = $videoRepository;
        $this->mongoDb = $mongoDb;
    }

    /**
     * Add to database channels by username
     *
     * @param string $username
     * @return Channel[]
     */
    public function addChannelsByUsername(string $username): array
    {
        $channels = $this->youtubeRepository->findChannelsByUser($username);
        foreach ($channels as $channel) {
            // ToDo: Unique channels?
            $this->channelRepository->add($channel);
        }

        return $channels;
    }

    public function addVideosByChannel(Channel $channel, int $limit = 50): array
    {
        $videos = $this->youtubeRepository->findVideosByChannel($channel, $limit);
        foreach ($videos as $video) {
            $this->videoRepository->add($video);
        }

        return $videos;
    }

    public function addChannel()
    {
//        $channels = $this->youtubeRepository->findChannelsByUser('GoogleDevelopers');
//        foreach ($channels as $channel) {
//            $this->channelRepository->add($channel);
//        }

//        $channel = $this->channelRepository->get('5bbb5304910def0006442fe3');

//        $channel = new Channel(
//            'qwe123qwe',
//            'Qwerty',
//            'ASD qwe zxc123',
//            'qwe-ewq',
//            new \DateTime,
//            new ChannelStatistics(128, 256, 512)
//        );

//        $channel->addThumbnail(new Thumbnail(
//            'default',
//            'https://docs.mongodb.com/images/mongodb-logo.svg',
//            200,
//            87
//        ));
//
//        $this->channelRepository->save($channel);

//        $video = new Video(
//            '5bbb5304910def0006442fe3',
//            'vd12345',
//            'Demo Video',
//            'Demo video description',
//            new \DateTime()
//        );
//        $video = $this->videoRepository->get('5bbb59f2910def000759bc74');

//        $video->addThumbnail(new Thumbnail(
//            'demo',
//            'https://docs.mongodb.com/images/mongodb-logo.svg',
//            250,
//            150
//        ));
//        $video->setStatistics(new VideoStatistics(350, 229, 52, 101));

//        $this->videoRepository->add($video);
//        $this->videoRepository->save($video);
//        $this->videoRepository->remove($video);

//        echo "Идентификатор вставленного документа '{$video->getId()}'";


        exit;
        //echo '<pre>' . print_r($channel, true) . '</pre>'; exit;

//        $collection = $this->mongoDb->test;
//        $result = $collection->insertOne([
//            'login' => 'remm3',
//            'email' => 'v0id@list.ru',
//            'active' => true,
//            'registered_at' => new \DateTime
//        ]);
//
//        echo "Идентификатор вставленного документа '{$result->getInsertedId()}'"; exit;

        /*$userList = ['GoogleDevelopers'];
        $channels = $this->youtubeRepository->findChannelsByUser('GoogleDevelopers');
        foreach ($channels as $channel) {
            $videos = $this->youtubeRepository->findVideosByChannel($channel, 10);
        }

        echo '<h2>Channels:</h2><pre>' . print_r($channels, true) . '</pre>';
        echo '<h2>Videos:</h2><pre>' . print_r($videos, true) . '</pre>';*/
    }
}
