<?php declare(strict_types=1);

namespace App\ValueObject;

use App\Entity\Channel;

class ChannelRate
{
    /** @var Channel */
    public $channel;

    /** @var float */
    public $rate;

    public function __construct(Channel $channel, float $rate = 0.0)
    {
        $this->channel = $channel;
        $this->rate = $rate;
    }

    /**
     * @return Channel
     */
    public function getChannel(): Channel
    {
        return $this->channel;
    }

    /**
     * @return float
     */
    public function getRate(): float
    {
        return $this->rate;
    }
}
