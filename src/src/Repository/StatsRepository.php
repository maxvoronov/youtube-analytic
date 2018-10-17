<?php declare(strict_types=1);

namespace App\Repository;

use App\Mapping\ChannelMapping;
use App\ValueObject\ChannelRate;
use App\ValueObject\ChannelVotes;
use App\Entity\Channel;
use MongoDB\Database;

class StatsRepository implements StatsRepositoryInterface
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
     * Return total likes and dislikes for all videos by channel
     *
     * @param Channel $channel
     * @return ChannelVotes
     */
    public function getVotesByChannelId(Channel $channel): ChannelVotes
    {
        $result = $this->mongoDb->videos->aggregate([
            ['$match' => ['channel_id' => $channel->getChannelId()]],
            [
                '$group' => [
                    '_id' => '$channel_id',
                    'likes' => ['$sum' => '$statistics.like_count'],
                    'dislikes' => ['$sum' => '$statistics.dislike_count'],
                ],
            ]
        ]);

        foreach ($result as $item) {
            $channelVotes = new ChannelVotes($channel, (int)$item['likes'], $item['dislikes']);
        }

        return $channelVotes;
    }

    /**
     * Return TOP channels by rate (likes / dislikes)
     *
     * @param int $limit
     * @return array
     */
    public function findTopChannelsByRate(int $limit): array
    {
        $result = $this->mongoDb->videos->aggregate([
            ['$group' => [
                '_id' => '$channel_id',
                'likes' => ['$sum' => '$statistics.like_count'],
                'dislikes' => ['$sum' => '$statistics.dislike_count'],
            ]],
            ['$project' => ['rate' => ['$divide' => ['$likes', '$dislikes']]]],
            ['$sort' => ['rate' => -1]],
            ['$limit' => $limit],
            ['$lookup' => [
                'from' => 'channels',
                'localField' => '_id',
                'foreignField' => 'channel_id',
                'as' => 'channelSource'
            ]],
            ['$replaceRoot' => [
                'newRoot' => [
                    '$mergeObjects' => ['$$ROOT', ['$arrayElemAt' => ['$channelSource', 0]]]
                ]
            ]],
            ['$project' => ['channelSource' => 0]]
        ]);

        $rates = [];
        foreach ($result as $item) {
            $channel = $this->channelMapping->mapMongoResultToObject($item);
            $channel->setId((string)$item['_id']);

            $rates[] = new ChannelRate($channel, (float)$item['rate']);
        }

        return $rates;
    }
}
