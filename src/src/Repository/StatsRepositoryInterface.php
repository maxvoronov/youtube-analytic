<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Channel;
use App\ValueObject\ChannelVotes;

interface StatsRepositoryInterface
{
    public function getVotesByChannelId(Channel $channel): ChannelVotes;

    public function findTopChannelsByRate(int $limit): array;
}
