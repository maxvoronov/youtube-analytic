<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Channel;

interface YoutubeRepositoryInterface
{
    public function findChannelById(string $id): Channel;

    public function findChannelsByUser(string $username): array;

    public function findVideosByChannel(Channel $channel, int $limit = 0): array;
}
