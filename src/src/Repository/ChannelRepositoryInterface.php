<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Channel;

interface ChannelRepositoryInterface
{
    public function get(string $id): Channel;

    public function add(Channel $channel): void;

    public function save(Channel $channel, bool $upsert = true): void;

    public function remove(Channel $channel): void;

    public function findByChannelId(string $channelId): Channel;
}
