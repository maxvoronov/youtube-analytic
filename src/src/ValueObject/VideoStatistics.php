<?php declare(strict_types=1);

namespace App\ValueObject;

class VideoStatistics
{
    /** @var int */
    protected $likeCount;

    /** @var int */
    protected $dislikeCount;

    /** @var int */
    protected $commentCount;

    /** @var int */
    protected $favoriteCount;

    public function __construct(
        int $likeCount = 0,
        int $dislikeCount = 0,
        int $commentCount = 0,
        int $favoriteCount = 0
    ) {
        $this->likeCount = $likeCount;
        $this->dislikeCount = $dislikeCount;
        $this->commentCount = $commentCount;
        $this->favoriteCount = $favoriteCount;
    }

    /**
     * @return int
     */
    public function getLikeCount(): int
    {
        return $this->likeCount;
    }

    /**
     * @return int
     */
    public function getDislikeCount(): int
    {
        return $this->dislikeCount;
    }

    /**
     * @return int
     */
    public function getCommentCount(): int
    {
        return $this->commentCount;
    }

    /**
     * @return int
     */
    public function getFavoriteCount(): int
    {
        return $this->favoriteCount;
    }
}
