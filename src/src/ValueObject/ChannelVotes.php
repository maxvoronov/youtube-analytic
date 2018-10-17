<?php declare(strict_types=1);

namespace App\ValueObject;

use App\Entity\Channel;

class ChannelVotes
{
    /** @var Channel */
    public $channel;

    /** @var int */
    public $likes;

    /** @var int */
    public $dislikes;

    public function __construct(Channel $channel, int $likes = 0, int $dislikes = 0)
    {
        $this->channel = $channel;
        $this->likes = $likes;
        $this->dislikes = $dislikes;
    }

    /**
     * @return Channel
     */
    public function getChannel(): Channel
    {
        return $this->channel;
    }

    /**
     * @return int
     */
    public function getLikes(): int
    {
        return $this->likes;
    }

    /**
     * @return int
     */
    public function getDislikes(): int
    {
        return $this->dislikes;
    }
}
