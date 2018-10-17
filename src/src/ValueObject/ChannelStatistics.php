<?php declare(strict_types=1);

namespace App\ValueObject;

class ChannelStatistics
{
    /** @var int */
    protected $viewCount;

    /** @var int */
    protected $videoCount;

    /** @var int */
    protected $subscriberCount;

    public function __construct(int $viewCount = 0, int $videoCount = 0, int $subscriberCount = 0)
    {
        $this->viewCount = $viewCount;
        $this->videoCount = $videoCount;
        $this->subscriberCount = $subscriberCount;
    }

    /**
     * @return int
     */
    public function getViewCount(): int
    {
        return $this->viewCount;
    }

    /**
     * @return int
     */
    public function getVideoCount(): int
    {
        return $this->videoCount;
    }

    /**
     * @return int
     */
    public function getSubscriberCount(): int
    {
        return $this->subscriberCount;
    }
}
