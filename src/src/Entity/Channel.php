<?php declare(strict_types=1);

namespace App\Entity;

class Channel extends AggregateRoot
{
    protected $channelId;
    protected $title;
    protected $description;
    protected $urlSlug;
    protected $publishedAt;
    protected $thumbnails;
    protected $statistics;

    public function __construct(
        string $channelId,
        string $title,
        string $description,
        string $urlSlug,
        \DateTime $publishedAt,
        ?ChannelStatistics $statistics = null
    ) {
        $this->channelId = $channelId;
        $this->title = $title;
        $this->description = $description;
        $this->urlSlug = $urlSlug;
        $this->publishedAt = $publishedAt;
        $this->thumbnails = [];
        $this->statistics = $statistics ?? new ChannelStatistics;
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
     * @return string
     */
    public function getUrlSlug(): string
    {
        return $this->urlSlug;
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
     * @return ChannelStatistics
     */
    public function getStatistics(): ChannelStatistics
    {
        return $this->statistics;
    }
}
