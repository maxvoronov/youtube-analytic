<?php declare(strict_types=1);

namespace App\Entity;

use App\ValueObject\Thumbnail;
use App\ValueObject\VideoStatistics;

class Video extends AggregateRoot
{
    protected $channelId;
    protected $videoId;
    protected $title;
    protected $description;
    protected $publishedAt;
    protected $thumbnails;
    protected $statistics;

    public function __construct(
        string $channelId,
        string $videoId,
        string $title,
        string $description,
        \DateTime $publishedAt
    ) {
        $this->channelId = $channelId;
        $this->videoId = $videoId;
        $this->title = $title;
        $this->description = $description;
        $this->publishedAt = $publishedAt;
        $this->thumbnails = [];
        $this->statistics = new VideoStatistics;
    }

    public function addThumbnail(Thumbnail $thumbnail): self
    {
        $this->thumbnails[$thumbnail->getType()] = $thumbnail;

        return $this;
    }

    public function removeThumbnail(Thumbnail $thumbnail): self
    {
        if (array_key_exists($thumbnail->getType(), $this->thumbnails)) {
            unset($this->thumbnails[$thumbnail->getType()]);
        }

        return $this;
    }

    public function setStatistics(VideoStatistics $statistics): self
    {
        $this->statistics = $statistics;

        return $this;
    }

    /**
     * @return string
     */
    public function getChannelId(): string
    {
        return $this->channelId;
    }

    /**
     * @return string
     */
    public function getVideoId(): string
    {
        return $this->videoId;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt(): \DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @return array
     */
    public function getThumbnails(): array
    {
        return $this->thumbnails;
    }

    /**
     * @return VideoStatistics
     */
    public function getStatistics(): VideoStatistics
    {
        return $this->statistics;
    }
}
